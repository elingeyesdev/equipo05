@extends('layouts.app')

@section('subtitle', 'Panel de Control')
@section('content_header_title', 'Dashboard SIPII')

@push('css')
<style>
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #007bff;
        font-weight: 600;
    }
    .info-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
    }
    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content_body')
<div class="container-fluid">
    <!-- Header Actions -->
    <div class="row mb-3">
        <div class="col-12 text-right">
            <button 
                @click="clearCache()" 
                class="btn btn-sm btn-outline-primary"
                x-data="{
                    async clearCache() {
                        try {
                            const response = await axios.post('/dashboard/clear-cache');
                            if (response.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Éxito',
                                    text: 'Caché actualizado correctamente',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                setTimeout(() => location.reload(), 2000);
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo actualizar el caché'
                            });
                        }
                    }
                }"
            >
                <i class="fas fa-sync-alt"></i> Actualizar Caché
            </button>
        </div>
    </div>

    <!-- Tabbed Interface -->
    <div class="card card-primary card-outline card-tabs">
        <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="monitoring-tab" data-toggle="tab" href="#monitoring" role="tab" aria-controls="monitoring" aria-selected="true">
                        <i class="fas fa-map-marked-alt"></i> Monitoreo en Tiempo Real
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="analysis-tab" data-toggle="tab" href="#analysis" role="tab" aria-controls="analysis" aria-selected="false">
                        <i class="fas fa-fire"></i> Análisis de Focos de Incendio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="biomass-tab" data-toggle="tab" href="#biomass" role="tab" aria-controls="biomass" aria-selected="false">
                        <i class="fas fa-leaf"></i> Gestión de Biomasas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="simulations-tab" data-toggle="tab" href="#simulations" role="tab" aria-controls="simulations" aria-selected="false">
                        <i class="fas fa-project-diagram"></i> Simulaciones
                    </a>
                </li>
                @if($isAdmin)
                <li class="nav-item">
                    <a class="nav-link" id="users-tab" data-toggle="tab" href="#users" role="tab" aria-controls="users" aria-selected="false">
                        <i class="fas fa-users"></i> Usuarios
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="dashboardTabsContent">
                
                <!-- Tab 1: Monitoreo en Tiempo Real -->
                <div class="tab-pane fade show active" id="monitoring" role="tabpanel" aria-labelledby="monitoring-tab">
                    {{-- EXISTING MAP AND WEATHER CONTENT WILL GO HERE --}}
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> El contenido del mapa y el clima existente se integrará aquí.
                    </div>
                </div>

                <!-- Tab 2: Análisis de Incendios -->
                <div class="tab-pane fade" id="analysis" role="tabpanel" aria-labelledby="analysis-tab">
                    <div class="row">
                        <!-- Statistics Cards -->
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-danger info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $generalStats['fires_48h'] ?? 0 }}</h3>
                                    <p class="stat-label">Focos de Incendio (48h)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-fire"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-warning info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $generalStats['fires_7d'] ?? 0 }}</h3>
                                    <p class="stat-label">Focos de Incendio (7 días)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-info info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $generalStats['avg_confidence'] ?? 0 }}%</h3>
                                    <p class="stat-label">Confianza Promedio</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-percentage"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-success info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $generalStats['active_alerts'] ?? 0 }}</h3>
                                    <p class="stat-label">Alertas Activas</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fire Trend Chart -->
                    <div class="row">
                        <div class="col-12">
                            <x-adminlte-card title="Tendencia de Focos de Incendio (Últimos 30 días)" theme="primary" icon="fas fa-chart-line">
                                <x-chart-line
                                    id="fireTrendChart"
                                    :labels="array_column($fireTrends ?? [], 'date')"
                                    :datasets="[
                                        [
                                            'label' => 'Focos de Incendio Detectados',
                                            'data' => array_column($fireTrends ?? [], 'count'),
                                            'borderColor' => 'rgb(255, 99, 132)',
                                            'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                                            'tension' => 0.4
                                        ]
                                    ]"
                                    height="300"
                                />
                            </x-adminlte-card>
                        </div>
                    </div>

                    <!-- Additional Analysis -->
                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-card title="Focos de Incendio por Región" theme="info" icon="fas fa-map">
                                <div class="table-responsive">
                                    <table class="table table-striped table-sm">
                                        <thead>
                                            <tr>
                                                <th>Región</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-center">Porcentaje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($generalStats['fires_by_region'] ?? [] as $region)
                                            <tr>
                                                <td>{{ $region['name'] }}</td>
                                                <td class="text-center">{{ $region['count'] }}</td>
                                                <td class="text-center">{{ $region['percentage'] }}%</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No hay datos disponibles</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </x-adminlte-card>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-card title="Nivel de Confianza" theme="success" icon="fas fa-check-circle">
                                <div class="progress-group">
                                    <span class="progress-text">Alta Confianza (&gt;80%)</span>
                                    <span class="float-right"><b>{{ $generalStats['high_confidence'] ?? 0 }}</b>/{{ $generalStats['fires_48h'] ?? 0 }}</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-success" style="width: {{ $generalStats['high_confidence_pct'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Media Confianza (50-80%)</span>
                                    <span class="float-right"><b>{{ $generalStats['medium_confidence'] ?? 0 }}</b>/{{ $generalStats['fires_48h'] ?? 0 }}</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-warning" style="width: {{ $generalStats['medium_confidence_pct'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                                <div class="progress-group">
                                    <span class="progress-text">Baja Confianza (&lt;50%)</span>
                                    <span class="float-right"><b>{{ $generalStats['low_confidence'] ?? 0 }}</b>/{{ $generalStats['fires_48h'] ?? 0 }}</span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-danger" style="width: {{ $generalStats['low_confidence_pct'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </x-adminlte-card>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Gestión de Biomasas -->
                <div class="tab-pane fade" id="biomass" role="tabpanel" aria-labelledby="biomass-tab">
                    <div class="row">
                        <!-- Biomass Statistics Cards -->
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-success info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $generalStats['total_biomass'] ?? 0 }}</h3>
                                    <p class="stat-label">Total Biomasas</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-primary info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $generalStats['active_biomass'] ?? 0 }}</h3>
                                    <p class="stat-label">Biomasas Activas</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-warning info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $generalStats['pending_biomass'] ?? 0 }}</h3>
                                    <p class="stat-label">Pendientes</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-info info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $generalStats['avg_biomass_density'] ?? 0 }}</h3>
                                    <p class="stat-label">Densidad Promedio</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-card title="Distribución por Tipo de Biomasa" theme="success" icon="fas fa-chart-pie">
                                <x-chart-pie
                                    id="biomasaTypeChart"
                                    :labels="array_column($biomasaDistribution ?? [], 'type')"
                                    :data="array_column($biomasaDistribution ?? [], 'count')"
                                    :backgroundColors="['#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6c757d']"
                                    height="300"
                                />
                            </x-adminlte-card>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-card title="Estado de Biomasas" theme="primary" icon="fas fa-chart-bar">
                                <x-chart-bar
                                    id="biomasaStatusChart"
                                    :labels="array_column($biomasaStatus ?? [], 'status')"
                                    :datasets="[
                                        [
                                            'label' => 'Cantidad',
                                            'data' => array_column($biomasaStatus ?? [], 'count'),
                                            'backgroundColor' => ['#28a745', '#ffc107', '#dc3545', '#6c757d']
                                        ]
                                    ]"
                                    height="300"
                                />
                            </x-adminlte-card>
                        </div>
                    </div>

                    <!-- Biomass Details Table -->
                    <div class="row">
                        <div class="col-12">
                            <x-adminlte-card title="Detalles de Biomasa por Tipo" theme="info" icon="fas fa-table">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Tipo de Biomasa</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-center">Área Total (ha)</th>
                                                <th class="text-center">Densidad Promedio</th>
                                                <th class="text-center">Riesgo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($biomasaDistribution ?? [] as $biomass)
                                            <tr>
                                                <td><i class="fas fa-leaf text-success mr-2"></i>{{ $biomass['type'] }}</td>
                                                <td class="text-center">{{ $biomass['count'] }}</td>
                                                <td class="text-center">{{ number_format($biomass['area'] ?? 0, 2) }}</td>
                                                <td class="text-center">{{ number_format($biomass['density'] ?? 0, 2) }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $risk = $biomass['risk'] ?? 'bajo';
                                                        $badgeClass = $risk === 'alto' ? 'danger' : ($risk === 'medio' ? 'warning' : 'success');
                                                    @endphp
                                                    <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($risk) }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No hay datos de biomasa disponibles</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </x-adminlte-card>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Simulaciones -->
                <div class="tab-pane fade" id="simulations" role="tabpanel" aria-labelledby="simulations-tab">
                    <div class="row">
                        <!-- Simulation Statistics Cards -->
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-purple info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $simulationStats['total_simulations'] ?? 0 }}</h3>
                                    <p class="stat-label">Total Simulaciones</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-project-diagram"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-primary info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $simulationStats['simulations_30d'] ?? 0 }}</h3>
                                    <p class="stat-label">Últimos 30 días</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-success info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $simulationStats['completed'] ?? 0 }}</h3>
                                    <p class="stat-label">Completadas</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-info info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $simulationStats['avg_duration'] ?? 0 }}s</h3>
                                    <p class="stat-label">Duración Promedio</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-stopwatch"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-card title="Riesgo Promedio de Incendio" theme="warning" icon="fas fa-tachometer-alt">
                                <x-chart-gauge
                                    id="avgFireRiskGauge"
                                    :value="$simulationStats['avg_fire_risk'] ?? 0"
                                    :max="100"
                                    label="Riesgo de Incendio (%)"
                                    height="300"
                                />
                            </x-adminlte-card>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-card title="Simulaciones por Mes" theme="primary" icon="fas fa-chart-line">
                                <x-chart-line
                                    id="monthlySimulationsChart"
                                    :labels="array_column($simulationStats['monthly'] ?? [], 'month')"
                                    :datasets="[
                                        [
                                            'label' => 'Simulaciones',
                                            'data' => array_column($simulationStats['monthly'] ?? [], 'count'),
                                            'borderColor' => 'rgb(54, 162, 235)',
                                            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                                            'tension' => 0.4,
                                            'fill' => true
                                        ]
                                    ]"
                                    height="300"
                                />
                            </x-adminlte-card>
                        </div>
                    </div>

                    <!-- Simulation Results Table -->
                    <div class="row">
                        <div class="col-12">
                            <x-adminlte-card title="Últimas Simulaciones" theme="info" icon="fas fa-history">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th class="text-center">Riesgo</th>
                                                <th class="text-center">Área Afectada</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Duración</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($simulationStats['recent'] ?? [] as $sim)
                                            <tr>
                                                <td><code>#{{ $sim['id'] }}</code></td>
                                                <td>{{ $sim['created_at'] }}</td>
                                                <td>{{ $sim['type'] }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $sim['risk_level'] === 'alto' ? 'danger' : ($sim['risk_level'] === 'medio' ? 'warning' : 'success') }}">
                                                        {{ $sim['risk_percentage'] }}%
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $sim['affected_area'] }} ha</td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $sim['status'] === 'completed' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($sim['status']) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $sim['duration'] }}s</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No hay simulaciones recientes</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </x-adminlte-card>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Usuarios (Admin Only) -->
                @if($isAdmin)
                <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                    <div class="row">
                        <!-- User Statistics Cards -->
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-info info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $userActivity['total_users'] ?? 0 }}</h3>
                                    <p class="stat-label">Total Usuarios</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-success info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $userActivity['active_users'] ?? 0 }}</h3>
                                    <p class="stat-label">Usuarios Activos</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-warning info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $userActivity['new_users_30d'] ?? 0 }}</h3>
                                    <p class="stat-label">Nuevos (30d)</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="small-box bg-primary info-card">
                                <div class="inner">
                                    <h3 class="stat-value">{{ $userActivity['online_now'] ?? 0 }}</h3>
                                    <p class="stat-label">Conectados</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Activity Chart -->
                    <div class="row">
                        <div class="col-12">
                            <x-adminlte-card title="Actividad y Contribuciones de Usuarios" theme="primary" icon="fas fa-chart-bar">
                                <x-chart-bar
                                    id="userActivityChart"
                                    :labels="array_column($userActivity['top_contributors'] ?? [], 'name')"
                                    :datasets="[
                                        [
                                            'label' => 'Biomasas Creadas',
                                            'data' => array_column($userActivity['top_contributors'] ?? [], 'biomass_count'),
                                            'backgroundColor' => 'rgba(75, 192, 192, 0.8)'
                                        ],
                                        [
                                            'label' => 'Simulaciones',
                                            'data' => array_column($userActivity['top_contributors'] ?? [], 'simulation_count'),
                                            'backgroundColor' => 'rgba(153, 102, 255, 0.8)'
                                        ],
                                        [
                                            'label' => 'Reportes',
                                            'data' => array_column($userActivity['top_contributors'] ?? [], 'report_count'),
                                            'backgroundColor' => 'rgba(255, 159, 64, 0.8)'
                                        ]
                                    ]"
                                    height="350"
                                />
                            </x-adminlte-card>
                        </div>
                    </div>

                    <!-- User Details Table -->
                    <div class="row">
                        <div class="col-12">
                            <x-adminlte-card title="Top Colaboradores" theme="success" icon="fas fa-trophy">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Usuario</th>
                                                <th>Email</th>
                                                <th class="text-center">Rol</th>
                                                <th class="text-center">Contribuciones</th>
                                                <th class="text-center">Última Actividad</th>
                                                <th class="text-center">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($userActivity['top_contributors'] ?? [] as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <i class="fas fa-user-circle mr-2"></i>{{ $user['name'] }}
                                                </td>
                                                <td>{{ $user['email'] }}</td>
                                                <td class="text-center">
                                                    <span class="badge badge-{{ $user['role'] === 'admin' ? 'danger' : 'info' }}">
                                                        {{ ucfirst($user['role']) }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-success">{{ $user['total_contributions'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <small class="text-muted">{{ $user['last_activity'] }}</small>
                                                </td>
                                                <td class="text-center">
                                                    @if($user['is_online'])
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-circle"></i> Online
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">Offline</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">No hay datos de usuarios disponibles</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </x-adminlte-card>
                        </div>
                    </div>

                    <!-- User Registration Trend -->
                    <div class="row">
                        <div class="col-12">
                            <x-adminlte-card title="Registro de Usuarios (Últimos 12 meses)" theme="info" icon="fas fa-chart-line">
                                <x-chart-line
                                    id="userRegistrationChart"
                                    :labels="array_column($userActivity['registration_trend'] ?? [], 'month')"
                                    :datasets="[
                                        [
                                            'label' => 'Nuevos Usuarios',
                                            'data' => array_column($userActivity['registration_trend'] ?? [], 'count'),
                                            'borderColor' => 'rgb(75, 192, 192)',
                                            'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                                            'tension' => 0.4,
                                            'fill' => true
                                        ]
                                    ]"
                                    height="300"
                                />
                            </x-adminlte-card>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });

    // Tab change event handler
    $('#dashboardTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var targetTab = $(e.target).attr('href');
        console.log('Tab cambiada a:', targetTab);
        
        // Trigger window resize to ensure charts render properly
        window.dispatchEvent(new Event('resize'));
    });

    // Auto-refresh data every 5 minutes
    setInterval(function() {
        console.log('Auto-refresh triggered');
        // You can add AJAX calls here to refresh specific data
    }, 300000);
</script>
@endpush
