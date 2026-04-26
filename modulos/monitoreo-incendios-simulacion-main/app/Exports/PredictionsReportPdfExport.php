<?php

namespace Modules\Incendios\Exports;

use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade\Pdf;

class PredictionsReportPdfExport
{
    protected $predictions;
    protected $filters;
    protected $statistics;

    public function __construct(Collection $predictions, array $filters = [], array $statistics = [])
    {
        $this->predictions = $predictions;
        $this->filters = $filters;
        $this->statistics = $statistics;
    }

    public function export()
    {
        $pdf = Pdf::loadView('exports.predictions_report_pdf', [
            'predictions' => $this->predictions,
            'filters' => $this->filters,
            'statistics' => $this->statistics,
        ]);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('predicciones_' . date('Y-m-d_His') . '.pdf');
    }
}
