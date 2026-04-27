<?php

namespace App\Http\Controllers\LogisticaTransportacion;

use App\Http\Controllers\Controller;
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
            $resumen[] = [
                'label' => $label,
                'total' => Schema::connection($connection)->hasTable($tabla)
                    ? DB::connection($connection)->table($tabla)->count()
                    : 0,
            ];
        }

        $solicitudesRecientes = [];
        if (Schema::connection($connection)->hasTable('solicitud')) {
            $query = DB::connection($connection)->table('solicitud');
            if (Schema::connection($connection)->hasColumn('solicitud', 'created_at')) {
                $query->orderByDesc('created_at');
            } else {
                $query->orderByDesc('id_solicitud');
            }
            $solicitudesRecientes = $query->limit(10)->get();
        }

        return view('fusion.modulos.logistica', [
            'resumen' => $resumen,
            'solicitudesRecientes' => $solicitudesRecientes,
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
