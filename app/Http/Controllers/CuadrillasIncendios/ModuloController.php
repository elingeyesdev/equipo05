<?php

namespace App\Http\Controllers\CuadrillasIncendios;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ModuloController extends Controller
{
    public function index(): View
    {
        $connection = 'cuadrillas';
        $tablasResumen = [
            'reporte' => 'Reportes',
            'reporte_incendio' => 'Reportes de Incendio',
            'foco_calor' => 'Focos de Calor',
            'equipo' => 'Equipos',
            'recurso' => 'Recursos',
            'curso' => 'Cursos',
            'inscrito' => 'Inscritos',
            'usuario' => 'Usuarios',
        ];

        $resumen = [];
        foreach ($tablasResumen as $tabla => $label) {
            $resumen[] = [
                'label' => $label,
                'total' => Schema::connection($connection)->hasTable($tabla)
                    ? DB::connection($connection)->table($tabla)->count()
                    : 0,
            ];
        }

        $reportesRecientes = collect();
        if (Schema::connection($connection)->hasTable('reporte')) {
            $query = DB::connection($connection)->table('reporte');
            if (Schema::connection($connection)->hasColumn('reporte', 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn('reporte', 'id_reporte')) {
                $query->orderByDesc('id_reporte');
            }
            $reportesRecientes = $query->limit(10)->get();
        }

        return view('fusion.modulos.cuadrillas-incendios', [
            'resumen' => $resumen,
            'reportesRecientes' => $reportesRecientes,
        ]);
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'cuadrillas-incendios-kardex-cursos',
        ]);
    }
}
