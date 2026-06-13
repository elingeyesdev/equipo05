<?php

namespace App\Http\Controllers\Fusion;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccesoPublicoController extends Controller
{
    public function logisticaSolicitud(): View
    {
        return view('fusion.modulos.publico-logistica-solicitud');
    }

    public function logisticaSolicitudStore(Request $request): RedirectResponse
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

        return redirect()->route('publico.logistica.solicitud')
            ->with('success', 'Solicitud enviada correctamente.');
    }

    public function logisticaGaleria(): View
    {
        $conn = DB::connection('logistica');
        $schema = $conn->getSchemaBuilder();
        $paquetes = collect();

        if ($schema->hasTable('paquete')) {
            $query = $conn->table('paquete')
                ->leftJoin('solicitud', 'paquete.id_solicitud', '=', 'solicitud.id_solicitud')
                ->leftJoin('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
                ->select([
                    'paquete.id_paquete',
                    'paquete.codigo',
                    'paquete.fecha_entrega',
                    'destino.comunidad',
                ]);

            if ($schema->hasColumn('paquete', 'imagen')) {
                $query->addSelect('paquete.imagen');
            }

            if ($schema->hasColumn('paquete', 'updated_at')) {
                $query->orderByDesc('paquete.updated_at');
            } elseif ($schema->hasColumn('paquete', 'id_paquete')) {
                $query->orderByDesc('paquete.id_paquete');
            }

            $paquetes = $query->limit(24)->get();
        }

        return view('fusion.modulos.publico-logistica-galeria', compact('paquetes'));
    }

    public function cuadrillasMapa(): View
    {
        $countEquiposDesplegados = DB::connection('cuadrillas')
            ->table('equipo')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->count();

        $countReportes = DB::connection('cuadrillas')
            ->table('reporte')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->count();

        $ultimoReporteFecha = DB::connection('cuadrillas')
            ->table('reporte')
            ->whereNotNull('fecha_hora')
            ->max('fecha_hora');

        $ultimoReporte = 'N/A';
        if ($ultimoReporteFecha) {
            $ultimoReporte = Carbon::parse($ultimoReporteFecha)->format('d/m/Y');
        }

        return view('fusion.modulos.publico-cuadrillas-mapa', compact('countEquiposDesplegados', 'countReportes', 'ultimoReporte'));
    }

    public function cuadrillasReporte(): View
    {
        $tiposIncidente = DB::connection('cuadrillas')->table('tipo_incidente')->orderBy('nombre')->get();
        $nivelesGravedad = DB::connection('cuadrillas')->table('nivel_gravedad')->orderBy('id_nivel_gravedad')->get();

        return view('fusion.modulos.publico-cuadrillas-reporte', compact('tiposIncidente', 'nivelesGravedad'));
    }

    public function cuadrillasReporteStore(Request $request)
    {
        $request->validate([
            'nombre_reportante' => 'required|string|max:200',
            'telefono_contacto' => 'nullable|string|max:20',
            'nombre_lugar' => 'nullable|string|max:200',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'tipo_incidente_id' => 'nullable|integer',
            'gravedad_id' => 'nullable|integer',
            'comentario_adicional' => 'nullable|string',
        ]);

        $estadoPendiente = DB::connection('cuadrillas')
            ->table('estado_sistema')
            ->whereRaw('LOWER(tabla) = ?', ['reportes'])
            ->whereRaw('LOWER(codigo) = ?', ['pendiente'])
            ->first();

        $idReporte = DB::connection('cuadrillas')->table('reporte')->insertGetId([
            'nombre_reportante' => $request->nombre_reportante,
            'telefono_contacto' => $request->telefono_contacto,
            'fecha_hora' => now(),
            'nombre_lugar' => $request->nombre_lugar,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
            'tipo_incidente_id' => $request->tipo_incidente_id,
            'gravedad_id' => $request->gravedad_id,
            'comentario_adicional' => $request->comentario_adicional,
            'cant_bomberos' => 0,
            'cant_paramedicos' => 0,
            'cant_veterinarios' => 0,
            'cant_autoridades' => 0,
            'estado_id' => $estadoPendiente->id_estado_sistema ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_reporte');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reporte enviado exitosamente.',
                'id' => $idReporte,
            ], 201);
        }

        return redirect()->route('publico.cuadrillas.mapa')
            ->with('success', 'Reporte enviado exitosamente. Gracias por su colaboración.');
    }

    public function cuadrillasEquiposApi(): \Illuminate\Http\JsonResponse
    {
        try {
            $equipos = DB::connection('cuadrillas')->table('equipo')
                ->leftJoin('estado_sistema', 'equipo.estado_id', '=', 'estado_sistema.id_estado_sistema')
                ->select([
                    'equipo.id_equipo as id',
                    'equipo.nombre as nombre_equipo',
                    'equipo.cantidad_integrantes',
                    'equipo.latitud',
                    'equipo.longitud',
                    'estado_sistema.nombre as estado_nombre',
                    'estado_sistema.codigo as estado_codigo',
                    'estado_sistema.color as estado_color',
                ])
                ->whereNotNull('equipo.latitud')
                ->whereNotNull('equipo.longitud')
                ->get()
                ->map(function ($equipo) {
                    return [
                        'id' => $equipo->id,
                        'nombre_equipo' => $equipo->nombre_equipo,
                        'cantidad_integrantes' => $equipo->cantidad_integrantes ?? 0,
                        'ubicacion' => [
                            'coordinates' => [
                                (float) $equipo->longitud,
                                (float) $equipo->latitud
                            ]
                        ],
                        'estado' => $equipo->estado_nombre ? [
                            'nombre' => $equipo->estado_nombre,
                            'codigo' => $equipo->estado_codigo,
                            'color' => $equipo->estado_color
                        ] : null
                    ];
                });

            return response()->json($equipos);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener equipos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cuadrillasReportesApi(): \Illuminate\Http\JsonResponse
    {
        try {
            $reportes = DB::connection('cuadrillas')->table('reporte')
                ->leftJoin('tipo_incidente', 'reporte.tipo_incidente_id', '=', 'tipo_incidente.id_tipo_incidente')
                ->leftJoin('nivel_gravedad', 'reporte.gravedad_id', '=', 'nivel_gravedad.id_nivel_gravedad')
                ->leftJoin('estado_sistema', 'reporte.estado_id', '=', 'estado_sistema.id_estado_sistema')
                ->select([
                    'reporte.id_reporte as id',
                    'reporte.nombre_reportante',
                    'reporte.telefono_contacto',
                    'reporte.fecha_hora',
                    'reporte.nombre_lugar',
                    'reporte.latitud',
                    'reporte.longitud',
                    'reporte.comentario_adicional',
                    'tipo_incidente.nombre as tipo_incidente_nombre',
                    'nivel_gravedad.nombre as gravedad_nombre',
                    'estado_sistema.nombre as estado_nombre',
                    'estado_sistema.color as estado_color',
                ])
                ->whereNotNull('reporte.latitud')
                ->whereNotNull('reporte.longitud')
                ->get()
                ->map(function ($reporte) {
                    return [
                        'id' => $reporte->id,
                        'nombre_lugar' => $reporte->nombre_lugar,
                        'nombre_reportante' => $reporte->nombre_reportante,
                        'telefono_contacto' => $reporte->telefono_contacto,
                        'fecha_hora' => $reporte->fecha_hora,
                        'comentario_adicional' => $reporte->comentario_adicional,
                        'ubicacion' => [
                            'coordinates' => [
                                (float) $reporte->longitud,
                                (float) $reporte->latitud
                            ]
                        ],
                        'tipos_incidente' => $reporte->tipo_incidente_nombre ? [
                            'nombre' => $reporte->tipo_incidente_nombre
                        ] : null,
                        'niveles_gravedad' => $reporte->gravedad_nombre ? [
                            'nombre' => $reporte->gravedad_nombre
                        ] : null,
                        'estados_sistema' => $reporte->estado_nombre ? [
                            'nombre' => $reporte->estado_nombre,
                            'color' => $reporte->estado_color
                        ] : null
                    ];
                });

            return response()->json($reportes);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener reportes',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function seguimientoInfo(): View
    {
        $conn = DB::connection('seguimiento');
        $schema = Schema::connection('seguimiento');

        $stats = [
            'voluntarios_activos' => 0,
            'capacitaciones' => 0,
            'solicitudes_abiertas' => 0,
            'solicitudes_atendidas' => 0,
        ];

        $voluntarios = collect();
        $capacitaciones = collect();
        $actividad = collect();
        $solicitudesPorEstado = collect();

        if ($schema->hasTable('usuario')) {
            $voluntariosQuery = $conn->table('usuario')
                ->select(['nombre', 'apellido', 'activo', 'created_at']);

            if ($schema->hasColumn('usuario', 'administrador')) {
                $voluntariosQuery->where('administrador', false);
            }

            if ($schema->hasColumn('usuario', 'activo')) {
                $stats['voluntarios_activos'] = (clone $voluntariosQuery)->where('activo', true)->count();
            } else {
                $stats['voluntarios_activos'] = $voluntariosQuery->count();
            }

            $voluntarios = $voluntariosQuery
                ->orderByDesc('created_at')
                ->limit(24)
                ->get()
                ->map(fn ($v) => $this->mapVoluntarioPublico($v));
        }

        if ($schema->hasTable('capacitacion')) {
            $capacitaciones = $conn->table('capacitacion')
                ->when(
                    $schema->hasColumn('capacitacion', 'nombre'),
                    fn ($q) => $q->orderBy('nombre'),
                    fn ($q) => $q->orderByDesc('id_capacitacion')
                )
                ->limit(12)
                ->pluck('nombre')
                ->filter()
                ->values();

            $stats['capacitaciones'] = $schema->hasTable('capacitacion')
                ? $conn->table('capacitacion')->count()
                : $capacitaciones->count();
        }

        if ($schema->hasTable('solicitudes_ayuda') && $schema->hasColumn('solicitudes_ayuda', 'estado')) {
            $solicitudesPorEstado = $conn->table('solicitudes_ayuda')
                ->select('estado', DB::raw('count(*) as total'))
                ->groupBy('estado')
                ->get()
                ->mapWithKeys(fn ($row) => [
                    $this->etiquetaEstadoSolicitud($row->estado) => (int) $row->total,
                ]);

            $stats['solicitudes_abiertas'] = (int) $conn->table('solicitudes_ayuda')
                ->whereIn('estado', ['pendiente', 'en_proceso', 'en proceso', 'abierta'])
                ->count();

            $stats['solicitudes_atendidas'] = (int) $conn->table('solicitudes_ayuda')
                ->whereIn('estado', ['atendida', 'cerrada', 'completada'])
                ->count();
        }

        if ($schema->hasTable('chat_mensajes') && $schema->hasColumn('chat_mensajes', 'mensaje')) {
            $actividad = $conn->table('chat_mensajes')
                ->when($schema->hasColumn('chat_mensajes', 'created_at'), fn ($q) => $q->orderByDesc('created_at'))
                ->limit(5)
                ->get(['mensaje', 'created_at'])
                ->map(fn ($m) => (object) [
                    'texto' => Str::limit(trim((string) $m->mensaje), 160),
                    'fecha' => $m->created_at
                        ? Carbon::parse($m->created_at)->locale('es')->diffForHumans()
                        : null,
                ])
                ->filter(fn ($m) => $m->texto !== '');
        }

        return view('fusion.modulos.publico-seguimiento-voluntarios', compact(
            'stats',
            'voluntarios',
            'capacitaciones',
            'actividad',
            'solicitudesPorEstado'
        ));
    }

    private function mapVoluntarioPublico(object $row): object
    {
        $nombre = trim((string) ($row->nombre ?? ''));
        $apellido = trim((string) ($row->apellido ?? ''));
        $inicialApellido = $apellido !== '' ? mb_strtoupper(mb_substr($apellido, 0, 1)).'.' : '';

        return (object) [
            'nombre' => trim($nombre.' '.$inicialApellido) ?: 'Voluntario',
            'inicial' => mb_strtoupper(mb_substr($nombre ?: 'V', 0, 1)),
            'activo' => (bool) ($row->activo ?? true),
            'desde' => ! empty($row->created_at)
                ? Carbon::parse($row->created_at)->locale('es')->translatedFormat('F Y')
                : null,
        ];
    }

    private function etiquetaEstadoSolicitud(?string $estado): string
    {
        return match (Str::lower((string) $estado)) {
            'pendiente' => 'Pendientes',
            'en_proceso', 'en proceso' => 'En proceso',
            'atendida', 'cerrada', 'completada' => 'Atendidas',
            default => Str::title(str_replace('_', ' ', (string) $estado)),
        };
    }

    private function renderPublicTable(
        string $titulo,
        string $subtitulo,
        string $connection,
        string $tabla,
        string $pk
    ): View {
        $columnas = [];
        $filas = collect();
        $total = 0;

        if (Schema::connection($connection)->hasTable($tabla)) {
            $columnas = Schema::connection($connection)->getColumnListing($tabla);
            $columnas = array_slice($columnas, 0, 8);

            $query = DB::connection($connection)->table($tabla);
            if (Schema::connection($connection)->hasColumn($tabla, 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn($tabla, $pk)) {
                $query->orderByDesc($pk);
            }

            $filas = $query->limit(20)->get($columnas);
            $total = DB::connection($connection)->table($tabla)->count();
        }

        return view('fusion.modulos.acceso-publico', compact(
            'titulo',
            'subtitulo',
            'columnas',
            'filas',
            'total'
        ));
    }
}
