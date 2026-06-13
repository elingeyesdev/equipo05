<?php

namespace App\Http\Controllers\LogisticaTransportacion;

use App\Http\Controllers\Controller;
use App\Support\AccessControl;
use App\Support\LogisticaOperativa;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ModuloController extends Controller
{
    public function index(): View
    {
        $connection = 'logistica';
        $tablasResumen = [
            'solicitud' => 'Solicitudes',
            'paquete' => 'Paquetes',
            'historial_seguimiento_donaciones' => 'Seguimientos',
            'vehiculo' => 'Vehiculos',
            'conductor' => 'Conductores',
            'reporte' => 'Reportes',
        ];

        $resumen = [];
        foreach ($tablasResumen as $tabla => $label) {
            $total = 0;
            if (Schema::connection($connection)->hasTable($tabla)) {
                $q = DB::connection($connection)->table($tabla);
                if ($tabla === 'solicitud') {
                    $q->where(function ($w) {
                        $w->whereNull('codigo_seguimiento')
                            ->orWhere('codigo_seguimiento', 'not like', 'LOG-DEMO-%');
                    });
                }
                if ($tabla === 'paquete') {
                    $q->where(function ($w) {
                        $w->whereNull('codigo')
                            ->orWhere('codigo', 'not like', 'PKG-LOG-DEMO-%');
                    });
                }
                $total = $q->count();
            }
            $resumen[] = ['label' => $label, 'total' => $total];
        }

        $solicitudesRecientes = LogisticaOperativa::solicitudesOperativas()->take(10);
        $vistaIntegrada = AccessControl::vistaIntegradaModulos(auth()->user());

        return view('fusion.modulos.logistica', [
            'resumen' => $resumen,
            'solicitudesRecientes' => $solicitudesRecientes,
            'vistaIntegrada' => $vistaIntegrada,
        ]);
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'logistica-transportacion-donaciones',
        ]);
    }
}
