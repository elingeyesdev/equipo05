<?php

namespace App\Http\Controllers\LogisticaTransportacion;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SeccionesController extends Controller
{
    public function solicitudes(): View
    {
        $solicitudes = DB::connection('logistica')
            ->table('solicitud')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->select([
                'solicitud.id_solicitud',
                'solicitud.codigo_seguimiento',
                'solicitud.estado',
                'solicitud.aprobada',
                'solicitud.tipo_emergencia',
                'solicitud.cantidad_personas',
                'solicitud.fecha_inicio',
                'solicitud.fecha_necesidad',
                'solicitud.created_at',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'solicitante.ci as solicitante_ci',
                'solicitante.telefono as solicitante_telefono',
                'destino.comunidad as destino_comunidad',
                'destino.provincia as destino_provincia',
                'destino.direccion as destino_direccion',
            ])
            ->orderByDesc('solicitud.created_at')
            ->limit(30)
            ->get();

        return view('fusion.modulos.logistica-solicitudes', compact('solicitudes'));
    }

    public function paquetes(): View
    {
        $paquetes = DB::connection('logistica')
            ->table('paquete')
            ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->leftJoin('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->leftJoin('estado', 'paquete.estado_id', '=', 'estado.id_estado')
            ->select([
                'paquete.id_paquete',
                'paquete.codigo',
                'paquete.ubicacion_actual',
                'paquete.fecha_creacion',
                'paquete.fecha_entrega',
                'paquete.updated_at',
                'solicitud.tipo_emergencia',
                'solicitud.codigo_seguimiento',
                'solicitante.nombre as solicitante_nombre',
                'solicitante.apellido as solicitante_apellido',
                'solicitante.ci as solicitante_ci',
                'estado.nombre_estado as estado_nombre',
            ])
            ->orderByDesc('paquete.updated_at')
            ->limit(30)
            ->get();

        return view('fusion.modulos.logistica-paquetes', compact('paquetes'));
    }

    public function seguimiento(): View
    {
        $seguimientos = DB::connection('logistica')
            ->table('historial_seguimiento_donaciones')
            ->leftJoin('paquete', 'historial_seguimiento_donaciones.id_paquete', '=', 'paquete.id_paquete')
            ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
            ->select([
                'historial_seguimiento_donaciones.id_historial',
                'historial_seguimiento_donaciones.id_paquete',
                'historial_seguimiento_donaciones.estado',
                'historial_seguimiento_donaciones.fecha_actualizacion',
                'historial_seguimiento_donaciones.vehiculo_placa',
                'historial_seguimiento_donaciones.conductor_nombre',
                'historial_seguimiento_donaciones.conductor_ci',
                'paquete.codigo as paquete_codigo',
                'solicitud.codigo_seguimiento',
            ])
            ->orderByDesc('historial_seguimiento_donaciones.fecha_actualizacion')
            ->limit(30)
            ->get();

        return view('fusion.modulos.logistica-seguimiento', compact('seguimientos'));
    }

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
