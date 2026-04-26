@extends('layouts.app')

@section('subtitle', 'Reporte de Predicciones')
@section('content_header_title', 'Reporte de Predicciones')

@section('content_body')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-line text-purple"></i> Filtros de Búsqueda</h3>
        <div class="card-tools">
            <a href="{{ route('incendios.reports.predictions.export-excel', request()->all()) }}" class="btn btn-success btn-sm mr-1">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('incendios.reports.predictions.export-pdf', request()->all()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('incendios.reports.predictions') }}" class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                           value="{{ request('fecha_inicio') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                           value="{{ request('fecha_fin') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="risk_min">Riesgo Mínimo</label>
                    <input type="number" step="0.01" min="0" max="1" class="form-control" 
                           id="risk_min" name="risk_min" value="{{ request('risk_min') }}" 
                           placeholder="0.0">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label for="risk_max">Riesgo Máximo</label>
                    <input type="number" step="0.01" min="0" max="1" class="form-control" 
                           id="risk_max" name="risk_max" value="{{ request('risk_max') }}" 
                           placeholder="1.0">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>{{ $statistics['total'] }}</h3>
                <p>Total Predicciones</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($statistics['avg_risk'], 2) }}</h3>
                <p>Riesgo Promedio</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ number_format($statistics['total_area'], 1) }} <sup style="font-size: 20px">km²</sup></h3>
                <p>Área Total Afectada</p>
            </div>
            <div class="icon">
                <i class="fas fa-map-marked-alt"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($statistics['avg_path_points'], 0) }}</h3>
                <p>Puntos Promedio</p>
            </div>
            <div class="icon">
                <i class="fas fa-route"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-pie text-purple"></i> Distribución por Nivel de Riesgo</h3>
            </div>
            <div class="card-body">
                <canvas id="riskDistributionChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Predictions Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-table text-purple"></i> Detalle de Predicciones</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha/Hora</th>
                    <th>Coordenadas Origen</th>
                    <th>Riesgo</th>
                    <th>Área Afectada</th>
                    <th>Puntos Trayectoria</th>
                    <th>Temperatura</th>
                    <th>Viento</th>
                    <th>Humedad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($predictions as $prediction)
                    @php
                        $meta = $prediction->meta ?? [];
                        $path = $prediction->path ?? [];
                        $inputs = $meta['input_parameters'] ?? [];
                        
                        // Get coordinates - try foco first, then path[0]
                        $lat = null;
                        $lng = null;
                        
                        $foco = $prediction->focoIncendio;
                        if ($foco && $foco->coordenadas) {
                            $coords = $foco->coordenadas;
                            if (is_array($coords)) {
                                $lat = $coords[0] ?? null;
                                $lng = $coords[1] ?? null;
                            }
                        } elseif (isset($path[0])) {
                            // Use first path point as origin
                            $lat = $path[0]['lat'] ?? null;
                            $lng = $path[0]['lng'] ?? null;
                        }

                        // Get max area from path
                        $maxArea = 0;
                        if (is_array($path)) {
                            foreach ($path as $point) {
                                if (isset($point['affected_area_km2'])) {
                                    $maxArea = max($maxArea, $point['affected_area_km2']);
                                }
                            }
                        }

                        // Calculate risk from fire_risk_index (0-100 scale)
                        $riskIndex = $meta['fire_risk_index'] ?? 0;
                        $risk = $riskIndex / 100;
                        $riskClass = 'success';
                        $riskText = 'Bajo';
                        if ($risk >= 0.7) {
                            $riskClass = 'danger';
                            $riskText = 'Alto';
                        } elseif ($risk >= 0.4) {
                            $riskClass = 'warning';
                            $riskText = 'Medio';
                        }
                    @endphp
                <tr>
                    <td><strong>#{{ $prediction->id }}</strong></td>
                    <td>
                        @if($prediction->predicted_at)
                            {{ $prediction->predicted_at->format('Y-m-d') }}<br>
                            <small class="text-muted">{{ $prediction->predicted_at->format('H:i:s') }}</small>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($lat && $lng)
                            <span class="badge badge-secondary">
                                {{ number_format($lat, 4) }}, {{ number_format($lng, 4) }}
                            </span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $riskClass }}">
                            {{ $riskText }}: {{ number_format($risk, 2) }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ number_format($maxArea, 2) }}</strong> km²
                    </td>
                    <td>
                        <span class="badge badge-info">{{ count($path) }} puntos</span>
                    </td>
                    <td>
                        {{ isset($inputs['temperature']) ? number_format($inputs['temperature'], 1) : 'N/A' }}°C
                    </td>
                    <td>
                        @if(isset($inputs['wind_speed']))
                            {{ number_format($inputs['wind_speed'], 1) }} km/h
                            @if(isset($inputs['wind_direction']))
                                <br><small class="text-muted">{{ $inputs['wind_direction'] }}°</small>
                            @endif
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        {{ isset($inputs['humidity']) ? number_format($inputs['humidity'], 1) : 'N/A' }}%
                    </td>
                    <td>
                        <a href="{{ route('incendios.predictions.show', $prediction->id) }}" 
                           class="btn btn-sm btn-info" title="Ver Detalle">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">
                        <i class="fas fa-info-circle"></i> No hay predicciones que coincidan con los filtros aplicados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($predictions->count() > 0)
    <div class="card-footer clearfix">
        <div class="float-right">
            <strong>Total de registros:</strong> {{ $predictions->count() }}
        </div>
    </div>
    @endif
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Risk Distribution Chart
    const riskCtx = document.getElementById('riskDistributionChart');
    if (riskCtx) {
        const riskData = @json($statistics['risk_distribution'] ?? ['high' => 0, 'medium' => 0, 'low' => 0]);
        
        new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: ['Alto (≥0.7)', 'Medio (0.4-0.7)', 'Bajo (<0.4)'],
                datasets: [{
                    data: [riskData.high, riskData.medium, riskData.low],
                    backgroundColor: ['#f56565', '#ed8936', '#48bb78'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush
