<?php

namespace Modules\Incendios\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FirmsDataService
{
    protected ?string $apiKey;
    protected FallbackHotspotsService $fallbackService;

    public function __construct(FallbackHotspotsService $fallbackService)
    {
        $this->apiKey = config('services.firms.key');
        $this->fallbackService = $fallbackService;
    }

    /**
     * Fetch fire data directly from NASA FIRMS CSV API.
     * Uses area-based endpoint for Chiquitanía region with 10-minute cache.
     * 
     * Chiquitanía approximate coordinates (Santa Cruz, Bolivia):
     * West: -62.5, South: -18.5, East: -57.5, North: -14.5
     * 
     * @param string $product Default: VIIRS_NOAA20_NRT (options: VIIRS_SNPP_NRT, MODIS_NRT)
     * @param string $area Bounding box as "west,south,east,north" or "world"
     * @param int $days Number of days (1-10) - Default: 2 for demo
     * @param bool $cluster Whether to cluster nearby fires into hotspots
     * @param float $clusterRadius Radius in km to consider fires as same cluster (default: 20km)
     * @return array Array of fire objects with lat, lng, date, confidence
     */
    public function getFireData(
        string $product = 'VIIRS_NOAA20_NRT', 
        string $area = '-62.5,-18.5,-57.5,-14.5', 
        int $days = 2,
        bool $cluster = true,
        float $clusterRadius = 20.0
    ): array {
        if (! $this->apiKey || trim($this->apiKey) === '') {
            Log::info('FIRMS_API_KEY no configurada: se intenta fallback o datos de demostración.');
            $fallback = $this->tryFallbackApi($days, $area, $cluster, $clusterRadius);
            if ($fallback['ok'] ?? false) {
                return $fallback;
            }

            return $this->demoFiresResponse($area, $days, $cluster, $clusterRadius, 'FIRMS_API_KEY no configurada; mostrando puntos de demostración (Chiquitanía).');
        }

        // Cache key for this specific request (include cluster params)
        $areaKey = str_replace(',', '_', $area);
        $clusterKey = $cluster ? "_c{$clusterRadius}" : '_raw';
        $cacheKey = "firms_fires_{$product}_{$areaKey}_{$days}{$clusterKey}";
        
        // Check cache (10 minutes)
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return [
                'ok' => true,
                'status' => 200,
                'data' => $cached,
                'count' => count($cached),
                'cached' => true,
            ];
        }

