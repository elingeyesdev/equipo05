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
    private function seccionesConfig(): array
    {
        return [
            'solicitante' => ['titulo' => 'Solicitantes', 'tabla' => 'solicitante', 'pk' => 'id_solicitante'],
            'destino' => ['titulo' => 'Destinos', 'tabla' => 'destino', 'pk' => 'id_destino'],
            'ubicacion' => ['titulo' => 'Ubicaciones', 'tabla' => 'ubicacion', 'pk' => 'id_ubicacion'],
            'vehiculo' => ['titulo' => 'Vehículos', 'tabla' => 'vehiculo', 'pk' => 'id_vehiculo'],
            'conductor' => ['titulo' => 'Conductores', 'tabla' => 'conductor', 'pk' => 'id_conductor'],
            'tipo-vehiculo' => ['titulo' => 'Tipo de Vehículo', 'tabla' => 'tipo_vehiculo', 'pk' => 'id_tipo_vehiculo'],
            'tipo-licencia' => ['titulo' => 'Licencias', 'tabla' => 'tipo_licencia', 'pk' => 'id_tipo_licencia'],
            'tipo-emergencia' => ['titulo' => 'Tipo de Emergencia', 'tabla' => 'tipo_emergencia', 'pk' => 'id_tipo_emergencia'],
            'marca' => ['titulo' => 'Marca de vehiculo', 'tabla' => 'marca', 'pk' => 'id_marca'],
            'usuario' => ['titulo' => 'Voluntarios', 'tabla' => 'usuario', 'pk' => 'id'],
            'rol' => ['titulo' => 'Roles', 'tabla' => 'rol', 'pk' => 'id'],
            'estado' => ['titulo' => 'Estados', 'tabla' => 'estado', 'pk' => 'id_estado'],
            'reporte' => ['titulo' => 'Reportes', 'tabla' => 'reporte', 'pk' => 'id_reporte'],
            'solicitud' => ['titulo' => 'Solicitudes', 'tabla' => 'solicitud', 'pk' => 'id_solicitud'],
            'paquete' => ['titulo' => 'Paquetes', 'tabla' => 'paquete', 'pk' => 'id_paquete'],
            'seguimiento' => ['titulo' => 'Seguimiento de Paquetes', 'tabla' => 'historial_seguimiento_donaciones', 'pk' => 'id_historial'],
            'galeria' => ['titulo' => 'Galería de Agradecimiento', 'tabla' => 'paquete', 'pk' => 'id_paquete'],
            'helpdesk' => ['titulo' => 'Centro de Soporte', 'tabla' => 'solicitud', 'pk' => 'id_solicitud'],
        ];
    }

    private function getOptionsForColumn(string $column): array
    {
        $conn = DB::connection('logistica');
        $schema = Schema::connection('logistica');
        return match ($column) {
            'id_licencia' => $conn->table('tipo_licencia')->select('id_tipo_licencia as id', 'tipo_licencia as nombre')->orderBy('tipo_licencia')->get()->toArray(),
            'id_tipovehiculo' => (function () use ($conn, $schema) {
                if (! $schema->hasTable('tipo_vehiculo')) {
                    return [];
                }

                $idColumn = $schema->hasColumn('tipo_vehiculo', 'id_tipovehiculo')
                    ? 'id_tipovehiculo'
                    : ($schema->hasColumn('tipo_vehiculo', 'id_tipo_vehiculo') ? 'id_tipo_vehiculo' : null);

                $nombreColumn = null;
                foreach (['nombre_tipovehiculo', 'nombre_tipo_vehiculo', 'tipo_vehiculo', 'nombre'] as $candidate) {
                    if ($schema->hasColumn('tipo_vehiculo', $candidate)) {
                        $nombreColumn = $candidate;
                        break;
                    }
                }

                if (! $idColumn || ! $nombreColumn) {
                    return [];
                }

                return $conn->table('tipo_vehiculo')
                    ->selectRaw("{$idColumn} as id, {$nombreColumn} as nombre")
                    ->orderBy($nombreColumn)
                    ->get()
                    ->toArray();
            })(),
            'id_marca' => $conn->table('marca')->select('id_marca as id', 'nombre_marca as nombre')->orderBy('nombre_marca')->get()->toArray(),
            'id_solicitud' => $conn->table('solicitud')->select('id_solicitud as id', 'codigo_seguimiento as nombre')->orderByDesc('id_solicitud')->limit(200)->get()->toArray(),
            'id_solicitante' => $conn->table('solicitante')
                ->select('id_solicitante', 'nombre', 'apellido', 'ci')
                ->orderBy('nombre')
                ->limit(200)
                ->get()
                ->map(function ($row) {
                    $nombreCompleto = trim(($row->nombre ?? '') . ' ' . ($row->apellido ?? ''));
                    $ci = $row->ci ?? '-';
                    return (object) [
                        'id' => $row->id_solicitante,
                        'nombre' => ($nombreCompleto !== '' ? $nombreCompleto : 'Solicitante') . " - CI {$ci}",
                    ];
                })
                ->values()
                ->toArray(),
            'id_destino' => $conn->table('destino')
                ->select('id_destino', 'comunidad', 'provincia')
                ->orderByDesc('id_destino')
                ->limit(200)
                ->get()
                ->map(function ($row) {
                    $comunidad = $row->comunidad ?? '';
                    $provincia = $row->provincia ?? '';
                    return (object) [
                        'id' => $row->id_destino,
                        'nombre' => trim($comunidad . ' - ' . $provincia, ' -'),
                    ];
                })
                ->values()
                ->toArray(),
            'estado_id' => $conn->table('estado')->select('id_estado as id', 'nombre_estado as nombre')->orderBy('nombre_estado')->get()->toArray(),
            'id_ubicacion' => $conn->table('ubicacion')
                ->select('id_ubicacion', 'zona', 'latitud', 'longitud')
                ->orderByDesc('id_ubicacion')
                ->limit(200)
                ->get()
                ->map(function ($row) {
                    $lat = $row->latitud ?? '-';
                    $lng = $row->longitud ?? '-';
                    return (object) [
                        'id' => $row->id_ubicacion,
                        'nombre' => $row->zona ?: "Lat {$lat}, Lng {$lng}",
                    ];
                })
                ->values()
                ->toArray(),
            'id_conductor' => $conn->table('conductor')
                ->select('conductor_id', 'nombre', 'apellido', 'ci')
                ->orderBy('nombre')
                ->limit(200)
                ->get()
                ->map(function ($row) {
                    $nombreCompleto = trim(($row->nombre ?? '') . ' ' . ($row->apellido ?? ''));
                    $ci = $row->ci ?? '-';
                    return (object) [
                        'id' => $row->conductor_id,
                        'nombre' => ($nombreCompleto !== '' ? $nombreCompleto : 'Conductor') . " - CI {$ci}",
                    ];
                })
                ->values()
                ->toArray(),
            'id_vehiculo' => $conn->table('vehiculo')
                ->select('id_vehiculo', 'placa', 'modelo')
                ->orderBy('placa')
                ->limit(200)
                ->get()
                ->map(function ($row) {
                    $placa = $row->placa ?? 'SIN_PLACA';
                    $modelo = $row->modelo ?? '';
                    return (object) [
                        'id' => $row->id_vehiculo,
                        'nombre' => trim($placa . ' - ' . $modelo, ' -'),
                    ];
                })
                ->values()
                ->toArray(),
            'user_id' => Schema::connection('logistica')->hasTable('usuario')
                ? $conn->table('usuario')
                    ->select('id', 'nombre', 'apellido')
                    ->orderBy('nombre')
                    ->limit(200)
                    ->get()
                    ->map(function ($row) {
                        return (object) [
                            'id' => $row->id,
                            'nombre' => trim(($row->nombre ?? '') . ' ' . ($row->apellido ?? '')),
                        ];
                    })
                    ->values()
                    ->toArray()
                : [],
            'id_encargado' => Schema::connection('logistica')->hasTable('usuario')
                ? $conn->table('usuario')
                    ->select('ci', 'nombre', 'apellido')
                    ->orderBy('nombre')
                    ->limit(200)
                    ->get()
                    ->map(function ($row) {
                        $nombreCompleto = trim(($row->nombre ?? '') . ' ' . ($row->apellido ?? ''));
                        return (object) [
                            'id' => $row->ci,
                            'nombre' => ($nombreCompleto !== '' ? $nombreCompleto : 'Encargado') . " - CI {$row->ci}",
                        ];
                    })
                    ->values()
                    ->toArray()
                : [],
            default => [],
        };
    }

    private function columnsForCrud(string $tabla, string $pk): array
    {
        $columns = Schema::connection('logistica')->getColumnListing($tabla);

        return array_values(array_filter($columns, function ($col) use ($pk) {
            return !in_array($col, [$pk, 'created_at', 'updated_at'], true);
        }));
    }

    private function normalizeCrudColumns(string $seccion, array $columns): array
    {
        if ($seccion === 'tipo-vehiculo') {
            $columns = array_values(array_filter($columns, fn ($column) => !in_array($column, ['id_tipovehiculo', 'id_tipo_vehiculo'], true)));
        }

        // En vehiculo existe columna legacy "marca" (texto) y "id_marca" (catalogo).
        // Mostramos solo el catalogo para evitar duplicidad/confusion.
        if ($seccion === 'vehiculo' && in_array('id_marca', $columns, true)) {
            $columns = array_values(array_filter($columns, fn ($column) => $column !== 'marca'));
        }

        return $columns;
    }

    private function normalizeCrudPayload(string $tabla, array $data): array
    {
        $schema = Schema::connection('logistica');

        foreach ($data as $column => $value) {
            $type = null;

            try {
                $type = $schema->getColumnType($tabla, $column);
            } catch (\Throwable) {
                // Columna no disponible en el esquema actual.
            }

            if ($type === 'boolean' || $type === 'bool') {
                $data[$column] = $this->castBooleanValue($value);
                continue;
            }

            if ($value === '') {
                $data[$column] = null;
            }
        }

        return $data;
    }

    private function castBooleanValue(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value !== 0;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'on', 'yes', 'si', 'sí'], true);
    }

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
        ], 'id_solicitante');

        $destinoId = $conn->table('destino')->insertGetId([
            'comunidad' => $data['comunidad'],
            'provincia' => $data['provincia'],
            'direccion' => $data['direccion'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_destino');

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
        $secciones = $this->seccionesConfig();

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
            'seccion' => $seccion,
            'tituloSeccion' => $config['titulo'],
            'nombreTabla' => $tabla,
            'primaryKey' => $pk,
            'columnas' => $columnas,
            'filas' => $filas,
            'total' => $total,
        ]);
    }

    public function crudCreate(string $seccion): View
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $columns = $this->columnsForCrud($config['tabla'], $config['pk']);
        $columns = $this->normalizeCrudColumns($seccion, $columns);
        $options = [];
        foreach ($columns as $column) {
            $options[$column] = $this->getOptionsForColumn($column);
        }

        return view('fusion.modulos.logistica-crud-form', [
            'seccion' => $seccion,
            'tituloSeccion' => $config['titulo'],
            'tabla' => $config['tabla'],
            'primaryKey' => $config['pk'],
            'columns' => $columns,
            'options' => $options,
            'registro' => null,
        ]);
    }

    public function crudStore(Request $request, string $seccion): RedirectResponse
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $columns = $this->columnsForCrud($config['tabla'], $config['pk']);
        $data = $this->normalizeCrudPayload($tabla, $request->only($columns));

        if ($config['tabla'] === 'paquete' && empty($data['codigo'])) {
            $data['codigo'] = 'PKG-' . now()->format('YmdHis');
        }
        if ($config['tabla'] === 'paquete' && empty($data['fecha_creacion'])) {
            $data['fecha_creacion'] = now()->toDateString();
        }

        if (Schema::connection('logistica')->hasColumn($tabla, 'created_at')) {
            $data['created_at'] = now();
        }
        if (Schema::connection('logistica')->hasColumn($tabla, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::connection('logistica')->table($tabla)->insert($data);

        return redirect()->route("logistica.$seccion")->with('success', 'Registro creado correctamente.');
    }

    public function crudEdit(string $seccion, int $id): View
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $columns = $this->columnsForCrud($config['tabla'], $config['pk']);
        $columns = $this->normalizeCrudColumns($seccion, $columns);
        $options = [];
        foreach ($columns as $column) {
            $options[$column] = $this->getOptionsForColumn($column);
        }

        $registro = DB::connection('logistica')
            ->table($config['tabla'])
            ->where($config['pk'], $id)
            ->first();
        abort_unless($registro, 404);

        return view('fusion.modulos.logistica-crud-form', [
            'seccion' => $seccion,
            'tituloSeccion' => $config['titulo'],
            'tabla' => $config['tabla'],
            'primaryKey' => $config['pk'],
            'columns' => $columns,
            'options' => $options,
            'registro' => $registro,
        ]);
    }

    public function crudUpdate(Request $request, string $seccion, int $id): RedirectResponse
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $columns = $this->columnsForCrud($config['tabla'], $config['pk']);
        $data = $this->normalizeCrudPayload($tabla, $request->only($columns));

        if (Schema::connection('logistica')->hasColumn($tabla, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::connection('logistica')
            ->table($tabla)
            ->where($config['pk'], $id)
            ->update($data);

        return redirect()->route("logistica.$seccion")->with('success', 'Registro actualizado correctamente.');
    }

    public function crudDestroy(string $seccion, int $id): RedirectResponse
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        DB::connection('logistica')
            ->table($config['tabla'])
            ->where($config['pk'], $id)
            ->delete();

        return redirect()->route("logistica.$seccion")->with('success', 'Registro eliminado correctamente.');
    }
}
