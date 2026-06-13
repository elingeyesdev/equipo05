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
        $voluntariosActivos = 0;
        try {
            if (Schema::connection('seguimiento')->hasTable('usuario')) {
                $voluntariosActivos = DB::connection('seguimiento')->table('usuario')
                    ->where('activo', true)
                    ->where(function($q) {
                        $q->whereNull('administrador')->orWhere('administrador', false);
                    })
                    ->count();
            }
        } catch (\Exception $e) {}

        $comunariosApoyo = 0;
        try {
            if (Schema::connection('cuadrillas')->hasTable('comunario')) {
                $comunariosApoyo = DB::connection('cuadrillas')->table('comunario')->count();
            } elseif (Schema::connection('cuadrillas')->hasTable('comunarios')) {
                $comunariosApoyo = DB::connection('cuadrillas')->table('comunarios')->count();
            }
        } catch (\Exception $e) {}

        $reportesEsteMes = 0;
        try {
            if (Schema::connection('cuadrillas')->hasTable('reporte')) {
                $reportesEsteMes = DB::connection('cuadrillas')->table('reporte')
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count();
            }
        } catch (\Exception $e) {}

        $incendiosReportados = 0;
        try {
            if (Schema::connection('cuadrillas')->hasTable('reporte_incendio')) {
                $incendiosReportados = DB::connection('cuadrillas')->table('reporte_incendio')->count();
            }
        } catch (\Exception $e) {}

        return view('fusion.modulos.cuadrillas-incendios', compact(
            'voluntariosActivos',
            'comunariosApoyo',
            'reportesEsteMes',
            'incendiosReportados'
        ));
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'cuadrillas-incendios-kardex-cursos',
        ]);
    }
}

