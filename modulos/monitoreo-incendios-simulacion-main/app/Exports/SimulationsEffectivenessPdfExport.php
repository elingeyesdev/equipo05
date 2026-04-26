<?php

namespace Modules\Incendios\Exports;

use Barryvdh\DomPDF\Facade\Pdf;

class SimulationsEffectivenessPdfExport
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
        $pdf = Pdf::loadView('exports.simulations_effectiveness_pdf', [
            'simulations' => $this->data,
            'filters' => $this->filters,
            'statistics' => $this->statistics,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = 'reporte_simulaciones_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }
}
