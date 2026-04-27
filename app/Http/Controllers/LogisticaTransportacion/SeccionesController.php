<?php

namespace App\Http\Controllers\LogisticaTransportacion;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SeccionesController extends Controller
{
    public function show(string $seccion): View
    {
        $secciones = [
            'solicitudes' => ['titulo' => 'Solicitudes', 'tabla' => 'solicitud', 'pk' => 'id_solicitud'],
            'paquetes' => ['titulo' => 'Paquetes', 'tabla' => 'paquete', 'pk' => 'id_paquete'],
            'seguimiento' => ['titulo' => 'Seguimiento', 'tabla' => 'historial_seguimiento_donaciones', 'pk' => 'id_historial'],
            'solicitantes' => ['titulo' => 'Solicitantes', 'tabla' => 'solicitante', 'pk' => 'id_solicitante'],
            'destinos' => ['titulo' => 'Destinos', 'tabla' => 'destino', 'pk' => 'id_destino'],
            'ubicaciones' => ['titulo' => 'Ubicaciones', 'tabla' => 'ubicacion', 'pk' => 'id_ubicacion'],
            'vehiculos' => ['titulo' => 'Vehiculos', 'tabla' => 'vehiculo', 'pk' => 'id_vehiculo'],
            'conductores' => ['titulo' => 'Conductores', 'tabla' => 'conductor', 'pk' => 'id_conductor'],
            'tipos-vehiculo' => ['titulo' => 'Tipos de Vehiculo', 'tabla' => 'tipo_vehiculo', 'pk' => 'id_tipo_vehiculo'],
            'tipos-licencia' => ['titulo' => 'Tipos de Licencia', 'tabla' => 'tipo_licencia', 'pk' => 'id_tipo_licencia'],
            'tipos-emergencia' => ['titulo' => 'Tipos de Emergencia', 'tabla' => 'tipo_emergencia', 'pk' => 'id_tipo_emergencia'],
            'marcas' => ['titulo' => 'Marcas', 'tabla' => 'marca', 'pk' => 'id_marca'],
            'reportes' => ['titulo' => 'Reportes', 'tabla' => 'reporte', 'pk' => 'id_reporte'],
            'usuarios' => ['titulo' => 'Usuarios', 'tabla' => 'usuario', 'pk' => 'id'],
            'roles' => ['titulo' => 'Roles', 'tabla' => 'rol', 'pk' => 'id'],
            'estados' => ['titulo' => 'Estados', 'tabla' => 'estado', 'pk' => 'id_estado'],
        ];

        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $pk = $config['pk'];
        $connection = 'logistica';

        $columnas = [];
        $filas = collect();
        $total = 0;

        if (Schema::connection($connection)->hasTable($tabla)) {
            $columnas = Schema::connection($connection)->getColumnListing($tabla);
            $columnas = array_slice($columnas, 0, 8);

            $query = DB::connection($connection)->table($tabla);
            if (in_array('created_at', $columnas, true)) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn($tabla, $pk)) {
                $query->orderByDesc($pk);
            }

            $filas = $query->limit(20)->get($columnas);
            $total = DB::connection($connection)->table($tabla)->count();
        }

        return view('fusion.modulos.logistica-seccion', [
            'tituloSeccion' => $config['titulo'],
            'nombreTabla' => $tabla,
            'columnas' => $columnas,
            'filas' => $filas,
            'total' => $total,
        ]);
    }
}
