<?php

namespace App\Http\Controllers;

use App\Models\Campania;

class CampaniaReporteController extends Controller
{
    public function general()
    {
        // Traemos todas las campañas con sumas y conteos
        $campanias = Campania::select('campanias.*')
            ->withSum(['donaciones as total_donado' => function ($q) {
                $q->where('estadoid', 2); // 2 = Confirmada (ajusta si tu catálogo es distinto)
            }], 'monto')
            ->withCount(['donaciones as cantidad_donaciones' => function ($q) {
                $q->where('estadoid', 2);
            }])
            ->withSum('asignaciones as total_asignado', 'monto')
            ->get();

        // Totales generales del sistema
        $metaTotal        = $campanias->sum('metarecaudacion');
        $recaudadoTotal   = $campanias->sum('total_donado');
        $asignadoTotal    = $campanias->sum('total_asignado');
        $saldoTotal       = $recaudadoTotal - $asignadoTotal;
        $faltanteTotal    = max(0, $metaTotal - $recaudadoTotal);

        return view('campanias.reporte_general', compact(
            'campanias',
            'metaTotal',
            'recaudadoTotal',
            'asignadoTotal',
            'saldoTotal',
            'faltanteTotal'
        ));
    }
}
