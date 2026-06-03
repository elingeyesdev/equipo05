<?php

namespace Modules\Incendios\Http\Controllers;

use Modules\Incendios\Models\Biomasa;
use Modules\Incendios\Models\FocoIncendio;
use Modules\Incendios\Services\BiomasaSpatialMatcher;
use Modules\Incendios\Models\Prediction;
use Modules\Incendios\Models\Simulacione;
use Modules\Incendios\Models\User;
use Modules\Incendios\Services\OpenMeteoService;
use Modules\Incendios\Services\FirmsDataService;
use Modules\Incendios\Services\DashboardMetricsService;
use Modules\Incendios\Exports\FiresActivityExport;
use Modules\Incendios\Exports\FiresActivityPdfExport;
use Modules\Incendios\Exports\BiomasasManagementExport;
use Modules\Incendios\Exports\BiomasasManagementPdfExport;
use Modules\Incendios\Exports\SimulationsEffectivenessExport;
use Modules\Incendios\Exports\SimulationsEffectivenessPdfExport;
use Modules\Incendios\Exports\PredictionsReportExport;
use Modules\Incendios\Exports\PredictionsReportPdfExport;
use Modules\Incendios\Support\ClimaActual;
use Modules\Incendios\Support\ClimaUbicaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with map, weather, and statistics.
     */
    public function index(Request $request, OpenMeteoService $weather, FirmsDataService $firms, DashboardMetricsService $metrics)
    {
        $climaUbicaciones = ClimaUbicaciones::all();

        if ($request->filled('clima')) {
            $climaUbicacionKey = ClimaUbicaciones::normalizeKey($request->query('clima'));
            session(['incendios_clima_ubicacion' => $climaUbicacionKey]);
        } else {
            $climaUbicacionKey = ClimaUbicaciones::normalizeKey(session('incendios_clima_ubicacion'));
        }

        $climaUbicacion = $climaUbicaciones[$climaUbicacionKey];
        $latitude = $climaUbicacion['lat'];
        $longitude = $climaUbicacion['lng'];

        $weatherData = $weather->getCurrentWeather($latitude, $longitude);
        $climaActual = ClimaActual::fromOpenMeteo($weatherData['data'] ?? null);
        
        // Get fire hotspots from Chiquitanía area (last 2 days for demo, clustered)
        // Area: west,south,east,north = -62.5,-18.5,-57.5,-14.5
        // Clustering radius: 20km (fires within 20km are grouped as one hotspot)
        $firesData = $firms->getFireData('VIIRS_NOAA20_NRT', '-62.5,-18.5,-57.5,-14.5', 2, true, 20.0);

        // Count biomasas APROBADAS
        $biomasasCount = 0;
        try {
            $biomasasCount = Biomasa::aprobadas()->count();
        } catch (\Throwable $e) {
            \Log::warning('Incendios dashboard: no se pudo contar biomasas', [
                'error' => $e->getMessage(),
            ]);
        }
        
        // Count active fires
        $firesCount = isset($firesData['data']) ? count($firesData['data']) : 0;

        // Get user info for permission checks
        $user = auth()->user();
        // Sistema unificado: con sesión en el main, todas las pestañas/acciones del panel están visibles; el "rol de contexto" es solo UI (sesión).
        $isAdmin = true;
        $userId = $user->id ?? $user->usuarioid ?? null;

        // Get dashboard metrics with caching
        $generalStats = [];
        $fireTrends = [];
        $fireHourly = [];
        $monthlyComparison = [];
        $riskAreas = [];
        $biomasaDistribution = [];
        $biomasaStatus = [];
        $simulationStats = [];
        $userActivity = [];

        try {
            $generalStats = $metrics->getGeneralStats($userId, $isAdmin);
            $fireTrends = $metrics->getFireTrends($userId, $isAdmin);
            $fireHourly = $metrics->getFireHourlyDistribution($userId, $isAdmin);
            $monthlyComparison = $metrics->getMonthlyFireComparison();
            $riskAreas = $metrics->getRiskAreasAnalysis();
            $biomasaDistribution = $metrics->getBiomasaDistribution($userId, $isAdmin);
            $biomasaStatus = $metrics->getBiomasaStatusDistribution($userId, $isAdmin);
            $simulationStats = $metrics->getSimulationStats($isAdmin);
            $userActivity = $metrics->getUserActivity($isAdmin);
        } catch (\Throwable $e) {
            \Log::warning('Incendios dashboard: metricas no disponibles en entorno integrado', [
                'error' => $e->getMessage(),
            ]);
        }

        return view('dashboard', [
            'weather' => $weatherData,
            'climaActual' => $climaActual ?? [],
            'climaUbicaciones' => $climaUbicaciones,
            'climaUbicacionKey' => $climaUbicacionKey,
            'climaUbicacionNombre' => $climaUbicacion['nombre'],
            'fires' => $firesData,
            'biomasasCount' => $biomasasCount,
            'firesCount' => $firesCount,
            'isAdmin' => $isAdmin,
            'generalStats' => $generalStats,
            'fireTrends' => $fireTrends,
            'fireHourly' => $fireHourly,
            'monthlyComparison' => $monthlyComparison,
            'riskAreas' => $riskAreas,
            'biomasaDistribution' => $biomasaDistribution,
            'biomasaStatus' => $biomasaStatus,
            'simulationStats' => $simulationStats,
            'userActivity' => $userActivity,
        ]);
    }

    /**
     * API endpoint to get biomasas as GeoJSON for map rendering.
     * Solo retorna biomasas APROBADAS para el mapa público.
     */
    public function getBiomasas()
    {
        // Solo mostrar biomasas aprobadas en el mapa del dashboard
        $biomasas = Biomasa::aprobadas()->with('tipoBiomasa')->get();

        $features = $biomasas->map(function ($biomasa) {
            // Parse coordenadas if it's a string
            $coords = is_string($biomasa->coordenadas) 
                ? json_decode($biomasa->coordenadas, true) 
                : $biomasa->coordenadas;

            // Invertir coordenadas de [lat,lng] a [lng,lat] para GeoJSON
            $coordsGeoJSON = [];
            if (is_array($coords)) {
                foreach ($coords as $point) {
                    if (is_array($point) && count($point) >= 2) {
                        // Invertir: de [lat, lng] a [lng, lat]
                        $coordsGeoJSON[] = [$point[1], $point[0]];
                    }
                }
            }

            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => [$coordsGeoJSON],
                ],
                'properties' => [
                    'id' => $biomasa->id,
                    'ubicacion' => $biomasa->ubicacion ?? 'Sin ubicación',
                    'area' => number_format(($biomasa->area_m2 ?? 0) / 1_000_000, 4) . ' km²',
                    'densidad' => $biomasa->densidad ?? 'N/A',
                    'tipo' => $biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A',
                    'color' => $biomasa->tipoBiomasa->color ?? '#28a745',
                    'fecha' => $biomasa->fecha_reporte 
                        ? $biomasa->fecha_reporte->format('d/m/Y') 
                        : ($biomasa->created_at ? $biomasa->created_at->format('d/m/Y') : 'N/A'),
                    'descripcion' => $biomasa->descripcion ?? '',
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    /**
     * Focos registrados en BD para el mapa del dashboard (manuales + importados).
     */
    public function getFocosForMap(Request $request)
    {
        $days = min(max((int) $request->query('days', 90), 1), 365);
        $since = now()->subDays($days);

        $focos = FocoIncendio::conCoordenadas()
            ->where('fecha', '>=', $since)
            ->orderByDesc('fecha')
            ->get()
            ->filter(fn (FocoIncendio $f) => $f->latitude !== null && $f->longitude !== null)
            ->map(fn (FocoIncendio $f) => [
                'id' => $f->id,
                'lat' => (float) $f->latitude,
                'lng' => (float) $f->longitude,
                'ubicacion' => $f->ubicacion,
                'intensidad' => (float) $f->intensidad,
                'fecha' => $f->fecha?->format('d/m/Y H:i'),
                'origen' => str_contains(mb_strtolower((string) $f->ubicacion), 'demo') ? 'demo' : 'registrado',
            ])
            ->values();

        return response()->json([
            'ok' => true,
            'count' => $focos->count(),
            'data' => $focos,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<FocoIncendio>
     */
    private function firesActivityQuery(Request $request)
    {
        $user = auth()->user();
        $isAdmin = true;

        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $intensidadMin = $request->input('intensidad_min');
        $intensidadMax = $request->input('intensidad_max');
        $mostrarDemo = $request->boolean('mostrar_demo');

        $query = FocoIncendio::whereBetween('fecha', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])
            ->with(['biomasa.tipoBiomasa', 'reporter']);

        if (! $mostrarDemo) {
            $query->operativos();
        }

        if ($intensidadMin) {
            $query->where('intensidad', '>=', $intensidadMin);
        }
        if ($intensidadMax) {
            $query->where('intensidad', '<=', $intensidadMax);
        }

        if (! $isAdmin) {
            $query->whereHas('biomasa', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

    /**
     * Reporte de Actividad de Focos de Incendio
     */
    public function firesActivityReport(Request $request)
    {
        $isAdmin = true;

        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $intensidadMin = $request->input('intensidad_min');
        $intensidadMax = $request->input('intensidad_max');
        $mostrarDemo = $request->boolean('mostrar_demo');

        $fires = $this->firesActivityQuery($request)->orderBy('fecha', 'desc')->get();
        $fires = app(BiomasaSpatialMatcher::class)->enrichFocos($fires);

        // Estadísticas
        $byDate = $fires->groupBy(fn($fire) => $fire->fecha->format('Y-m-d'))
            ->map(fn($group) => $group->count())
            ->sortKeys()
            ->take(30);

        // Si no hay datos, agregar un punto vacío para evitar errores
        if ($byDate->isEmpty()) {
            $byDate = collect([now()->format('Y-m-d') => 0]);
        }

        $statistics = [
            'total' => $fires->count(),
            'avg_intensity' => $fires->count() > 0 ? round($fires->avg('intensidad'), 2) : 0,
            'max_intensity' => $fires->count() > 0 ? $fires->max('intensidad') : 0,
            'min_intensity' => $fires->count() > 0 ? $fires->min('intensidad') : 0,
            'by_date' => $byDate,
        ];

        $filters = [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'intensidad_min' => $intensidadMin,
            'intensidad_max' => $intensidadMax,
            'mostrar_demo' => $mostrarDemo,
        ];

        return view('reports.fires_activity', compact('fires', 'statistics', 'filters', 'isAdmin'));
    }

    /**
     * Exportar Reporte de Focos de Incendio a Excel
     */
    public function firesActivityExportExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $intensidadMin = $request->input('intensidad_min');
        $intensidadMax = $request->input('intensidad_max');

        $fires = app(BiomasaSpatialMatcher::class)->enrichFocos(
            $this->firesActivityQuery($request)->orderBy('fecha', 'desc')->get()
        );

        $statistics = [
            'total' => $fires->count(),
            'avg_intensity' => round($fires->avg('intensidad'), 2),
            'max_intensity' => $fires->max('intensidad'),
            'min_intensity' => $fires->min('intensidad'),
        ];

        $filters = compact('fechaInicio', 'fechaFin', 'intensidadMin', 'intensidadMax');

        $export = new FiresActivityExport($fires, $filters);
        return $export->export();
    }

    /**
     * Exportar Reporte de Focos de Incendio a PDF
     */
    public function firesActivityExportPdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));
        $intensidadMin = $request->input('intensidad_min');
        $intensidadMax = $request->input('intensidad_max');

        $fires = app(BiomasaSpatialMatcher::class)->enrichFocos(
            $this->firesActivityQuery($request)->orderBy('fecha', 'desc')->get()
        );

        $statistics = [
            'total' => $fires->count(),
            'avg_intensity' => $fires->count() > 0 ? round($fires->avg('intensidad'), 2) : 0,
            'max_intensity' => $fires->count() > 0 ? $fires->max('intensidad') : 0,
            'min_intensity' => $fires->count() > 0 ? $fires->min('intensidad') : 0,
        ];

        $filters = compact('fechaInicio', 'fechaFin', 'intensidadMin', 'intensidadMax');

        $export = new FiresActivityPdfExport($fires, $filters, $statistics);
        return $export->export();
    }

    /**
     * Reporte de Gestión de Biomasas
     */
    public function biomasasManagementReport(Request $request)
    {
        $user = auth()->user();
        $isAdmin = true;

        // Filtros
        $estado = $request->input('estado');
        $tipoBiomasaId = $request->input('tipo_biomasa_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Query base
        $query = Biomasa::with(['tipoBiomasa', 'user', 'aprobadaPor']);

        // Voluntarios solo ven sus propias biomasas
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        // Aplicar filtros
        if ($estado) {
            $query->where('estado', $estado);
        }
        if ($tipoBiomasaId) {
            $query->where('tipo_biomasa_id', $tipoBiomasaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $biomasas = $query->orderBy('created_at', 'desc')->get();

        // Estadísticas
        $statistics = [
            'total' => $biomasas->count(),
            'aprobadas' => $biomasas->where('estado', 'aprobada')->count(),
            'pendientes' => $biomasas->where('estado', 'pendiente')->count(),
            'rechazadas' => $biomasas->where('estado', 'rechazada')->count(),
            'tasa_aprobacion' => $biomasas->count() > 0 
                ? round(($biomasas->where('estado', 'aprobada')->count() / $biomasas->count()) * 100, 2)
                : 0,
            'area_total_ha' => round($biomasas->sum('area_m2') / 10000, 2),
        ];

        // Calcular tiempo promedio de revisión (solo para biomasas revisadas)
        $biomasasRevisadas = $biomasas->whereIn('estado', ['aprobada', 'rechazada'])
            ->where('fecha_revision', '!=', null);
        
        if ($biomasasRevisadas->count() > 0) {
            $tiemposRevision = $biomasasRevisadas->map(function ($biomasa) {
                return $biomasa->created_at->diffInHours($biomasa->fecha_revision);
            });
            $statistics['tiempo_promedio_revision_horas'] = round($tiemposRevision->avg(), 2);
        } else {
            $statistics['tiempo_promedio_revision_horas'] = 0;
        }

        $filters = compact('estado', 'tipoBiomasaId', 'fechaInicio', 'fechaFin');

        return view('reports.biomasas_management', compact('biomasas', 'statistics', 'filters', 'isAdmin'));
    }

    /**
     * Reporte de Efectividad de Simulaciones
     */
    public function simulationsEffectivenessReport(Request $request)
    {
        $user = auth()->user();
        $isAdmin = true;

        // Filtros
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $fireRiskMin = $request->input('fire_risk_min');
        $fireRiskMax = $request->input('fire_risk_max');

        // Query base
        $query = Simulacione::with('admin');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }
        if ($fireRiskMin) {
            $query->where('fire_risk', '>=', $fireRiskMin);
        }
        if ($fireRiskMax) {
            $query->where('fire_risk', '<=', $fireRiskMax);
        }

        $simulations = $query->orderBy('fecha', 'desc')->get();

        // Estadísticas
        $statistics = [
            'total' => $simulations->count(),
            'avg_duration' => round($simulations->avg('duracion'), 2),
            'avg_fire_risk' => round($simulations->avg('fire_risk'), 2),
            'total_volunteers' => $simulations->sum('num_voluntarios_enviados'),
            'avg_active_fires' => round($simulations->avg('focos_activos'), 2),
        ];

        // Correlaciones ambientales
        $correlations = null;
        if ($simulations->count() > 1) {
            $correlations = [
                'temp_vs_risk' => $this->calculateCorrelation(
                    $simulations->pluck('temperature')->toArray(),
                    $simulations->pluck('fire_risk')->toArray()
                ),
                'humidity_vs_risk' => $this->calculateCorrelation(
                    $simulations->pluck('humidity')->toArray(),
                    $simulations->pluck('fire_risk')->toArray()
                ),
                'wind_vs_risk' => $this->calculateCorrelation(
                    $simulations->pluck('wind_speed')->toArray(),
                    $simulations->pluck('fire_risk')->toArray()
                ),
            ];
        }

        // Estrategias más utilizadas
        $allStrategies = [];
        foreach ($simulations as $sim) {
            if ($sim->mitigation_strategies) {
                $strategies = is_string($sim->mitigation_strategies) 
                    ? json_decode($sim->mitigation_strategies, true) 
                    : $sim->mitigation_strategies;
                if (is_array($strategies)) {
                    foreach ($strategies as $strategy) {
                        $strategyName = is_array($strategy) ? ($strategy['name'] ?? 'Unknown') : $strategy;
                        $allStrategies[] = $strategyName;
                    }
                }
            }
        }
        $statistics['top_strategies'] = array_count_values($allStrategies);
        arsort($statistics['top_strategies']);
        $statistics['top_strategies'] = array_slice($statistics['top_strategies'], 0, 5, true);

        $filters = compact('fechaInicio', 'fechaFin', 'fireRiskMin', 'fireRiskMax');

        return view('reports.simulations_effectiveness', compact('simulations', 'statistics', 'correlations', 'filters', 'isAdmin'));
    }

    /**
     * Helper: Calculate Pearson correlation coefficient
     */
    private function calculateCorrelation(array $x, array $y): ?float
    {
        $n = count($x);
        if ($n === 0 || $n !== count($y)) {
            return null;
        }

        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumX2 = 0;
        $sumY2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumX2 += $x[$i] * $x[$i];
            $sumY2 += $y[$i] * $y[$i];
        }

        $numerator = ($n * $sumXY) - ($sumX * $sumY);
        $denominator = sqrt((($n * $sumX2) - ($sumX * $sumX)) * (($n * $sumY2) - ($sumY * $sumY)));

        if ($denominator == 0) {
            return null;
        }

        return round($numerator / $denominator, 4);
    }

    /**
     * Export Biomasas Management Report to Excel
     */
    public function biomasasManagementExportExcel(Request $request)
    {
        $user = auth()->user();
        $isAdmin = true;

        // Filtros
        $estado = $request->input('estado');
        $tipoBiomasaId = $request->input('tipo_biomasa_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        // Query base
        $query = Biomasa::with(['tipoBiomasa', 'user', 'aprobadaPor']);

        // Voluntarios solo ven sus propias biomasas
        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        // Aplicar filtros
        if ($estado) {
            $query->where('estado', $estado);
        }
        if ($tipoBiomasaId) {
            $query->where('tipo_biomasa_id', $tipoBiomasaId);
        }
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
        }

        $biomasas = $query->orderBy('created_at', 'desc')->get();

        $filters = compact('estado', 'tipoBiomasaId', 'fechaInicio', 'fechaFin');
        $export = new \Modules\Incendios\Exports\BiomasasManagementExport($biomasas, $filters);
        return $export->export();
    }

    /**
     * Export Biomasas Management Report to PDF
     */
    public function biomasasManagementExportPdf(Request $request)
    {
        $user = auth()->user();
        $isAdmin = true;

        $estado = $request->input('estado');
        $tipoBiomasaId = $request->input('tipo_biomasa_id');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Biomasa::with(['tipoBiomasa', 'user', 'aprobadaPor']);

        if (!$isAdmin) {
            $query->where('user_id', $user->id);
        }

        if ($estado) $query->where('estado', $estado);
        if ($tipoBiomasaId) $query->where('tipo_biomasa_id', $tipoBiomasaId);
        if ($fechaInicio && $fechaFin) $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);

        $biomasas = $query->orderBy('created_at', 'desc')->get();

        $statistics = [
            'total' => $biomasas->count(),
            'approved' => $biomasas->where('estado', 'aprobada')->count(),
            'pending' => $biomasas->where('estado', 'pendiente')->count(),
            'rejected' => $biomasas->where('estado', 'rechazada')->count(),
        ];

        $filters = compact('estado', 'tipoBiomasaId', 'fechaInicio', 'fechaFin');
        $export = new BiomasasManagementPdfExport($biomasas, $filters, $statistics);
        return $export->export();
    }

    /**
     * Export Simulations Effectiveness Report to Excel
     */
    public function simulationsEffectivenessExportExcel(Request $request)
    {
        // Filtros
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $fireRiskMin = $request->input('fire_risk_min');
        $fireRiskMax = $request->input('fire_risk_max');

        // Query base
        $query = Simulacione::with('admin');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }
        if ($fireRiskMin) {
            $query->where('fire_risk', '>=', $fireRiskMin);
        }
        if ($fireRiskMax) {
            $query->where('fire_risk', '<=', $fireRiskMax);
        }

        $simulations = $query->orderBy('fecha', 'desc')->get();

        $filters = compact('fechaInicio', 'fechaFin', 'fireRiskMin', 'fireRiskMax');
        $export = new \Modules\Incendios\Exports\SimulationsEffectivenessExport($simulations, $filters);
        return $export->export();
    }

    /**
     * Export Simulations Effectiveness Report to PDF
     */
    public function simulationsEffectivenessExportPdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $fireRiskMin = $request->input('fire_risk_min');
        $fireRiskMax = $request->input('fire_risk_max');

        $query = Simulacione::with('admin');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        }
        if ($fireRiskMin) $query->where('fire_risk', '>=', $fireRiskMin);
        if ($fireRiskMax) $query->where('fire_risk', '<=', $fireRiskMax);

        $simulations = $query->orderBy('fecha', 'desc')->get();

        $statistics = [
            'total' => $simulations->count(),
            'avg_risk' => $simulations->count() > 0 ? round($simulations->avg('fire_risk'), 2) : 0,
            'total_volunteers' => $simulations->sum('num_voluntarios_enviados'),
            'avg_duration' => $simulations->count() > 0 ? round($simulations->avg('duracion'), 0) : 0,
        ];

        $filters = compact('fechaInicio', 'fechaFin', 'fireRiskMin', 'fireRiskMax');
        $export = new SimulationsEffectivenessPdfExport($simulations, $filters, $statistics);
        return $export->export();
    }

    /**
     * Display Predictions Report
     */
    public function predictionsReport(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $riskMin = $request->input('risk_min');
        $riskMax = $request->input('risk_max');

        $query = Prediction::with('focoIncendio');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('predicted_at', [$fechaInicio, $fechaFin]);
        }
        if ($riskMin) {
            $query->whereRaw('CAST(meta->\'$.fire_risk_index\' AS DECIMAL(5,2)) >= ?', [$riskMin * 100]);
        }
        if ($riskMax) {
            $query->whereRaw('CAST(meta->\'$.fire_risk_index\' AS DECIMAL(5,2)) <= ?', [$riskMax * 100]);
        }

        $predictions = $query->orderBy('predicted_at', 'desc')->get();

        // Calculate statistics
        $statistics = [
            'total' => $predictions->count(),
            'avg_risk' => $predictions->count() > 0 ? $predictions->avg(function($p) {
                $riskIndex = $p->meta['fire_risk_index'] ?? 0;
                return $riskIndex / 100;
            }) : 0,
            'total_area' => $predictions->sum(function($p) {
                $path = $p->path ?? [];
                $maxArea = 0;
                if (is_array($path)) {
                    foreach ($path as $point) {
                        if (isset($point['affected_area_km2'])) {
                            $maxArea = max($maxArea, $point['affected_area_km2']);
                        }
                    }
                }
                return $maxArea;
            }),
            'avg_path_points' => $predictions->count() > 0 ? $predictions->avg(function($p) {
                return count($p->path ?? []);
            }) : 0,
        ];

        // Risk distribution
        $riskDistribution = [
            'high' => $predictions->filter(function($p) {
                $riskIndex = $p->meta['fire_risk_index'] ?? 0;
                return ($riskIndex / 100) >= 0.7;
            })->count(),
            'medium' => $predictions->filter(function($p) {
                $riskIndex = $p->meta['fire_risk_index'] ?? 0;
                $risk = $riskIndex / 100;
                return $risk >= 0.4 && $risk < 0.7;
            })->count(),
            'low' => $predictions->filter(function($p) {
                $riskIndex = $p->meta['fire_risk_index'] ?? 0;
                return ($riskIndex / 100) < 0.4;
            })->count(),
        ];
        $statistics['risk_distribution'] = $riskDistribution;

        return view('reports.predictions_report', compact('predictions', 'statistics'));
    }

    /**
     * Export Predictions Report to Excel/CSV
     */
    public function predictionsReportExportExcel(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $riskMin = $request->input('risk_min');
        $riskMax = $request->input('risk_max');

        $query = Prediction::with('focoIncendio');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('predicted_at', [$fechaInicio, $fechaFin]);
        }
        if ($riskMin) {
            $query->whereRaw('CAST(meta->\'$.fire_risk_index\' AS DECIMAL(5,2)) >= ?', [$riskMin * 100]);
        }
        if ($riskMax) {
            $query->whereRaw('CAST(meta->\'$.fire_risk_index\' AS DECIMAL(5,2)) <= ?', [$riskMax * 100]);
        }

        $predictions = $query->orderBy('predicted_at', 'desc')->get();

        $filters = compact('fechaInicio', 'fechaFin', 'riskMin', 'riskMax');
        $export = new PredictionsReportExport($predictions, $filters);
        return $export->export();
    }

    /**
     * Export Predictions Report to PDF
     */
    public function predictionsReportExportPdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $riskMin = $request->input('risk_min');
        $riskMax = $request->input('risk_max');

        $query = Prediction::with('focoIncendio');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('predicted_at', [$fechaInicio, $fechaFin]);
        }
        if ($riskMin) {
            $query->whereRaw('CAST(meta->\'$.fire_risk_index\' AS DECIMAL(5,2)) >= ?', [$riskMin * 100]);
        }
        if ($riskMax) {
            $query->whereRaw('CAST(meta->\'$.fire_risk_index\' AS DECIMAL(5,2)) <= ?', [$riskMax * 100]);
        }

        $predictions = $query->orderBy('predicted_at', 'desc')->get();

        // Calculate statistics
        $statistics = [
            'total' => $predictions->count(),
            'avg_risk' => $predictions->count() > 0 ? $predictions->avg(function($p) {
                $riskIndex = $p->meta['fire_risk_index'] ?? 0;
                return $riskIndex / 100;
            }) : 0,
            'total_area' => $predictions->sum(function($p) {
                $path = $p->path ?? [];
                $maxArea = 0;
                if (is_array($path)) {
                    foreach ($path as $point) {
                        if (isset($point['affected_area_km2'])) {
                            $maxArea = max($maxArea, $point['affected_area_km2']);
                        }
                    }
                }
                return $maxArea;
            }),
            'avg_path_points' => $predictions->count() > 0 ? $predictions->avg(function($p) {
                return count($p->path ?? []);
            }) : 0,
        ];

        // Risk distribution
        $riskDistribution = [
            'high' => $predictions->filter(function($p) {
                $riskIndex = $p->meta['fire_risk_index'] ?? 0;
                return ($riskIndex / 100) >= 0.7;
            })->count(),
            'medium' => $predictions->filter(function($p) {
                $riskIndex = $p->meta['fire_risk_index'] ?? 0;
                $risk = $riskIndex / 100;
                return $risk >= 0.4 && $risk < 0.7;
            })->count(),
            'low' => $predictions->filter(function($p) {
                $riskIndex = $p->meta['fire_risk_index'] ?? 0;
                return ($riskIndex / 100) < 0.4;
            })->count(),
        ];
        $statistics['risk_distribution'] = $riskDistribution;

        $filters = compact('fechaInicio', 'fechaFin', 'riskMin', 'riskMax');
        $export = new PredictionsReportPdfExport($predictions, $filters, $statistics);
        return $export->export();
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache(DashboardMetricsService $metrics)
    {
        $metrics->clearCache(auth()->id());
        return response()->json(['success' => true, 'message' => 'Cache actualizado']);
    }
}
