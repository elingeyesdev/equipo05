<?php

namespace App\Exports;

use Barryvdh\DomPDF\Facade\Pdf;

class FiresActivityPdfExport
{
    protected $data;
    protected $filters;
    protected $statistics;

    public function __construct($data, $filters, $statistics)
    {
        $this->data = $data;
        $this->filters = $filters;
        $this->statistics = $statistics;
    }

    public function export()
    {
        $pdf = Pdf::loadView('exports.fires_activity_pdf', [
            'fires' => $this->data,
            'filters' => $this->filters,
            'statistics' => $this->statistics,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = 'reporte_incendios_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
