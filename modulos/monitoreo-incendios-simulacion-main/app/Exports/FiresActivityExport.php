<?php

namespace App\Exports;

class FiresActivityExport
{
    protected $data;
    protected $filters;

    public function __construct($data, $filters)
    {
        $this->data = $data;
        $this->filters = $filters;
    }

    public function export()
    {
        $filename = 'reporte_incendios_' . date('Y-m-d_His') . '.csv';
        $tempFile = storage_path('app/' . $filename);
        
        $handle = fopen($tempFile, 'w');
        
        // BOM UTF-8 para que Excel abra correctamente los caracteres especiales
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($handle, [
            'ID', 
            'Fecha', 
            'Hora',
            'Ubicación', 
            'Latitud', 
            'Longitud', 
            'Intensidad', 
            'Nivel'
        ]);
        
        // Datos
        foreach ($this->data as $foco) {
            // Extraer lat/lon del array de coordenadas
            $lat = '';
            $lon = '';
            
            if ($foco->coordenadas && is_array($foco->coordenadas)) {
                $lat = $foco->coordenadas['lat'] ?? $foco->coordenadas['latitude'] ?? $foco->coordenadas[0] ?? '';
                $lon = $foco->coordenadas['lng'] ?? $foco->coordenadas['lon'] ?? $foco->coordenadas['longitude'] ?? $foco->coordenadas[1] ?? '';
            }
            
            // Nivel de intensidad (rango 1-10)
            $nivelIntensidad = 'Baja';
            if ($foco->intensidad >= 7) {
                $nivelIntensidad = 'Alta';
            } elseif ($foco->intensidad >= 4) {
                $nivelIntensidad = 'Media';
            }
            
            fputcsv($handle, [
                $foco->id,
                $foco->fecha->format('d/m/Y'),
                $foco->fecha->format('H:i'),
                $foco->ubicacion ?? 'Sin ubicación',
                $lat ? number_format((float)$lat, 6, '.', '') : 'N/A',
                $lon ? number_format((float)$lon, 6, '.', '') : 'N/A',
                number_format($foco->intensidad, 2, '.', ''),
                $nivelIntensidad
            ]);
        }
        
        fclose($handle);
        
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
