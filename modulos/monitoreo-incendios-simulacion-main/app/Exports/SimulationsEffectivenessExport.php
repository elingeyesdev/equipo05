<?php

namespace App\Exports;

class SimulationsEffectivenessExport
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
        $filename = 'reporte_simulaciones_' . date('Y-m-d_His') . '.csv';
        $tempFile = storage_path('app/' . $filename);
        
        $handle = fopen($tempFile, 'w');
        
        // BOM UTF-8 para Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($handle, [
            'ID',
            'Fecha',
            'Hora',
            'Temperatura (°C)',
            'Humedad (%)',
            'Viento (km/h)',
            'Dirección',
            'Focos Activos',
            'Riesgo',
            'Duración (min)',
            'Voluntarios'
        ]);
        
        // Datos
        foreach ($this->data as $simulation) {
            fputcsv($handle, [
                $simulation->id,
                $simulation->fecha->format('d/m/Y'),
                $simulation->fecha->format('H:i'),
                number_format($simulation->temperature, 1, '.', ''),
                number_format($simulation->humidity, 1, '.', ''),
                number_format($simulation->wind_speed, 1, '.', ''),
                $simulation->wind_direction ?? '-',
                $simulation->focos_activos,
                number_format($simulation->fire_risk, 2, '.', ''),
                number_format($simulation->duracion, 0, '.', ''),
                $simulation->num_voluntarios_enviados
            ]);
        }
        
        fclose($handle);
        
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
