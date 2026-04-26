<?php

namespace Modules\Incendios\Http\Controllers\Api;

use Modules\Incendios\Http\Controllers\Controller;
use Modules\Incendios\Models\Prediction;
use Modules\Incendios\Models\FocosIncendio;
use Modules\Incendios\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PublicPredictionsController extends Controller
{
    /**
     * Get all active predictions (public endpoint)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $cacheKey = 'public_predictions_list';
        $cacheDuration = 5; // minutes

        $predictions = Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () use ($request) {
            // Get predictions from last N days
            $days = $request->query('days', 7);
            $days = min(max($days, 1), 30); // Between 1 and 30 days

            $since = Carbon::now()->subDays($days);

            return Prediction::with('focoIncendio')
                ->where('predicted_at', '>=', $since)
                ->orderBy('predicted_at', 'desc')
                ->get()
                ->map(function ($prediction) {
                    return $this->formatPrediction($prediction);
                });
        });

        return response()->json([
            'success' => true,
            'count' => $predictions->count(),
            'timestamp' => now()->toIso8601String(),
            'data' => $predictions,
            'metadata' => [
                'cache_duration_minutes' => $cacheDuration,
                'update_frequency' => 'real-time',
                'data_source' => 'SIPII Prediction System',
            ],
        ]);
    }

    /**
     * Get latest predictions (most recent N predictions)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest(Request $request)
    {
        $limit = $request->query('limit', 10);
        $limit = min(max($limit, 1), 100); // Between 1 and 100

        $predictions = Prediction::with('focoIncendio')
            ->orderBy('predicted_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($prediction) {
                return $this->formatPrediction($prediction);
            });

        return response()->json([
            'success' => true,
            'count' => $predictions->count(),
            'timestamp' => now()->toIso8601String(),
            'data' => $predictions,
        ]);
    }

    /**
     * Get prediction by ID (public)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $prediction = Prediction::with('focoIncendio')->find($id);

        if (!$prediction) {
            return response()->json([
                'success' => false,
                'error' => 'Prediction not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'data' => $this->formatPrediction($prediction, true), // Detailed view
        ]);
    }

    /**
     * Get predictions for a specific fire hotspot
     * 
     * @param int $focoId
     * @return \Illuminate\Http\JsonResponse
     */
    public function byFoco($focoId)
    {
        $predictions = Prediction::with('focoIncendio')
            ->where('foco_incendio_id', $focoId)
            ->orderBy('predicted_at', 'desc')
            ->get()
            ->map(function ($prediction) {
                return $this->formatPrediction($prediction);
            });

        if ($predictions->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No predictions found for this fire hotspot',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'count' => $predictions->count(),
            'foco_id' => (int) $focoId,
            'timestamp' => now()->toIso8601String(),
            'data' => $predictions,
        ]);
    }

    /**
     * Get GeoJSON format of predictions (for mapping)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function geojson(Request $request)
    {
        $days = $request->query('days', 7);
        $days = min(max($days, 1), 30);

        $cacheKey = "public_predictions_geojson_{$days}d";

        $geojson = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($days) {
            $since = Carbon::now()->subDays($days);

            $predictions = Prediction::with('focoIncendio')
                ->where('predicted_at', '>=', $since)
                ->get();

            $features = $predictions->map(function ($prediction) {
                $path = is_string($prediction->path) 
                    ? json_decode($prediction->path, true) 
                    : $prediction->path;

                // Create LineString from prediction path
                $coordinates = [];
                if (is_array($path)) {
                    foreach ($path as $point) {
                        if (isset($point['lat'], $point['lng'])) {
                            $coordinates[] = [$point['lng'], $point['lat']];
                        }
                    }
                }

                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => $coordinates,
                    ],
                    'properties' => [
                        'id' => $prediction->id,
                        'predicted_at' => $prediction->predicted_at->toIso8601String(),
                        'foco_id' => $prediction->foco_incendio_id,
                        'foco_location' => $prediction->focoIncendio 
                            ? $prediction->focoIncendio->ubicacion 
                            : null,
                        'path_points_count' => count($coordinates),
                        'meta' => $prediction->meta,
                    ],
                ];
            });

            return [
                'type' => 'FeatureCollection',
                'features' => $features,
                'metadata' => [
                    'generated_at' => now()->toIso8601String(),
                    'count' => $features->count(),
                    'time_range_days' => $days,
                ],
            ];
        });

        return response()->json($geojson);
    }

    /**
     * Lookup nearest prediction by latitude/longitude.
     * Returns the nearest prediction (detailed) and distance in km.
     * If no prediction is within the provided radius, returns the nearest anyway with `matched=false`.
     *
     * Query params: `lat` (required), `lng` (required), `radius_km` (optional, default 5), `days` (optional, default 7)
     */
    public function lookup(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius_km' => 'nullable|numeric',
            'days' => 'nullable|integer',
            'hours' => 'nullable|integer', // duration in hours for generated path
        ]);

        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');
        $radius = (float) $request->query('radius_km', 5);
        $days = min(max((int) $request->query('days', 7), 1), 30);
        $hours = min(max((int) $request->query('hours', 24), 1), 168); // default 24h, max 7 days

        $since = Carbon::now()->subDays($days);

        $predictions = Prediction::with('focoIncendio')
            ->where('predicted_at', '>=', $since)
            ->get();

        $best = null;
        $bestDist = PHP_FLOAT_MAX;

        foreach ($predictions as $prediction) {
            // Compute distance to foco coords (if available)
            $foco = $prediction->focoIncendio;
            if ($foco && is_array($foco->coordenadas)) {
                $fLat = $foco->coordenadas['lat'] ?? null;
                $fLng = $foco->coordenadas['lng'] ?? null;
                if ($fLat !== null && $fLng !== null) {
                    $d = $this->haversineDistance($lat, $lng, (float) $fLat, (float) $fLng);
                    if ($d < $bestDist) {
                        $bestDist = $d;
                        $best = $prediction;
                    }
                }
            }

            // Compute distance to each point in path (if available)
            $path = is_string($prediction->path) ? json_decode($prediction->path, true) : $prediction->path;
            if (is_array($path) && count($path) > 0) {
                foreach ($path as $pt) {
                    if (isset($pt['lat'], $pt['lng'])) {
                        $d = $this->haversineDistance($lat, $lng, (float) $pt['lat'], (float) $pt['lng']);
                        if ($d < $bestDist) {
                            $bestDist = $d;
                            $best = $prediction;
                        }
                    }
                }
            }
        }

        $matched = $bestDist <= $radius;

        // If matched within radius, return nearest existing prediction
        if ($matched && $best) {
            $nearest = $this->formatPrediction($best, true);
            $nearest['distance_km'] = round($bestDist, 3);

            return response()->json([
                'success' => true,
                'timestamp' => now()->toIso8601String(),
                'data' => $nearest,
            ]);
        }

        // No prediction within radius: create a new prediction record
        // Try to associate nearest foco within the radius (or null)
        $nearestFoco = null;
        $focos = FocosIncendio::all();
        $bestFocoDist = PHP_FLOAT_MAX;
        foreach ($focos as $foco) {
            if (is_array($foco->coordenadas) && isset($foco->coordenadas['lat'], $foco->coordenadas['lng'])) {
                $d = $this->haversineDistance($lat, $lng, (float) $foco->coordenadas['lat'], (float) $foco->coordenadas['lng']);
                if ($d < $bestFocoDist) {
                    $bestFocoDist = $d;
                    $nearestFoco = $foco;
                }
            }
        }

        $associateFocoId = null;
        if ($nearestFoco && $bestFocoDist <= $radius) {
            $associateFocoId = $nearestFoco->id;
        }

        // Obtain weather for the exact coordinates and compute prediction following SIMULADOR logic
        $weatherService = new WeatherService();
        $weatherResp = $weatherService->currentByCoords($lat, $lng);

        $nowIso = now()->toIso8601String();

        $weather = null;
        if (is_array($weatherResp) && ($weatherResp['ok'] ?? false)) {
            $weather = $weatherResp['data'];
        }

        // Default environmental values if weather not available
        $temperature = $weather['main']['temp'] ?? 25.0;
        $humidity = $weather['main']['humidity'] ?? 40.0;
        $windSpeedMs = $weather['wind']['speed'] ?? 2.0; // m/s
        $windSpeed = $windSpeedMs * 3.6; // km/h
        $windDir = $weather['wind']['deg'] ?? null;

        // Compute risk factors (0-100)
        $tempFactor = max(0, min(100, ($temperature / 50) * 100));
        $humidityFactor = max(0, min(100, (1 - ($humidity / 100)) * 100));
        $windFactor = max(0, min(100, ($windSpeed / 100) * 100));

        $fireRisk = ($tempFactor * 0.4) + ($humidityFactor * 0.3) + ($windFactor * 0.3);

        // Spread calculations per SIMULADOR.md
        $simulationSpeed = 1.0;
        $spreadRate = ($fireRisk / 100) * ($windSpeed / 20) * ($temperature / 30) * (1 - $humidity / 150);
        $spreadDistance = 0.01 * $spreadRate * $simulationSpeed; // degrees approximation

        // Build a multi-point path for the requested duration (hours)
        $initialIntensity = 1.0;
        $path = [];

        // Direction: windDir ± 30°, random if missing
        $baseDir = $windDir ?? rand(0, 359);
        $randOffset = rand(-30, 30);
        $dirDeg = ($baseDir + $randOffset) % 360;
        $dirRad = deg2rad($dirDeg);

        // We'll step hourly (1 hour per step)
        $currentLat = $lat;
        $currentLng = $lng;
        $stepSeconds = 3600;

        for ($i = 0; $i <= $hours; $i++) {
            $t = now()->addHours($i)->toIso8601String();
            $intensity = round($initialIntensity * pow(0.95, $i), 4);

            // compute next offset using spreadDistance as degrees-per-hour approximation
            $variation = (rand(-10, 10) / 1000); // small randomness
            $stepDegrees = ($spreadDistance + $variation);

            // Estimate perimeter radius (meters) from spreadDistance (degrees -> meters)
            $radius_m = max(20, (int) round($stepDegrees * 111000 * (1 + (1 - $intensity))));

            $path[] = [
                'lat' => $currentLat,
                'lng' => $currentLng,
                't' => $t,
                'intensity' => $intensity,
                'perimeter_radius_m' => $radius_m,
            ];

            $dLat = $stepDegrees * cos($dirRad);
            $dLng = $stepDegrees * sin($dirRad) / max(cos(deg2rad($currentLat)), 0.00001);

            $currentLat += $dLat;
            $currentLng += $dLng;
        }

        $meta = [
            'source' => 'public_lookup_exact',
            'created_at' => $nowIso,
            'weather' => $weather,
            'fireRisk' => round($fireRisk, 2),
            'spreadRate' => $spreadRate,
            'spreadDistance_degrees' => $spreadDistance,
            'simulationSpeed' => $simulationSpeed,
            'direction_deg' => $dirDeg,
        ];

        $newPrediction = Prediction::create([
            'foco_incendio_id' => $associateFocoId,
            'predicted_at' => now(),
            'path' => $path,
            'meta' => $meta,
            'user_id' => null,
            'ci_usuario' => null,
        ]);

        $newPrediction->load('focoIncendio');

        $result = $this->formatPrediction($newPrediction, true);
        $result['distance_km'] = round($bestDist === PHP_FLOAT_MAX ? 0 : $bestDist, 3);

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'data' => $result,
        ]);
    }

    /**
     * Get statistics about predictions
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $cacheKey = 'public_predictions_statistics';

        $stats = Cache::remember($cacheKey, now()->addMinutes(10), function () {
            $now = Carbon::now();

            return [
                'total_predictions' => Prediction::count(),
                'last_24_hours' => Prediction::where('predicted_at', '>=', $now->copy()->subDay())->count(),
                'last_7_days' => Prediction::where('predicted_at', '>=', $now->copy()->subDays(7))->count(),
                'last_30_days' => Prediction::where('predicted_at', '>=', $now->copy()->subDays(30))->count(),
                'predictions_with_focos' => Prediction::whereNotNull('foco_incendio_id')->count(),
                'latest_prediction' => Prediction::latest('predicted_at')->first()?->predicted_at,
                'oldest_prediction' => Prediction::oldest('predicted_at')->first()?->predicted_at,
            ];
        });

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'statistics' => $stats,
        ]);
    }

    /**
     * Format prediction data for API response
     * 
     * @param Prediction $prediction
     * @param bool $detailed
     * @return array
     */
    protected function formatPrediction(Prediction $prediction, bool $detailed = false): array
    {
        $path = is_string($prediction->path) 
            ? json_decode($prediction->path, true) 
            : $prediction->path;

        $foco = $prediction->focoIncendio;

        $data = [
            'id' => $prediction->id,
            'predicted_at' => $prediction->predicted_at->toIso8601String(),
            'predicted_at_human' => $prediction->predicted_at->diffForHumans(),
            'path_points_count' => is_array($path) ? count($path) : 0,
            'created_at' => $prediction->created_at->toIso8601String(),
        ];

        // Add foco information
        if ($foco) {
            $data['foco'] = [
                'id' => $foco->id,
                'fecha' => $foco->fecha?->toIso8601String(),
                'ubicacion' => $foco->ubicacion,
                'lat' => $foco->coordenadas['lat'] ?? null,
                'lng' => $foco->coordenadas['lng'] ?? null,
                'intensidad' => $foco->intensidad,
            ];
        }

        // Add detailed information if requested
        if ($detailed) {
            $data['path'] = $path;
            $data['meta'] = $prediction->meta;
            
            // Calculate path statistics
            if (is_array($path) && count($path) > 0) {
                $data['path_statistics'] = $this->calculatePathStatistics($path);
            }
        } else {
            // Only summary for list view
            $data['meta_summary'] = [
                'has_meta' => !empty($prediction->meta),
                'meta_keys' => is_array($prediction->meta) ? array_keys($prediction->meta) : [],
            ];
        }

        return $data;
    }

    /**
     * Calculate statistics from prediction path
     * 
     * @param array $path
     * @return array
     */
    protected function calculatePathStatistics(array $path): array
    {
        if (empty($path)) {
            return [
                'total_points' => 0,
                'total_distance_km' => 0,
            ];
        }

        $totalDistance = 0;
        $lats = [];
        $lngs = [];

        for ($i = 0; $i < count($path) - 1; $i++) {
            $p1 = $path[$i];
            $p2 = $path[$i + 1];

            if (isset($p1['lat'], $p1['lng'], $p2['lat'], $p2['lng'])) {
                $lats[] = $p1['lat'];
                $lngs[] = $p1['lng'];
                
                // Calculate distance using Haversine formula
                $totalDistance += $this->haversineDistance(
                    $p1['lat'], $p1['lng'],
                    $p2['lat'], $p2['lng']
                );
            }
        }

        // Add last point
        $lastPoint = end($path);
        if (isset($lastPoint['lat'], $lastPoint['lng'])) {
            $lats[] = $lastPoint['lat'];
            $lngs[] = $lastPoint['lng'];
        }

        return [
            'total_points' => count($path),
            'total_distance_km' => round($totalDistance, 2),
            'bounds' => [
                'north' => !empty($lats) ? max($lats) : null,
                'south' => !empty($lats) ? min($lats) : null,
                'east' => !empty($lngs) ? max($lngs) : null,
                'west' => !empty($lngs) ? min($lngs) : null,
            ],
        ];
    }

    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in kilometers
     */
    protected function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDiff = deg2rad($lat2 - $lat1);
        $lonDiff = deg2rad($lon2 - $lon1);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDiff / 2) * sin($lonDiff / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
