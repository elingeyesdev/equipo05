<?php

namespace Modules\Incendios\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Servicio de fallback para obtener hotspots de API alternativa
 * cuando NASA FIRMS API no está disponible
 */
class FallbackHotspotsService
{
    /**
     * Base URL de la API alternativa
     */
    private string $baseUrl;

    /**
     * Timeout para requests HTTP en segundos
     */
    private int $timeout;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baseUrl = (string) config('services.fallback_hotspots.url', '');
        $this->timeout = config('services.fallback_hotspots.timeout', 15);
    }

    /**
     * Obtener hotspots de la API alternativa
     * 
     * @param int $days Días hacia atrás
     * @param array|null $boundingBox [min_lat, max_lat, min_lng, max_lng]
     * @param string|null $minConfidence Confianza mínima (h, n, l)
     * @return array
     * @throws Exception
     */
    public function getHotspots(
        int $days = 7,
        ?array $boundingBox = null,
        ?string $minConfidence = null
    ): array {
        $base = (string) $this->baseUrl;
        if ($base === '' || $base === 'http://cambiarurl' || $base === 'https://cambiarurl') {
            Log::info('Fallback de hotspots: URL no configurada (FALLBACK_HOTSPOTS_URL), se omite la petición HTTP.');

            return [];
        }
        try {
            Log::info('🔄 Intentando obtener hotspots de API alternativa (fallback)', [
                'url' => $this->baseUrl,
                'days' => $days,
                'has_bounding_box' => !is_null($boundingBox),
                'min_confidence' => $minConfidence
            ]);

            // Construir query parameters
            $params = [
                'days' => $days,
            ];

            if ($boundingBox) {
                $params['min_lat'] = $boundingBox[0];
                $params['max_lat'] = $boundingBox[1];
                $params['min_lng'] = $boundingBox[2];
                $params['max_lng'] = $boundingBox[3];
            }

            if ($minConfidence) {
                $params['min_confidence'] = $minConfidence;
            }

            // Realizar request con timeout
            $response = Http::timeout($this->timeout)
                ->retry(2, 1000) // 2 reintentos con 1 segundo de espera
                ->get("{$this->baseUrl}/api/v1/hotspots", $params);

            if (!$response->successful()) {
                throw new Exception(
                    "Fallback API error: HTTP {$response->status()} - {$response->body()}"
                );
            }

            $data = $response->json();

            // Validar estructura de respuesta
            if (!isset($data['success']) || !$data['success']) {
                throw new Exception('Fallback API returned success=false');
            }

            if (!isset($data['data']) || !is_array($data['data'])) {
                throw new Exception('Fallback API returned invalid data structure');
            }

            Log::info('✅ Hotspots obtenidos exitosamente de API alternativa', [
                'count' => count($data['data']),
                'total' => $data['meta']['total'] ?? count($data['data']),
                'timestamp' => $data['timestamp'] ?? now()
            ]);

            return $data['data'];

        } catch (Exception $e) {
            Log::error('❌ Error al obtener datos de API alternativa (fallback)', [
                'error' => $e->getMessage(),
                'url' => $this->baseUrl,
                'days' => $days
            ]);

            throw $e;
        }
    }

    /**
     * Convertir hotspot de formato alternativo a formato FIRMS
     * 
     * @param array $hotspot Hotspot en formato alternativo
     * @return array Hotspot en formato FIRMS
     */
    public function convertToFirmsFormat(array $hotspot): array
    {
        // Manejar confianza (puede ser 'h'/'n'/'l' o número)
        $confidence = $hotspot['confidence'];
        if (is_numeric($confidence)) {
            // Si es número, convertir a letra según rango
            $confidence = $this->numericConfidenceToLetter((float)$confidence);
        }

        // Combinar fecha y hora en formato FIRMS
        $acqDateTime = null;
        if (isset($hotspot['acq_date']) && isset($hotspot['acq_time'])) {
            // acq_date: "2025-11-30", acq_time: "1430"
            $date = $hotspot['acq_date'];
            $time = str_pad($hotspot['acq_time'], 4, '0', STR_PAD_LEFT);
            $hour = substr($time, 0, 2);
            $minute = substr($time, 2, 2);
            $acqDateTime = "{$date} {$hour}:{$minute}:00";
        }

        return [
            'latitude' => (float)$hotspot['latitude'],
            'longitude' => (float)$hotspot['longitude'],
            'brightness' => $hotspot['bright_ti4'] ?? null,
            'bright_t31' => $hotspot['bright_ti5'] ?? null,
            'scan' => null, // No disponible en formato alternativo
            'track' => null, // No disponible
            'acq_date' => $hotspot['acq_date'] ?? null,
            'acq_time' => $hotspot['acq_time'] ?? null,
            'satellite' => 'FALLBACK', // Marcar como fuente alternativa
            'confidence' => $confidence,
            'version' => '1.0NRT',
            'bright_ti4' => $hotspot['bright_ti4'] ?? null,
            'bright_ti5' => $hotspot['bright_ti5'] ?? null,
            'frp' => $hotspot['frp'] ?? null,
            'daynight' => $this->inferDayNight($hotspot['acq_time'] ?? null),
            
            // Metadatos adicionales
            '_source' => 'fallback_api',
            '_original_id' => $hotspot['id'] ?? null,
            '_acquired_at' => $acqDateTime,
        ];
    }

    /**
     * Convertir confianza numérica a letra
     * 
     * @param float $confidence Confianza numérica (0-100)
     * @return string 'h', 'n', o 'l'
     */
    private function numericConfidenceToLetter(float $confidence): string
    {
        if ($confidence >= 80) {
            return 'h'; // high
        } elseif ($confidence >= 50) {
            return 'n'; // nominal
        } else {
            return 'l'; // low
        }
    }

    /**
     * Inferir día/noche basado en hora de adquisición
     * 
     * @param string|null $acqTime Hora en formato HHMM
     * @return string 'D' o 'N'
     */
    private function inferDayNight(?string $acqTime): string
    {
        if (!$acqTime) {
            return 'D'; // Default: día
        }

        $hour = (int)substr(str_pad($acqTime, 4, '0', STR_PAD_LEFT), 0, 2);
        
        // Considerar noche entre 18:00 y 06:00
        return ($hour >= 18 || $hour < 6) ? 'N' : 'D';
    }

    /**
     * Verificar si la API alternativa está disponible
     * 
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/api/v1/hotspots", ['days' => 1]);
            
            return $response->successful();
        } catch (Exception $e) {
            Log::warning('⚠️ API alternativa no disponible', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Obtener estadísticas de la API alternativa
     * 
     * @return array|null
     */
    public function getStatistics(): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/v1/hotspots", ['days' => 1]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            return [
                'available' => true,
                'total_hotspots' => $data['meta']['total'] ?? 0,
                'last_page' => $data['meta']['last_page'] ?? 1,
                'timestamp' => $data['timestamp'] ?? now()->toIso8601String(),
            ];
        } catch (Exception $e) {
            return [
                'available' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
