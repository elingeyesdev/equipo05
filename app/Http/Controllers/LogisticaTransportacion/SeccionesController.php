<?php

namespace App\Http\Controllers\LogisticaTransportacion;

use App\Http\Controllers\Controller;
use App\Support\FusionModuloAccess;
use App\Support\AccessControl;
use App\Support\LogisticaCrudUi;
use App\Support\LogisticaOperativa;
use App\Support\LogisticaMapa;
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
            'id_licencia' => (function () use ($conn, $schema) {
                if (! $schema->hasTable('tipo_licencia')) {
                    return [];
                }
                $nombreCol = $schema->hasColumn('tipo_licencia', 'tipo_licencia') ? 'tipo_licencia' : 'nombre';

                return $conn->table('tipo_licencia')
                    ->selectRaw("id_tipo_licencia as id, {$nombreCol} as nombre")
                    ->orderBy($nombreCol)
                    ->get()
                    ->toArray();
            })(),
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
            'id_marca' => (function () use ($conn, $schema) {
                if (! $schema->hasTable('marca')) {
                    return [];
                }
                $nombreCol = $schema->hasColumn('marca', 'nombre_marca') ? 'nombre_marca' : 'nombre';

                return $conn->table('marca')
                    ->selectRaw("id_marca as id, {$nombreCol} as nombre")
                    ->orderBy($nombreCol)
                    ->get()
                    ->toArray();
            })(),
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
            'tipo_emergencia' => (function () use ($conn, $schema) {
                if (! $schema->hasTable('tipo_emergencia')) {
                    return collect(LogisticaCrudUi::emergenciaFallbackOptions())
                        ->map(fn ($nombre) => (object) ['id' => $nombre, 'nombre' => $nombre])
                        ->all();
                }

                $nombreCol = $schema->hasColumn('tipo_emergencia', 'tipo_emergencia')
                    ? 'tipo_emergencia'
                    : 'nombre';

                return $conn->table('tipo_emergencia')
                    ->selectRaw("{$nombreCol} as id, {$nombreCol} as nombre")
                    ->orderBy($nombreCol)
                    ->get()
                    ->toArray();
            })(),
            'id_ubicacion' => (function () use ($conn, $schema) {
                if (! $schema->hasTable('ubicacion')) {
                    return [];
                }

                if ($schema->hasColumn('ubicacion', 'zona')) {
                    return $conn->table('ubicacion')
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
                        ->toArray();
                }

                return $conn->table('ubicacion')
                    ->select('id_ubicacion', 'descripcion')
                    ->orderByDesc('id_ubicacion')
                    ->limit(200)
                    ->get()
                    ->map(fn ($row) => (object) [
                        'id' => $row->id_ubicacion,
                        'nombre' => $row->descripcion ?? 'Ubicación',
                    ])
                    ->values()
                    ->toArray();
            })(),
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

    private function columnsForCrud(string $tabla, string $pk, string $seccion = ''): array
    {
        $schema = Schema::connection('logistica');
        $available = array_values(array_filter(
            $schema->getColumnListing($tabla),
            fn ($col) => ! in_array($col, [$pk, 'created_at', 'updated_at'], true)
        ));

        $preferred = LogisticaCrudUi::preferredColumns($seccion);
        if ($preferred === []) {
            return $available;
        }

        $ordered = [];
        foreach ($preferred as $column) {
            if (in_array($column, $available, true)) {
                $ordered[] = $column;
            }
        }

        foreach ($available as $column) {
            if (! in_array($column, $ordered, true)) {
                $ordered[] = $column;
            }
        }

        return $ordered;
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

        if ($seccion === 'paquete') {
            $columns = array_values(array_filter($columns, fn ($column) => $column !== 'imagen'));
        }

        if ($seccion === 'vehiculo') {
            $schema = Schema::connection('logistica');
            if ($schema->hasColumn('vehiculo', 'id_tipovehiculo')) {
                $columns = array_values(array_filter($columns, fn ($column) => $column !== 'id_tipo_vehiculo'));
            } elseif ($schema->hasColumn('vehiculo', 'id_tipo_vehiculo')) {
                $columns = array_values(array_filter($columns, fn ($column) => $column !== 'id_tipovehiculo'));
            }
        }

        return LogisticaCrudUi::orderColumns($seccion, $columns);
    }

    private function resolvePaqueteImagenUpload(Request $request): ?string
    {
        if (! $request->hasFile('foto_entrega')) {
            return null;
        }

        $file = $request->file('foto_entrega');
        if (! $file || ! $file->isValid()) {
            return null;
        }

        $mime = (string) $file->getMimeType();
        if (! str_starts_with($mime, 'image/')) {
            return null;
        }

        $contents = file_get_contents($file->getRealPath());

        return $contents !== false ? $contents : null;
    }

    private function vehiculosFlotaEnriquecidos()
    {
        $schema = Schema::connection('logistica');
        $conn = DB::connection('logistica');

        if (! $schema->hasTable('vehiculo')) {
            return collect();
        }

        $query = $conn->table('vehiculo as v');

        if ($schema->hasTable('marca') && $schema->hasColumn('vehiculo', 'id_marca')) {
            $nombreMarca = $schema->hasColumn('marca', 'nombre_marca') ? 'm.nombre_marca' : 'm.nombre';
            $query->leftJoin('marca as m', 'v.id_marca', '=', 'm.id_marca')
                ->addSelect(DB::raw("{$nombreMarca} as marca_nombre"));
        }

        if ($schema->hasTable('tipo_vehiculo')) {
            $tipoFk = $schema->hasColumn('vehiculo', 'id_tipovehiculo')
                ? 'id_tipovehiculo'
                : ($schema->hasColumn('vehiculo', 'id_tipo_vehiculo') ? 'id_tipo_vehiculo' : null);

            if ($tipoFk) {
                $tipoPk = $schema->hasColumn('tipo_vehiculo', 'id_tipovehiculo') ? 'id_tipovehiculo' : 'id_tipo_vehiculo';
                $tipoNombre = $schema->hasColumn('tipo_vehiculo', 'nombre_tipovehiculo')
                    ? 'tv.nombre_tipovehiculo'
                    : ($schema->hasColumn('tipo_vehiculo', 'nombre_tipo_vehiculo') ? 'tv.nombre_tipo_vehiculo' : 'tv.nombre');
                $query->leftJoin('tipo_vehiculo as tv', "v.{$tipoFk}", '=', "tv.{$tipoPk}")
                    ->addSelect(DB::raw("{$tipoNombre} as tipo_nombre"));
            }
        }

        return $query->select('v.*')->orderByDesc('v.updated_at')->limit(100)->get();
    }

    private function conductoresFlotaEnriquecidos()
    {
        $schema = Schema::connection('logistica');
        $conn = DB::connection('logistica');

        if (! $schema->hasTable('conductor')) {
            return collect();
        }

        $query = $conn->table('conductor as c');

        if ($schema->hasTable('tipo_licencia') && $schema->hasColumn('conductor', 'id_licencia')) {
            $licPk = $schema->hasColumn('tipo_licencia', 'id_tipo_licencia') ? 'id_tipo_licencia' : 'id_licencia';
            $licNombre = $schema->hasColumn('tipo_licencia', 'tipo_licencia')
                ? 'tl.tipo_licencia'
                : ($schema->hasColumn('tipo_licencia', 'nombre') ? 'tl.nombre' : null);

            if ($licNombre) {
                $query->leftJoin('tipo_licencia as tl', 'c.id_licencia', '=', "tl.{$licPk}")
                    ->addSelect(DB::raw("{$licNombre} as licencia_nombre"));
            }
        }

        return $query->select('c.*')->orderBy('c.nombre')->limit(100)->get();
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
        $tiposEmergencia = $this->getOptionsForColumn('tipo_emergencia');

        return view('fusion.modulos.logistica-solicitud-create', compact('tiposEmergencia'));
    }

    public function solicitudStore(Request $request): RedirectResponse
    {
        FusionModuloAccess::assertLogisticaWrite();
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
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
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

        $destinoPayload = [
            'comunidad' => $data['comunidad'],
            'provincia' => $data['provincia'],
            'direccion' => $data['direccion'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (Schema::connection('logistica')->hasColumn('destino', 'latitud') && isset($data['latitud'])) {
            $destinoPayload['latitud'] = $data['latitud'];
        }
        if (Schema::connection('logistica')->hasColumn('destino', 'longitud') && isset($data['longitud'])) {
            $destinoPayload['longitud'] = $data['longitud'];
        }

        $destinoId = $conn->table('destino')->insertGetId($destinoPayload, 'id_destino');

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
        $solicitudes = LogisticaOperativa::solicitudesOperativas();
        $totalDemoOcultos = DB::connection('logistica')->table('solicitud')
            ->where('codigo_seguimiento', 'like', 'LOG-DEMO-%')
            ->count();
        $vistaIntegrada = AccessControl::vistaIntegradaModulos(auth()->user());
        $filtroInicial = request()->query('filtro');

        return view('fusion.modulos.logistica-solicitudes', compact('solicitudes', 'totalDemoOcultos', 'vistaIntegrada', 'filtroInicial'));
    }

    public function paquetes(): View
    {
        $paquetes = LogisticaOperativa::paquetesOperativos();
        $vistaIntegrada = AccessControl::vistaIntegradaModulos(auth()->user());
        $filtroInicial = request()->query('filtro');

        return view('fusion.modulos.logistica-paquetes', compact('paquetes', 'vistaIntegrada', 'filtroInicial'));
    }

    public function flota(): View
    {
        $schema = Schema::connection('logistica');

        $vehiculos = $this->vehiculosFlotaEnriquecidos();
        $conductores = $this->conductoresFlotaEnriquecidos();

        $vehiculoTieneMarca = $schema->hasTable('vehiculo') && $schema->hasColumn('vehiculo', 'id_marca');
        $vehiculoTieneTipo = $schema->hasTable('vehiculo')
            && ($schema->hasColumn('vehiculo', 'id_tipovehiculo') || $schema->hasColumn('vehiculo', 'id_tipo_vehiculo'));
        $vehiculoTieneModelo = $schema->hasTable('vehiculo') && $schema->hasColumn('vehiculo', 'modelo');
        $vehiculoTieneAnio = $schema->hasTable('vehiculo') && $schema->hasColumn('vehiculo', 'anio');
        $vehiculoTieneCapacidad = $schema->hasTable('vehiculo') && $schema->hasColumn('vehiculo', 'capacidad');

        $conductorTieneCi = $schema->hasTable('conductor') && $schema->hasColumn('conductor', 'ci');
        $conductorTieneTelefono = $schema->hasTable('conductor') && $schema->hasColumn('conductor', 'telefono');
        $conductorTieneEmail = $schema->hasTable('conductor') && $schema->hasColumn('conductor', 'email');
        $conductorTieneLicencia = $schema->hasTable('conductor') && $schema->hasColumn('conductor', 'id_licencia');

        return view('fusion.modulos.logistica-flota', compact(
            'vehiculos',
            'conductores',
            'vehiculoTieneMarca',
            'vehiculoTieneTipo',
            'vehiculoTieneModelo',
            'vehiculoTieneAnio',
            'vehiculoTieneCapacidad',
            'conductorTieneCi',
            'conductorTieneTelefono',
            'conductorTieneEmail',
            'conductorTieneLicencia',
        ));
    }

    public function configuracion(): View
    {
        return view('fusion.modulos.logistica-configuracion');
    }

    public function mapa(): View
    {
        $marcadores = LogisticaMapa::marcadoresOperativos();
        $origen = LogisticaMapa::origenAlmacen();

        return view('fusion.modulos.logistica-mapa', compact('marcadores', 'origen'));
    }

    public function tracking(int $id): View
    {
        $datos = LogisticaMapa::datosTracking($id);
        abort_if($datos === null, 404);

        return view('fusion.modulos.logistica-tracking', [
            'paquete' => $datos['paquete'],
            'historial' => $datos['historial'],
            'points' => $datos['points'],
            'waypoints' => $datos['waypoints'],
            'origen' => $datos['origen'],
            'destino' => $datos['destino'],
        ]);
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

            $filas = $query->get($columnas);
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
        FusionModuloAccess::assertLogisticaWrite();
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $columns = $this->columnsForCrud($config['tabla'], $config['pk'], $seccion);
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
            'tieneFotoEntrega' => false,
        ]);
    }

    public function crudStore(Request $request, string $seccion): RedirectResponse
    {
        FusionModuloAccess::assertLogisticaWrite();
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $columns = $this->columnsForCrud($config['tabla'], $config['pk'], $seccion);
        $columns = array_values(array_filter($columns, fn ($column) => $column !== 'imagen'));
        $data = $this->normalizeCrudPayload($tabla, $request->only($columns));

        if ($config['tabla'] === 'paquete') {
            $imagen = $this->resolvePaqueteImagenUpload($request);
            if ($imagen !== null && Schema::connection('logistica')->hasColumn('paquete', 'imagen')) {
                $data['imagen'] = $imagen;
            }
        }

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

        return redirect()
            ->route(LogisticaCrudUi::listRouteName($seccion), LogisticaCrudUi::listRouteParams($seccion))
            ->with('success', 'Registro creado correctamente.');
    }

    public function crudEdit(string $seccion, int $id): View
    {
        FusionModuloAccess::assertLogisticaWrite();
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $columns = $this->columnsForCrud($config['tabla'], $config['pk'], $seccion);
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

        $tieneFotoEntrega = $seccion === 'paquete'
            && Schema::connection('logistica')->hasColumn('paquete', 'imagen')
            && ! empty($registro->imagen);

        return view('fusion.modulos.logistica-crud-form', [
            'seccion' => $seccion,
            'tituloSeccion' => $config['titulo'],
            'tabla' => $config['tabla'],
            'primaryKey' => $config['pk'],
            'columns' => $columns,
            'options' => $options,
            'registro' => $registro,
            'tieneFotoEntrega' => $tieneFotoEntrega,
        ]);
    }

    public function crudUpdate(Request $request, string $seccion, int $id): RedirectResponse
    {
        FusionModuloAccess::assertLogisticaWrite();
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $columns = $this->columnsForCrud($config['tabla'], $config['pk'], $seccion);
        $columns = array_values(array_filter($columns, fn ($column) => $column !== 'imagen'));
        $data = $this->normalizeCrudPayload($tabla, $request->only($columns));

        if ($config['tabla'] === 'paquete') {
            $imagen = $this->resolvePaqueteImagenUpload($request);
            if ($imagen !== null && Schema::connection('logistica')->hasColumn('paquete', 'imagen')) {
                $data['imagen'] = $imagen;
            }
        }

        if (Schema::connection('logistica')->hasColumn($tabla, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::connection('logistica')
            ->table($tabla)
            ->where($config['pk'], $id)
            ->update($data);

        return redirect()
            ->route(LogisticaCrudUi::listRouteName($seccion), LogisticaCrudUi::listRouteParams($seccion))
            ->with('success', 'Registro actualizado correctamente.');
    }

    public function crudDestroy(string $seccion, int $id): RedirectResponse
    {
        FusionModuloAccess::assertLogisticaWrite();
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        DB::connection('logistica')
            ->table($config['tabla'])
            ->where($config['pk'], $id)
            ->delete();

        return redirect()
            ->route(LogisticaCrudUi::listRouteName($seccion), LogisticaCrudUi::listRouteParams($seccion))
            ->with('success', 'Registro eliminado correctamente.');
    }
}
