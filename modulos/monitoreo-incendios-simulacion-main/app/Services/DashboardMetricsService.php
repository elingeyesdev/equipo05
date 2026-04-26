<?php

namespace App\Services;

use App\Models\Biomasa;
use App\Models\FocoIncendio;
use App\Models\Simulacione;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * Cache duration in seconds (15 minutes)
     */
    protected int $cacheDuration = 900;

    /**
     * Get fire trends for the last 30 days
     * Returns daily fire counts grouped by date
     *
     * @param int|null $userId
     * @param bool $isAdmin
     * @return array
     */
    public function getFireTrends(?int $userId = null, bool $isAdmin = false): array
    {
        $cacheKey = 'dashboard_fire_trends_' . ($userId ?? 'all') . '_' . ($isAdmin ? 'admin' : 'user');

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($userId, $isAdmin) {
            $query = FocoIncendio::selectRaw('DATE(fecha) as date, COUNT(*) as count, AVG(intensidad) as avg_intensity')
                ->where('fecha', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date', 'asc');

            // Fires are public data from satellites, not filtered by user

            $data = $query->get();

            // Fill missing dates with zero counts
            $dates = [];
            $counts = [];
            $intensities = [];
            
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $record = $data->firstWhere('date', $date);
                
                $dates[] = now()->subDays($i)->format('d/m');
                $counts[] = $record ? $record->count : 0;
                $intensities[] = $record ? round($record->avg_intensity, 2) : 0;
            }

            // Since confidence is not in DB, we'll use intensity ranges as proxy
            $highConf = FocoIncendio::where('fecha', '>=', now()->subDays(7))
                ->where('intensidad', '>=', 3.0)
                ->count();
            $medConf = FocoIncendio::where('fecha', '>=', now()->subDays(7))
                ->whereBetween('intensidad', [1.5, 2.99])
                ->count();
            $lowConf = FocoIncendio::where('fecha', '>=', now()->subDays(7))
                ->where('intensidad', '<', 1.5)
                ->count();

            return [
                'labels' => $dates,
                'counts' => $counts,
                'avg_intensities' => $intensities,
                'high_confidence' => $highConf,
                'medium_confidence' => $medConf,
                'low_confidence' => $lowConf,
            ];
        });
    }

    /**
     * Get biomass distribution by type
     *
     * @param int|null $userId
     * @param bool $isAdmin
     * @return array
     */
    public function getBiomasaDistribution(?int $userId = null, bool $isAdmin = false): array
    {
        $cacheKey = 'dashboard_biomasa_dist_' . ($userId ?? 'all') . '_' . ($isAdmin ? 'admin' : 'user');

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($userId, $isAdmin) {
            $query = Biomasa::aprobadas()
                ->select('tipo_biomasa_id')
                ->selectRaw('SUM(area_m2) as total_area, COUNT(*) as count')
                ->with('tipoBiomasa:id,tipo_biomasa,color')
                ->groupBy('tipo_biomasa_id');

            // Volunteers see only their biomasas
            if (!$isAdmin && $userId) {
                $query->where('user_id', $userId);
            }

            $data = $query->get();

            $labels = [];
            $areas = [];
            $counts = [];
            $colors = [];

            foreach ($data as $item) {
                $labels[] = $item->tipoBiomasa->tipo_biomasa ?? 'Sin tipo';
                $areas[] = round($item->total_area / 10000, 2); // Convert to hectares
                $counts[] = $item->count;
                $colors[] = $item->tipoBiomasa->color ?? '#cccccc';
            }

            return [
                'labels' => $labels,
                'areas' => $areas,
                'counts' => $counts,
                'colors' => $colors,
            ];
        });
    }

    /**
     * Get biomass status distribution
     *
     * @param int|null $userId
     * @param bool $isAdmin
     * @return array
     */
    public function getBiomasaStatusDistribution(?int $userId = null, bool $isAdmin = false): array
    {
        $cacheKey = 'dashboard_biomasa_status_' . ($userId ?? 'all') . '_' . ($isAdmin ? 'admin' : 'user');

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($userId, $isAdmin) {
            $query = Biomasa::select('estado')
                ->selectRaw('COUNT(*) as count')
                ->groupBy('estado');

            if (!$isAdmin && $userId) {
                $query->where('user_id', $userId);
            }

            $data = $query->get();

            $labels = [];
            $counts = [];
            $colors = [
                'pendiente' => '#ffc107',
                'aprobada' => '#28a745',
                'rechazada' => '#dc3545',
            ];

            foreach ($data as $item) {
                $labels[] = ucfirst($item->estado);
                $counts[] = $item->count;
            }

            return [
                'labels' => $labels,
                'counts' => $counts,
                'colors' => array_values($colors),
            ];
        });
    }

    /**
     * Get simulation statistics
     *
     * @param bool $isAdmin
     * @return array
     */
    public function getSimulationStats(bool $isAdmin = false): array
    {
        $cacheKey = 'dashboard_simulation_stats_' . ($isAdmin ? 'admin' : 'user');

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($isAdmin) {
            $stats = Simulacione::selectRaw('
                COUNT(*) as total_simulations,
                AVG(fire_risk) as avg_fire_risk,
                AVG(duracion) as avg_duration,
                SUM(num_voluntarios_enviados) as total_volunteers,
                SUM(focos_activos) as total_active_fires
            ')->first();

            // Monthly simulation counts for the last 6 months
            $monthlyCounts = Simulacione::selectRaw('
                    DATE_TRUNC(\'month\', fecha) as month,
                    COUNT(*) as count
                ')
                ->where('fecha', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get();

            $months = [];
            $counts = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i)->format('Y-m');
                $record = $monthlyCounts->firstWhere('month', $month . '-01 00:00:00');
                
                $months[] = now()->subMonths($i)->locale('es')->isoFormat('MMM');
                $counts[] = $record ? $record->count : 0;
            }

            return [
                'total_simulations' => $stats->total_simulations ?? 0,
                'avg_fire_risk' => round($stats->avg_fire_risk ?? 0, 2),
                'avg_duration' => round($stats->avg_duration ?? 0, 0),
                'total_volunteers' => $stats->total_volunteers ?? 0,
                'total_active_fires' => $stats->total_active_fires ?? 0,
                'monthly_labels' => $months,
                'monthly_counts' => $counts,
            ];
        });
    }

    /**
     * Get user activity metrics (admin only)
     *
     * @param bool $isAdmin
     * @return array
     */
    public function getUserActivity(bool $isAdmin = false): array
    {
        if (!$isAdmin) {
            return [
                'labels' => [],
                'counts' => [],
            ];
        }

        $cacheKey = 'dashboard_user_activity_admin';

        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $topUsers = User::select('users.*')
                ->selectSub(function ($query) {
                    $query->selectRaw('COUNT(*)')
                        ->from('biomasas')
                        ->whereColumn('biomasas.user_id', 'users.id');
                }, 'biomasas_count')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('biomasas')
                        ->whereColumn('biomasas.user_id', 'users.id');
                })
                ->orderByDesc('biomasas_count')
                ->take(10)
                ->get();

            $labels = [];
            $counts = [];

            foreach ($topUsers as $user) {
                $labels[] = $user->name;
                $counts[] = $user->biomasas_count ?? 0;
            }

            return [
                'labels' => $labels,
                'counts' => $counts,
            ];
        });
    }

    /**
     * Get hourly fire distribution (last 7 days)
     */
    public function getFireHourlyDistribution(?int $userId = null, bool $isAdmin = false): array
    {
        $cacheKey = 'dashboard_fire_hourly_' . ($userId ?? 'all');
        
        return Cache::remember($cacheKey, $this->cacheDuration, function () {
            $data = FocoIncendio::where('fecha', '>=', now()->subDays(7))
                ->selectRaw('EXTRACT(HOUR FROM fecha) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
            
            $hours = range(0, 23);
            $counts = array_fill(0, 24, 0);
            
            foreach ($data as $item) {
                $counts[(int)$item->hour] = $item->count;
            }
            
            return [
                'labels' => array_map(fn($h) => sprintf('%02d:00', $h), $hours),
                'counts' => $counts,
            ];
        });
    }

    /**
     * Get monthly fire comparison (current vs previous)
     */
    public function getMonthlyFireComparison(): array
    {
        return Cache::remember('dashboard_fire_monthly_comp', $this->cacheDuration, function () {
            $currentMonth = FocoIncendio::whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->year)
                ->count();
            
            $previousMonth = FocoIncendio::whereMonth('fecha', now()->subMonth()->month)
                ->whereYear('fecha', now()->subMonth()->year)
                ->count();
            
            $lastYear = FocoIncendio::whereMonth('fecha', now()->month)
                ->whereYear('fecha', now()->subYear()->year)
                ->count();
            
            return [
                'current_month' => $currentMonth,
                'previous_month' => $previousMonth,
                'last_year_same_month' => $lastYear,
                'change_percentage' => $previousMonth > 0 ? round((($currentMonth - $previousMonth) / $previousMonth) * 100, 1) : 0,
            ];
        });
    }

    /**
     * Get risk areas analysis
     */
    public function getRiskAreasAnalysis(): array
    {
        return Cache::remember('dashboard_risk_areas', $this->cacheDuration, function () {
            try {
                // Top 5 biomasas con más área (proxy para riesgo)
                $riskAreas = Biomasa::aprobadas()
                    ->select('biomasas.id', 'biomasas.nombre', 'biomasas.area_m2', 'tipo_biomasas.tipo_biomasa')
                    ->join('tipo_biomasas', 'biomasas.tipo_biomasa_id', '=', 'tipo_biomasas.id')
                    ->orderByDesc('area_m2')
                    ->limit(5)
                    ->get();
                
                return [
                    'labels' => $riskAreas->pluck('nombre')->toArray(),
                    'counts' => $riskAreas->pluck('area_m2')->map(fn($area) => round($area / 10000, 1))->toArray(),
                    'types' => $riskAreas->pluck('tipo_biomasa')->toArray(),
                ];
            } catch (\Exception $e) {
                \Log::error('Error en getRiskAreasAnalysis: ' . $e->getMessage());
                return [
                    'labels' => [],
                    'counts' => [],
                    'types' => [],
                ];
            }
        });
    }

    /**
     * Get general statistics for the dashboard
     *
     * @param int|null $userId
     * @param bool $isAdmin
     * @return array
     */
    public function getGeneralStats(?int $userId = null, bool $isAdmin = false): array
    {
        $cacheKey = 'dashboard_general_stats_' . ($userId ?? 'all') . '_' . ($isAdmin ? 'admin' : 'user');

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($userId, $isAdmin) {
            // Fire statistics (fires are public data from satellites, not filtered by user)
            $fireQuery = FocoIncendio::where('fecha', '>=', now()->subHours(48));
            $firesCount = $fireQuery->count();
            $firesLast7Days = (clone $fireQuery)->where('fecha', '>=', now()->subDays(7))->count();

            // Biomass statistics
            $biomasaQuery = Biomasa::aprobadas();
            if (!$isAdmin && $userId) {
                $biomasaQuery->where('user_id', $userId);
            }
            $biomasasCount = $biomasaQuery->count();
            $totalArea = $biomasaQuery->sum('area_m2') / 10000; // Convert to hectares

            // Pending biomasas (admin sees all, volunteer sees their own)
            $pendingQuery = Biomasa::pendientes();
            if (!$isAdmin && $userId) {
                $pendingQuery->where('user_id', $userId);
            }
            $pendingBiomasas = $pendingQuery->count();

            // Recent simulations
            $recentSimulations = Simulacione::where('fecha', '>=', now()->subDays(7))->count();

            return [
                'fires_48h' => $firesCount,
                'fires_7d' => $firesLast7Days,
                'fires_trend' => $firesLast7Days > 0 ? round((($firesCount - $firesLast7Days) / $firesLast7Days) * 100, 1) : 0,
                'biomasas_count' => $biomasasCount,
                'biomasas_area_ha' => round($totalArea, 2),
                'pending_biomasas' => $pendingBiomasas,
                'recent_simulations' => $recentSimulations,
            ];
        });
    }

    /**
     * Clear all cached metrics for a specific user
     *
     * @param int|null $userId
     * @return void
     */
    public function clearCache(?int $userId = null): void
    {
        $patterns = [
            'dashboard_fire_trends_',
            'dashboard_biomasa_dist_',
            'dashboard_biomasa_status_',
            'dashboard_simulation_stats_',
            'dashboard_user_activity_',
            'dashboard_general_stats_',
        ];

        foreach ($patterns as $pattern) {
            if ($userId) {
                Cache::forget($pattern . $userId . '_user');
                Cache::forget($pattern . $userId . '_admin');
            }
            Cache::forget($pattern . 'all_admin');
            Cache::forget($pattern . 'all_user');
        }
    }
}
