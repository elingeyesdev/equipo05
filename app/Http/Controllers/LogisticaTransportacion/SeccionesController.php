<?php

namespace App\Http\Controllers\LogisticaTransportacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SeccionesController extends Controller
{
    public function solicitudCreate(): View
    {
        return view('fusion.modulos.logistica-solicitud-create');
    }

    public function solicitudStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'apellido' => ['nullable', 'string', 'max:120'],
            'ci' => ['required', 'string', 'max:40'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'comunidad' => ['required', 'string', 'max:120'],
            'provincia' => ['required', 'string', 'max:120'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'tipo_emergencia' => ['required', 'string', 'max:120'],
            'cantidad_personas' => ['required', 'integer', 'min:1'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_necesidad' => ['nullable', 'date'],
            'insumos_necesarios' => ['nullable', 'string'],
        ]);

        $conn = DB::connection('logistica');

        $solicitanteId = $conn->table('solicitante')->insertGetId([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'] ?? null,
            'ci' => $data['ci'],
            'telefono' => $data['telefono'] ?? null,
            'email' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $destinoId = $conn->table('destino')->insertGetId([
            'comunidad' => $data['comunidad'],
            'provincia' => $data['provincia'],
            'direccion' => $data['direccion'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $codigo = 'SOL-' . now()->format('YmdHis');

        $conn->table('solicitud')->insert([
            'estado' => 'pendiente',
            'codigo_seguimiento' => $codigo,
            'cantidad_personas' => $data['cantidad_personas'],
            'fecha_inicio' => $data['fecha_inicio'],
            'tipo_emergencia' => $data['tipo_emergencia'],
            'insumos_necesarios' => $data['insumos_necesarios'] ?? null,
            'id_solicitante' => $solicitanteId,
            'id_destino' => $destinoId,
            'fecha_solicitud' => now()->toDateString(),
            'aprobada' => 0,
            'apoyoaceptado' => 0,
            'fecha_necesidad' => $data['fecha_necesidad'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('logistica.solicitud')->with('success', 'Solicitud creada correctamente.');
    }

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
            'solicitante' => ['titulo' => 'Solicitantes', 'tabla' => 'solicitante', 'pk' => 'id_solicitante'],
            'destino' => ['titulo' => 'Destinos', 'tabla' => 'destino', 'pk' => 'id_destino'],
            'ubicacion' => ['titulo' => 'Ubicaciones', 'tabla' => 'ubicacion', 'pk' => 'id_ubicacion'],
            'vehiculo' => ['titulo' => 'Vehículos', 'tabla' => 'vehiculo', 'pk' => 'id_vehiculo'],
            'conductor' => ['titulo' => 'Conductores', 'tabla' => 'conductor', 'pk' => 'id_conductor'],
            'tipo-vehiculo' => ['titulo' => 'Tipo de Vehículo', 'tabla' => 'tipo_vehiculo', 'pk' => 'id_tipo_vehiculo'],
            'tipo-licencia' => ['titulo' => 'Licencias', 'tabla' => 'tipo_licencia', 'pk' => 'id_tipo_licencia'],
            'tipo-emergencia' => ['titulo' => 'Tipo de Emergencia', 'tabla' => 'tipo_emergencia', 'pk' => 'id_tipo_emergencia'],
            'marca' => ['titulo' => 'Marcas', 'tabla' => 'marca', 'pk' => 'id_marca'],
            'reporte' => ['titulo' => 'Reportes', 'tabla' => 'reporte', 'pk' => 'id_reporte'],
            'usuario' => ['titulo' => 'Voluntarios', 'tabla' => 'usuario', 'pk' => 'id'],
            'rol' => ['titulo' => 'Roles', 'tabla' => 'rol', 'pk' => 'id'],
            'estado' => ['titulo' => 'Estados', 'tabla' => 'estado', 'pk' => 'id_estado'],
            'galeria' => ['titulo' => 'Galería de Agradecimiento', 'tabla' => 'paquete', 'pk' => 'id_paquete'],
            'helpdesk' => ['titulo' => 'Centro de Soporte', 'tabla' => 'solicitud', 'pk' => 'id_solicitud'],
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
