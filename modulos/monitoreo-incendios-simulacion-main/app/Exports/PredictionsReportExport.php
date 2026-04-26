<?php

namespace Modules\Incendios\Exports;

use Illuminate\Support\Collection;

class PredictionsReportExport
{
    protected $predictions;
    protected $filters;

    public function __construct(Collection $predictions, array $filters = [])
    {
        $this->predictions = $predictions;
        $this->filters = $filters;
    }

    public function export()
    {
        $filename = 'predicciones_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID',
                'Fecha',
                'Hora',
                'Latitud Origen',
                'Longitud Origen',
                'Riesgo Propagación',
                'Área Afectada (km²)',
                'Puntos en Trayectoria',
                'Temperatura (°C)',
                'Velocidad Viento (km/h)',
                'Dirección Viento',
                'Humedad (%)'
            ]);

            // Data rows
            foreach ($this->predictions as $prediction) {
                $meta = $prediction->meta ?? [];
                $path = $prediction->path ?? [];
                $inputs = $meta['input_parameters'] ?? [];
                
                // Get fire data - try foco first, then path[0]
                $lat = null;
                $lng = null;
                
                $foco = $prediction->focoIncendio;
                if ($foco && $foco->coordenadas) {
                    $coords = $foco->coordenadas;
                    if (is_array($coords)) {
                        $lat = $coords[0] ?? null;
                        $lng = $coords[1] ?? null;
                    }
                } elseif (isset($path[0])) {
                    // Use first path point as origin
                    $lat = $path[0]['lat'] ?? null;
                    $lng = $path[0]['lng'] ?? null;
                }

                // Get max area from path
                $maxArea = 0;
                if (is_array($path)) {
                    foreach ($path as $point) {
                        if (isset($point['affected_area_km2'])) {
                            $maxArea = max($maxArea, $point['affected_area_km2']);
                        }
                    }
                }

                // Calculate risk from fire_risk_index (0-100 scale)
                $riskIndex = $meta['fire_risk_index'] ?? 0;
                $risk = $riskIndex / 100;

                fputcsv($file, [
                    $prediction->id,
                    $prediction->predicted_at ? $prediction->predicted_at->format('Y-m-d') : '',
                    $prediction->predicted_at ? $prediction->predicted_at->format('H:i:s') : '',
                    $lat ?? 'N/A',
                    $lng ?? 'N/A',
                    round($risk, 2),
                    round($maxArea, 2),
                    count($path),
                    isset($inputs['temperature']) ? round($inputs['temperature'], 1) : 'N/A',
                    isset($inputs['wind_speed']) ? round($inputs['wind_speed'], 1) : 'N/A',
                    isset($inputs['wind_direction']) ? $inputs['wind_direction'] : 'N/A',
                    isset($inputs['humidity']) ? round($inputs['humidity'], 1) : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
