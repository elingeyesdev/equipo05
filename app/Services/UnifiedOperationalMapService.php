<?php

namespace App\Services;

use App\Support\LogisticaMapa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Incendios\Models\Biomasa;
use Modules\Incendios\Models\FocoIncendio;
use Modules\Incendios\Services\FirmsDataService;
use Modules\Rescate\Models\Center;
use Modules\Rescate\Services\Fire\MapaCampoDataService;

/**
 * Agrega capas geográficas de los siete módulos operativos para el mapa territorial admin.
 */
class UnifiedOperationalMapService
{
    /** @var list<string> */
    public const LAYER_KEYS = [
        'incendios_firms',
        'incendios_registrados',
        'incendios_biomasas',
        'rescate_hallazgos',
        'rescate_liberaciones',
        'rescate_centros',
        'logistica_entregas',
        'cuadrillas_equipos',
        'cuadrillas_reportes',
        'voluntarios_ayudas',
        'inventario_sitios',
    ];

    public function __construct(
        private readonly ?FirmsDataService $firmsDataService = null,
        private readonly ?MapaCampoDataService $mapaCampoDataService = null,
    ) {}

    /** @return array<string, int> */
    public function summary(): array
    {
        $layers = $this->buildLayers(self::LAYER_KEYS);

        return collect($layers)
            ->map(fn (array $items) => count($items))
            ->all();
    }