        try {
            // Use area endpoint instead of country endpoint
            // Format: /api/area/csv/[MAP_KEY]/[SOURCE]/[AREA_COORDINATES]/[DAY_RANGE]
            $url = sprintf(
                'https://firms.modaps.eosdis.nasa.gov/api/area/csv/%s/%s/%s/%d',
                $this->apiKey,
                $product,
                $area,
                $days
            );

            Log::info('🔥 Intentando obtener datos de NASA FIRMS', [
                'product' => $product,
                'area' => $area,
                'days' => $days
            ]);

            $response = Http::timeout(20)
                ->retry(3, 1000) // Aumentar reintentos de 2 a 3
                ->withHeaders(['User-Agent' => 'SIPII-Laravel/1.0'])
                ->get($url);

            if (! $response->ok()) {
                Log::warning('⚠️ FIRMS API falló, intentando fallback', [
                    'status' => $response->status(),
                    'error' => $response->body()
                ]);

                return $this->tryFallbackApi($days, $area, $cluster, $clusterRadius);
            }

            $csv = $response->body();
            $fires = $this->parseCsv($csv);

            if (count($fires) === 0) {
                Log::info('FIRMS devolvió 0 detecciones; se intenta fallback o demo');
                $fb = $this->tryFallbackApi($days, $area, $cluster, $clusterRadius);
                if ($fb['ok'] ?? false) {
                    return $fb;
                }

                return $this->demoFiresResponse($area, $days, $cluster, $clusterRadius, 'FIRMS sin detecciones en el área; datos de demostración.');
            }

            // Apply clustering if requested
            if ($cluster && count($fires) > 0) {
                $fires = $this->clusterFires($fires, $clusterRadius);
            }

            // Cache for 10 minutes
            Cache::put($cacheKey, $fires, now()->addMinutes(10));

            Log::info('✅ Datos obtenidos exitosamente de FIRMS', [
                'count' => count($fires),
                'clustered' => $cluster
            ]);

            return [
                'ok' => true,
                'status' => 200,
                'data' => $fires,
                'count' => count($fires),
                'cached' => false,
                'source' => 'firms',
            ];
        } catch (\Exception $e) {
            Log::error('❌ Error con FIRMS API, intentando fallback', [
                'error' => $e->getMessage()
            ]);

            $fallback = $this->tryFallbackApi($days, $area, $cluster, $clusterRadius);
            if ($fallback['ok'] ?? false) {
                return $fallback;
            }

            return $this->demoFiresResponse($area, $days, $cluster, $clusterRadius, 'Error en FIRMS: ' . $e->getMessage());
        }
    }

    /**
     * Puntos fijos de demostración (Chiquitanía / Santa Cruz) para entornos sin clave o sin red.
     */
    protected function buildDemoFiresInArea(string $area): array
    {
        $base = [
            ['lat' => -16.48, 'lng' => -60.12, 'confidence' => 'h'],
            ['lat' => -16.20, 'lng' => -59.85, 'confidence' => 'n'],
            ['lat' => -17.10, 'lng' => -60.50, 'confidence' => 'l'],
            ['lat' => -16.00, 'lng' => -59.20, 'confidence' => 'n'],
            ['lat' => -17.35, 'lng' => -59.10, 'confidence' => 'h'],
        ];
        if ($area === 'world') {
            $base = array_merge($base, [
                ['lat' => -19.0, 'lng' => -63.5, 'confidence' => 'l'],
            ]);
        } else {
            $coords = array_map('floatval', explode(',', $area));
            if (count($coords) === 4) {
                [$w, $s, $e, $n] = $coords;
                foreach ($base as $i => $row) {
                    $base[$i]['lat'] = $s + (($n - $s) * (0.2 + $i * 0.12));
                    $base[$i]['lng'] = $w + (($e - $w) * (0.15 + $i * 0.1));
                }
            }
        }

        $today = now()->toDateString();

        return array_map(static function (array $row) use ($today) {
            return [
                'lat' => (float) $row['lat'],
                'lng' => (float) $row['lng'],
                'date' => $today,
                'time' => '1200',
                'confidence' => $row['confidence'],
                'frp' => 12.5,
                '_source' => 'demo',
            ];
        }, $base);
    }

    protected function demoFiresResponse(
        string $area,
        int $days,
        bool $cluster,
        float $clusterRadius,
        string $note = ''
    ): array {
        $fires = $this->buildDemoFiresInArea($area);
        if ($cluster && count($fires) > 0) {
            $fires = $this->clusterFires($fires, $clusterRadius);
        }

        return [
            'ok' => true,
            'status' => 200,
            'data' => $fires,
            'count' => count($fires),
            'cached' => false,
            'source' => 'demo',
            'demo' => true,
            'message' => $note !== '' ? $note : 'Datos de demostración: configure FIRMS_API_KEY para datos reales de NASA.',
        ];
    }

    /**
     * Intentar obtener datos de la API alternativa (fallback)
     * 
     * @param int $days
     * @param string $area Bounding box "west,south,east,north"
     * @param bool $cluster
     * @param float $clusterRadius
     * @return array
     */
    protected function tryFallbackApi(int $days, string $area, bool $cluster, float $clusterRadius): array
    {
        try {
            Log::info('🔄 Activando fallback API...');

            // Parsear bounding box si no es "world"
            $boundingBox = null;
            if ($area !== 'world') {
                $coords = explode(',', $area);
                if (count($coords) === 4) {
                    // Convertir a formato [min_lat, max_lat, min_lng, max_lng]
                    $boundingBox = [
                        (float)$coords[1], // south -> min_lat
                        (float)$coords[3], // north -> max_lat
                        (float)$coords[0], // west -> min_lng
                        (float)$coords[2], // east -> max_lng
                    ];
                }
            }

            // Obtener datos de fallback
            $hotspots = $this->fallbackService->getHotspots($days, $boundingBox);

            if (empty($hotspots)) {
                Log::warning('⚠️ Fallback API no devolvió datos; se usan puntos de demostración');
                return $this->demoFiresResponse($area, $days, $cluster, $clusterRadius, 'Fallback vacío: datos de demostración.');
            }

            // Convertir a formato FIRMS
            $fires = array_map(
                fn($hotspot) => $this->convertFallbackToFire($hotspot),
                $hotspots
            );

            // Aplicar clustering si está habilitado
            if ($cluster && count($fires) > 0) {
                $fires = $this->clusterFires($fires, $clusterRadius);
            }

            Log::info('✅ Datos obtenidos exitosamente de fallback API', [
                'count' => count($fires),
                'clustered' => $cluster
            ]);

            return [
                'ok' => true,
                'status' => 200,
                'data' => $fires,
                'count' => count($fires),
                'cached' => false,
                'source' => 'fallback',
                'fallback_used' => true,
            ];

        } catch (\Exception $e) {
            Log::error('❌ Fallback API también falló; demostración local', [
                'error' => $e->getMessage()
            ]);

            return $this->demoFiresResponse($area, $days, $cluster, $clusterRadius, 'Fallback no disponible: ' . $e->getMessage());
        }
    }

    /**
     * Convertir hotspot de formato fallback a formato fire interno
     * 
     * @param array $hotspot
     * @return array
     */
    protected function convertFallbackToFire(array $hotspot): array
    {
        // Manejar confianza (puede ser 'h'/'n'/'l' o número)
        $confidence = $hotspot['confidence'];
        if (is_numeric($confidence)) {
            $numericConf = (float)$confidence;
            $confidence = $numericConf >= 80 ? 'h' : ($numericConf >= 50 ? 'n' : 'l');
        }

        return [
            'lat' => (float)$hotspot['latitude'],
            'lng' => (float)$hotspot['longitude'],
            'date' => $hotspot['acq_date'] ?? date('Y-m-d'),
            'time' => $hotspot['acq_time'] ?? null,
            'confidence' => $confidence,
            'frp' => $hotspot['frp'] ?? null,
            '_source' => 'fallback',
            '_original_id' => $hotspot['id'] ?? null,
        ];
    }

    /**
     * Parse CSV string into array of fire objects.
     * 
     * CSV format from FIRMS API:
     * - column 0: latitude
     * - column 1: longitude
     * - column 5: acq_date (acquisition date, YYYY-MM-DD)
     * - column 6: acq_time (acquisition time, HHMM)
     * - column 9: confidence (n=nominal, h=high, l=low)
     * - column 12: frp (fire radiative power)
     * 
     * @param string $csv
     * @return array
     */
    protected function parseCsv(string $csv): array
    {
        $lines = array_filter(array_map('trim', explode("\n", trim($csv))));
        
        if (count($lines) < 2) {
            return []; // No data rows
        }

        // Skip header (first line)
        $headers = array_shift($lines);
        
        $fires = [];
        foreach ($lines as $lineNumber => $line) {
            if (empty($line)) {
                continue;
            }

            $data = str_getcsv($line);

            // Verify minimum columns
            if (count($data) < 10) {
                continue;
            }

            $lat = isset($data[0]) ? (float) $data[0] : null;
            $lng = isset($data[1]) ? (float) $data[1] : null;

            // Only add if coordinates are valid
            if ($lat !== null && $lng !== null && !is_nan($lat) && !is_nan($lng)) {
                $fires[] = [
                    'lat' => $lat,
                    'lng' => $lng,
                    'date' => $data[5] ?? 'Fecha desconocida',
                    'time' => $data[6] ?? null,
                    'confidence' => $data[9] ?? null,
                    'frp' => isset($data[12]) ? (float) $data[12] : null,
                ];
            }
        }

        return $fires;
    }

    /**
     * Cluster nearby fires into hotspots using DBSCAN-like algorithm.
     * Fires within the specified radius are grouped together.
     * 
     * @param array $fires Array of fire objects
     * @param float $radiusKm Clustering radius in kilometers
     * @return array Clustered fires (centroids with aggregated data)
     */
    protected function clusterFires(array $fires, float $radiusKm = 2.0): array
    {
        if (empty($fires)) {
            return [];
        }

        $clusters = [];
        $assigned = array_fill(0, count($fires), false);

        for ($i = 0; $i < count($fires); $i++) {
            if ($assigned[$i]) {
                continue;
            }

            // Start a new cluster
            $clusterIndices = [$i];
            $assigned[$i] = true;

            // Expand cluster by finding all nearby fires
            $queue = [$i];
            
            while (!empty($queue)) {
                $currentIndex = array_shift($queue);
                $currentFire = $fires[$currentIndex];

                // Check all unassigned fires
                for ($j = 0; $j < count($fires); $j++) {
                    if ($assigned[$j]) {
                        continue;
                    }

                    $distance = $this->haversineDistance(
                        $currentFire['lat'], $currentFire['lng'],
                        $fires[$j]['lat'], $fires[$j]['lng']
                    );

                    if ($distance <= $radiusKm) {
                        $clusterIndices[] = $j;
                        $assigned[$j] = true;
                        $queue[] = $j; // Add to queue to expand further
                    }
                }
            }

            // Create cluster from collected fires
            $clusterFires = array_map(fn($idx) => $fires[$idx], $clusterIndices);
            $clusters[] = $this->clusterToCentroid($clusterFires);
        }

        return $clusters;
    }

    /**
     * Calculate centroid (center point) of a cluster of fires.
     * Uses average of coordinates weighted by FRP (Fire Radiative Power).
     * 
     * @param array $fires Fires in the cluster
     * @return array Centroid fire object
     */
    protected function clusterToCentroid(array $fires): array
    {
        $count = count($fires);
        
        if ($count === 1) {
            return array_merge($fires[0], [
                'cluster_size' => 1,
                'is_cluster' => false,
            ]);
        }

        // Calculate weighted average using FRP (Fire Radiative Power)
        $totalFrp = 0;
        $weightedLat = 0;
        $weightedLng = 0;
        $maxConfidence = 'l'; // low, nominal, high
        $totalFrpValue = 0;
        $dates = [];

        foreach ($fires as $fire) {
            $frp = $fire['frp'] ?? 1.0; // Use 1.0 if FRP not available
            $totalFrp += $frp;
            $weightedLat += $fire['lat'] * $frp;
            $weightedLng += $fire['lng'] * $frp;
            $totalFrpValue += $frp;
            
            // Track highest confidence
            if ($fire['confidence'] === 'h') {
                $maxConfidence = 'h';
            } elseif ($fire['confidence'] === 'n' && $maxConfidence !== 'h') {
                $maxConfidence = 'n';
            }

            $dates[] = $fire['date'];
        }

        $centroidLat = $totalFrp > 0 ? $weightedLat / $totalFrp : array_sum(array_column($fires, 'lat')) / $count;
        $centroidLng = $totalFrp > 0 ? $weightedLng / $totalFrp : array_sum(array_column($fires, 'lng')) / $count;

        return [
            'lat' => round($centroidLat, 6),
            'lng' => round($centroidLng, 6),
            'date' => $fires[0]['date'], // Use first detection date
            'time' => $fires[0]['time'],
            'confidence' => $maxConfidence,
            'frp' => round($totalFrpValue, 2),
            'cluster_size' => $count,
            'is_cluster' => true,
            'dates' => array_unique($dates),
        ];
    }

    /**
     * Calculate distance between two points using Haversine formula.
     * Returns distance in kilometers.
     * 
     * @param float $lat1 Latitude of first point
     * @param float $lng1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lng2 Longitude of second point
     * @return float Distance in kilometers
     */
    protected function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
