<?php

namespace Modules\Incendios\Exports;

class BiomasasManagementExport
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
        $filename = 'reporte_biomasas_' . date('Y-m-d_His') . '.csv';
        $tempFile = storage_path('app/' . $filename);
        
        $handle = fopen($tempFile, 'w');
        
        // BOM UTF-8 para Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($handle, [
            'ID',
            'Fecha',
            'Hora',
            'Ubicación',
            'Área (ha)',
            'Densidad',
            'Tipo de Biomasa',
            'Estado',
            'Creado Por'
        ]);
        
        // Datos
        foreach ($this->data as $biomasa) {
            fputcsv($handle, [
                $biomasa->id,
                $biomasa->created_at->format('d/m/Y'),
                $biomasa->created_at->format('H:i'),
                $biomasa->ubicacion ?? 'Sin ubicación',
                number_format($biomasa->area_m2 / 10000, 2, '.', ''),
                ucfirst($biomasa->densidad ?? 'N/A'),
                $biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A',
                ucfirst($biomasa->estado),
                $biomasa->user->name ?? 'N/A'
            ]);
        }
        
        fclose($handle);
        
        return response()->download($tempFile, $filename, [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend(true);
    }
}