    /**
     * @param  list<string>|null  $only
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function buildLayers(?array $only = null): array
    {
        $keys = $only !== null && $only !== []
            ? array_values(array_intersect(self::LAYER_KEYS, $only))
            : self::LAYER_KEYS;

        $layers = [];
        foreach ($keys as $key) {
            $layers[$key] = match ($key) {
                'incendios_firms' => $this->layerIncendiosFirms(),
                'incendios_registrados' => $this->layerIncendiosRegistrados(),
                'incendios_biomasas' => $this->layerIncendiosBiomasas(),
                'rescate_hallazgos' => $this->layerRescateHallazgos(),
                'rescate_liberaciones' => $this->layerRescateLiberaciones(),
                'rescate_centros' => $this->layerRescateCentros(),
                'logistica_entregas' => $this->layerLogisticaEntregas(),
                'cuadrillas_equipos' => $this->layerCuadrillasEquipos(),
                'cuadrillas_reportes' => $this->layerCuadrillasReportes(),
                'voluntarios_ayudas' => $this->layerVoluntariosAyudas(),
                'inventario_sitios' => $this->layerInventarioSitios(),
                default => [],
            };
        }

        return $layers;
    }

    /** @return array<string, mixed> */
    public function payload(?array $only = null): array
    {
        $layers = $this->buildLayers($only);

        return [
            'generated_at' => now()->toIso8601String(),
            'center' => ['lat' => -17.8857, 'lng' => -60.7556, 'zoom' => 8],
            'origen_logistica' => LogisticaMapa::origenAlmacen(),
            'summary' => collect($layers)->map(fn ($items) => count($items))->all(),
            'layers' => $layers,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    private function layerIncendiosFirms(): array
    {
        try {
            $service = $this->firmsDataService ?? app(FirmsDataService::class);
            $response = $service->getFireData(days: 3, cluster: true);
            $data = $response['data'] ?? [];

            return collect($data)->map(function ($fire) {
                $lat = (float) ($fire['lat'] ?? $fire['latitude'] ?? 0);
                $lng = (float) ($fire['lng'] ?? $fire['longitude'] ?? 0);
                if (! $this->validCoord($lat, $lng)) {
                    return null;
                }

                return $this->point(
                    'incendios_firms',
                    $lat,
                    $lng,
                    'FIRMS · '.($fire['date'] ?? $fire['acq_date'] ?? 'satélite'),
                    '#dc2626',
                    'satellite',
                    'incendios',
                    null,
                    [
                        'confidence' => $fire['confidence'] ?? null,
                        'frp' => $fire['frp'] ?? null,
                        'cluster_size' => $fire['cluster_size'] ?? 1,
                    ]
                );
            })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerIncendiosRegistrados(): array
    {
        try {
            if (! Schema::connection('incendios')->hasTable('focos_incendios')) {
                return [];
            }

            return FocoIncendio::conCoordenadas()
                ->where('fecha', '>=', now()->subDays(90))
                ->orderByDesc('fecha')
                ->limit(500)
                ->get()
                ->map(function (FocoIncendio $f) {
                    $lat = (float) $f->latitude;
                    $lng = (float) $f->longitude;
                    if (! $this->validCoord($lat, $lng)) {
                        return null;
                    }

                    return $this->point(
                        'incendios_registrados',
                        $lat,
                        $lng,
                        'Foco #'.$f->id,
                        '#2563eb',
                        'fire',
                        'incendios',
                        route('incendios.focos-incendios.show', $f->id),
                        [
                            'ubicacion' => $f->ubicacion,
                            'intensidad' => $f->intensidad,
                            'fecha' => $f->fecha?->format('d/m/Y H:i'),
                        ]
                    );
                })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerIncendiosBiomasas(): array
    {
        try {
            if (! class_exists(Biomasa::class)) {
                return [];
            }

            return Biomasa::aprobadas()->with('tipoBiomasa')->limit(200)->get()->map(function (Biomasa $b) {
                $coords = is_string($b->coordenadas) ? json_decode($b->coordenadas, true) : $b->coordenadas;
                if (! is_array($coords) || $coords === []) {
                    return null;
                }
                $centroid = $this->polygonCentroid($coords);
                if ($centroid === null) {
                    return null;
                }

                return $this->point(
                    'incendios_biomasas',
                    $centroid['lat'],
                    $centroid['lng'],
                    'Biomasa #'.$b->id,
                    $b->tipoBiomasa->color ?? '#16a34a',
                    'tree',
                    'incendios',
                    route('incendios.biomasas.show', $b->id),
                    [
                        'tipo' => $b->tipoBiomasa->tipo_biomasa ?? null,
                        'ubicacion' => $b->ubicacion,
                        'polygon' => $coords,
                    ]
                );
            })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerRescateHallazgos(): array
    {
        try {
            $data = ($this->mapaCampoDataService ?? app(MapaCampoDataService::class))->build();

            return collect($data['reports'] ?? [])->map(function ($report) {
                $lat = (float) ($report['latitud'] ?? 0);
                $lng = (float) ($report['longitud'] ?? 0);
                if (! $this->validCoord($lat, $lng)) {
                    return null;
                }
                $id = $report['id'] ?? null;
                if ($id === 'simulado') {
                    return $this->point(
                        'rescate_hallazgos',
                        $lat,
                        $lng,
                        'Hallazgo demo',
                        '#15803d',
                        'paw',
                        'rescate',
                        route('rescate.reports.mapa-campo'),
                        ['urgencia' => $report['urgencia'] ?? null]
                    );
                }

                return $this->point(
                    'rescate_hallazgos',
                    $lat,
                    $lng,
                    'Hallazgo #'.$id,
                    '#15803d',
                    'paw',
                    'rescate',
                    route('rescate.reports.show', $id),
                    [
                        'urgencia' => $report['urgencia'] ?? null,
                        'direccion' => $report['direccion'] ?? null,
                    ]
                );
            })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerRescateLiberaciones(): array
    {
        try {
            $data = ($this->mapaCampoDataService ?? app(MapaCampoDataService::class))->build();

            return collect($data['releases'] ?? [])->map(function ($release) {
                $lat = (float) ($release['latitud'] ?? 0);
                $lng = (float) ($release['longitud'] ?? 0);
                if (! $this->validCoord($lat, $lng)) {
                    return null;
                }

                return $this->point(
                    'rescate_liberaciones',
                    $lat,
                    $lng,
                    'Liberación #'.($release['id'] ?? ''),
                    '#059669',
                    'leaf',
                    'rescate',
                    route('rescate.releases.show', $release['id'] ?? 0),
                    [
                        'especie' => $release['especie']['nombre'] ?? null,
                        'fecha' => $release['fecha'] ?? null,
                    ]
                );
            })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerRescateCentros(): array
    {
        try {
            if (! Schema::connection('rescate')->hasTable('centers')) {
                return [];
            }

            return Center::query()
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->orderBy('nombre')
                ->get()
                ->map(function (Center $c) {
                    $lat = (float) $c->latitud;
                    $lng = (float) $c->longitud;
                    if (! $this->validCoord($lat, $lng)) {
                        return null;
                    }

                    return $this->point(
                        'rescate_centros',
                        $lat,
                        $lng,
                        $c->nombre,
                        '#0891b2',
                        'hospital',
                        'rescate',
                        route('rescate.centers.show', $c->id),
                        ['direccion' => $c->direccion]
                    );
                })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerLogisticaEntregas(): array
    {
        try {
            return collect(LogisticaMapa::marcadoresOperativos())->map(function (array $m) {
                return $this->point(
                    'logistica_entregas',
                    (float) $m['lat'],
                    (float) $m['lng'],
                    $m['ref'] ?? 'Entrega',
                    match ($m['tipo'] ?? '') {
                        'entregada' => '#059669',
                        'en_ruta' => '#0891b2',
                        'aprobada' => '#4f46e5',
                        'rechazada' => '#dc3545',
                        default => '#ffc107',
                    },
                    'truck',
                    'logistica',
                    $m['tracking_url'] ?? route('logistica.mapa'),
                    [
                        'estado' => $m['tipo'] ?? null,
                        'comunidad' => $m['comunidad'] ?? null,
                        'solicitante' => $m['solicitante'] ?? null,
                    ]
                );
            })->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerCuadrillasEquipos(): array
    {
        try {
            if (! Schema::connection('cuadrillas')->hasTable('equipo')) {
                return [];
            }

            return DB::connection('cuadrillas')->table('equipo')
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->orderBy('nombre')
                ->get()
                ->map(function ($e) {
                    $lat = (float) $e->latitud;
                    $lng = (float) $e->longitud;
                    if (! $this->validCoord($lat, $lng)) {
                        return null;
                    }

                    return $this->point(
                        'cuadrillas_equipos',
                        $lat,
                        $lng,
                        $e->nombre ?? ('Equipo #'.$e->id_equipo),
                        '#7c3aed',
                        'users',
                        'cuadrillas',
                        route('cuadrillas.focos-calor'),
                        ['integrantes' => $e->cantidad_integrantes ?? null]
                    );
                })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerCuadrillasReportes(): array
    {
        try {
            if (! Schema::connection('cuadrillas')->hasTable('reporte')) {
                return [];
            }

            return DB::connection('cuadrillas')->table('reporte')
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->orderByDesc('fecha_hora')
                ->limit(300)
                ->get()
                ->map(function ($r) {
                    $lat = (float) $r->latitud;
                    $lng = (float) $r->longitud;
                    if (! $this->validCoord($lat, $lng)) {
                        return null;
                    }

                    return $this->point(
                        'cuadrillas_reportes',
                        $lat,
                        $lng,
                        $r->nombre_lugar ?? ('Reporte #'.$r->id_reporte),
                        '#ea580c',
                        'exclamation-triangle',
                        'cuadrillas',
                        route('cuadrillas.reportes'),
                        [
                            'reportante' => $r->nombre_reportante ?? null,
                            'fecha' => $r->fecha_hora ?? null,
                        ]
                    );
                })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerVoluntariosAyudas(): array
    {
        try {
            if (! Schema::connection('seguimiento')->hasTable('solicitudes_ayuda')) {
                return [];
            }

            return DB::connection('seguimiento')->table('solicitudes_ayuda')
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->orderByDesc('created_at')
                ->limit(300)
                ->get()
                ->map(function ($s) {
                    $lat = (float) $s->latitud;
                    $lng = (float) $s->longitud;
                    if (! $this->validCoord($lat, $lng)) {
                        return null;
                    }
                    $prioridad = strtolower((string) ($s->prioridad ?? 'medio'));
                    $color = match ($prioridad) {
                        'alta', 'high' => '#dc2626',
                        'baja', 'low' => '#64748b',
                        default => '#d97706',
                    };

                    return $this->point(
                        'voluntarios_ayudas',
                        $lat,
                        $lng,
                        'Ayuda #'.($s->id ?? ''),
                        $color,
                        'hands-helping',
                        'seguimiento',
                        route('seguimiento.ayudas-solicitadas'),
                        [
                            'prioridad' => $prioridad,
                            'estado' => $s->estado ?? null,
                            'tipo' => $s->tipo ?? null,
                        ]
                    );
                })->filter()->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function layerInventarioSitios(): array
    {
        $items = [];

        try {
            if (Schema::connection('inventario')->hasTable('almacenes')) {
                $hasCoords = Schema::connection('inventario')->hasColumn('almacenes', 'latitud');
                if ($hasCoords) {
                    $rows = DB::connection('inventario')->table('almacenes')
                        ->whereNotNull('latitud')
                        ->whereNotNull('longitud')
                        ->get();
                    foreach ($rows as $row) {
                        $lat = (float) $row->latitud;
                        $lng = (float) $row->longitud;
                        if ($this->validCoord($lat, $lng)) {
                            $items[] = $this->point(
                                'inventario_sitios',
                                $lat,
                                $lng,
                                'Almacén · '.($row->nombre ?? $row->id),
                                '#0d9488',
                                'warehouse',
                                'inventario',
                                route('inventario.almacene.index'),
                                ['tipo' => 'almacen', 'direccion' => $row->direccion ?? null]
                            );
                        }
                    }
                }
            }

            if (Schema::connection('inventario')->hasTable('puntos_recoleccion')) {
                $rows = DB::connection('inventario')->table('puntos_recoleccion')
                    ->whereNotNull('latitud')
                    ->whereNotNull('longitud')
                    ->get();
                foreach ($rows as $row) {
                    $lat = (float) $row->latitud;
                    $lng = (float) $row->longitud;
                    if ($this->validCoord($lat, $lng)) {
                        $items[] = $this->point(
                            'inventario_sitios',
                            $lat,
                            $lng,
                            'Punto · '.($row->nombre ?? $row->id),
                            '#14b8a6',
                            'map-pin',
                            'inventario',
                            route('inventario.puntos-recoleccion.index'),
                            ['tipo' => 'punto_recoleccion']
                        );
                    }
                }
            }
        } catch (\Throwable) {
            return $items;
        }

        return $items;
    }

    /** @param  array<string, mixed>|null  $meta */
    private function point(
        string $layer,
        float $lat,
        float $lng,
        string $label,
        string $color,
        string $icon,
        string $module,
        ?string $url,
        ?array $meta = null,
    ): array {
        return [
            'layer' => $layer,
            'lat' => $lat,
            'lng' => $lng,
            'label' => $label,
            'color' => $color,
            'icon' => $icon,
            'module' => $module,
            'url' => $url,
            'meta' => $meta ?? [],
        ];
    }

    private function validCoord(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180
            && ($lat != 0.0 || $lng != 0.0);
    }

    /** @param  array<int, array{0: float, 1: float}|array{lat: float, lng: float}>  $coords */
    private function polygonCentroid(array $coords): ?array
    {
        $sumLat = 0.0;
        $sumLng = 0.0;
        $n = 0;
        foreach ($coords as $point) {
            if (is_array($point) && count($point) >= 2) {
                $sumLat += (float) $point[0];
                $sumLng += (float) $point[1];
                $n++;
            }
        }

        if ($n === 0) {
            return null;
        }

        return ['lat' => $sumLat / $n, 'lng' => $sumLng / $n];
    }
}
