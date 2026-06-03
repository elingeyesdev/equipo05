<?php

namespace Modules\Incendios\Http\Controllers\Api;

use Modules\Incendios\Http\Controllers\Controller;
use Modules\Incendios\Models\FocoIncendio;
use Modules\Incendios\Services\FirmsDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PublicFirmsController extends Controller
{
    protected FirmsDataService $firmsService;

    public function __construct(FirmsDataService $firmsService)
    {
        $this->firmsService = $firmsService;
    }

    /**
     * Get active fire hotspots from database (updated every 5 min by scheduler)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveFires(Request $request)
    {
        // Cache key for this endpoint
        $cacheKey = 'public_active_fires_list';
        $cacheDuration = 5; // minutes

        $fires = Cache::remember($cacheKey, now()->addMinutes($cacheDuration), function () use ($request) {
            // Get fires from last 24 hours by default
            $hours = $request->query('hours', 24);
            $hours = min(max($hours, 1), 168); // Between 1 hour and 7 days

            $since = Carbon::now()->subHours($hours);

            return FocoIncendio::where('fecha', '>=', $since)
                ->orderBy('fecha', 'desc')
                ->orderBy('intensidad', 'desc')
                ->select(['id', 'fecha', 'ubicacion', 'coordenadas', 'intensidad', 'created_at'])
                ->get()
                ->map(function ($foco) {
                    return [
                        'id' => $foco->id,
                        'fecha' => $foco->fecha->toIso8601String(),
                        'fecha_humana' => $foco->fecha->diffForHumans(),
                        'ubicacion' => $foco->ubicacion,
                        'lat' => $foco->latitude,
                        'lng' => $foco->longitude,
                        'intensidad' => $foco->intensidad,
                        'nivel' => $this->getIntensityLevel($foco->intensidad),
                    ];
                })
                ->filter(fn ($f) => $f['lat'] !== null && $f['lng'] !== null)
                ->values();
        });

        return response()->json([
            'success' => true,
            'count' => $fires->count(),
            'timestamp' => now()->toIso8601String(),
            'data' => $fires,
            'metadata' => [
                'cache_duration_minutes' => $cacheDuration,
                'update_frequency' => 'every 5 minutes',
                'data_source' => 'NASA FIRMS',
            ],
        ]);
    }

    /**
     * Get fire statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $cacheKey = 'public_fire_statistics';
        
        $stats = Cache::remember($cacheKey, now()->addMinutes(10), function () {
            $now = Carbon::now();

            return [
                'last_hour' => FocoIncendio::where('fecha', '>=', $now->copy()->subHour())->count(),
                'last_6_hours' => FocoIncendio::where('fecha', '>=', $now->copy()->subHours(6))->count(),
                'last_24_hours' => FocoIncendio::where('fecha', '>=', $now->copy()->subDay())->count(),
                'last_7_days' => FocoIncendio::where('fecha', '>=', $now->copy()->subDays(7))->count(),
                'total_recorded' => FocoIncendio::count(),
                'highest_intensity_today' => FocoIncendio::where('fecha', '>=', $now->copy()->startOfDay())
                    ->max('intensidad') ?? 0,
                'average_intensity_today' => round(
                    FocoIncendio::where('fecha', '>=', $now->copy()->startOfDay())
                        ->avg('intensidad') ?? 0,
                    2
                ),
                'last_update' => FocoIncendio::max('created_at'),
            ];
        });

        return response()->json([
            'success' => true,
            'timestamp' => now()->toIso8601String(),
            'statistics' => $stats,
        ]);
    }

    /**
     * Get latest fires (real-time from FIRMS API, not DB)
     * Use this sparingly as it hits the external API
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestFromFirms(Request $request)
    {
        $product = $request->query('product', 'VIIRS_NOAA20_NRT');
        $area = $request->query('area', '-62.5,-18.5,-57.5,-14.5');
        $days = (int) $request->query('days', 1);
        $cluster = $request->query('cluster', 'true') === 'true';

        $result = $this->firmsService->getFireData(
            product: $product,
            area: $area,
            days: $days,
            cluster: $cluster
        );

        if (!$result['ok']) {
            return response()->json([
                'success' => false,
                'error' => 'No se pudo obtener la información de focos en este momento.',
            ], $result['status'] ?? 500);
        }

        return response()->json([
            'success' => true,
            'count' => $result['count'] ?? 0,
            'cached' => $result['cached'] ?? false,
            'timestamp' => now()->toIso8601String(),
            'data' => $result['data'],
            'metadata' => [
                'product' => $product,
                'area' => $area,
                'days' => $days,
                'clustered' => $cluster,
                'data_source' => 'NASA FIRMS (Direct)',
            ],
        ]);
    }

    /**
     * Get GeoJSON format for mapping applications
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGeoJson(Request $request)
    {
        $hours = $request->query('hours', 24);
        $hours = min(max($hours, 1), 168);

        $cacheKey = "public_fires_geojson_{$hours}h";

        $geojson = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($hours) {
            $since = Carbon::now()->subHours($hours);

            $fires = FocoIncendio::where('fecha', '>=', $since)
                ->orderBy('fecha', 'desc')
                ->get();

            $features = $fires->map(function ($foco) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            $foco->coordenadas['lng'] ?? 0,
                            $foco->coordenadas['lat'] ?? 0,
                        ],
                    ],
                    'properties' => [
                        'id' => $foco->id,
                        'fecha' => $foco->fecha->toIso8601String(),
                        'ubicacion' => $foco->ubicacion,
                        'intensidad' => $foco->intensidad,
                        'nivel' => $this->getIntensityLevel($foco->intensidad),
                        'edad_horas' => $foco->fecha->diffInHours(now()),
                    ],
                ];
            });

            return [
                'type' => 'FeatureCollection',
                'features' => $features,
                'metadata' => [
                    'generated_at' => now()->toIso8601String(),
                    'count' => $features->count(),
                    'time_range_hours' => $hours,
                ],
            ];
        });

        return response()->json($geojson);
    }

    /**
     * Health check endpoint
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function healthCheck()
    {
        $lastUpdate = FocoIncendio::max('created_at');
        $isHealthy = $lastUpdate && Carbon::parse($lastUpdate)->diffInMinutes(now()) < 15;

        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'warning',
            'service' => 'FIRMS Fire Data API',
            'last_update' => $lastUpdate,
            'minutes_since_update' => $lastUpdate ? Carbon::parse($lastUpdate)->diffInMinutes(now()) : null,
            'total_fires_in_db' => FocoIncendio::count(),
            'scheduler_status' => $isHealthy ? 'running' : 'may need attention',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Determine intensity level based on FRP or confidence value
     * 
     * @param float $intensity
     * @return string
     */
    protected function getIntensityLevel(float $intensity): string
    {
        if ($intensity >= 100) return 'muy_alto';
        if ($intensity >= 50) return 'alto';
        if ($intensity >= 20) return 'medio';
        return 'bajo';
    }
}
