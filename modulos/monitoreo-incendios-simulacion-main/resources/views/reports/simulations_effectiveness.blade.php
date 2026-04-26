@extends('layouts.app')

@section('subtitle', 'Reporte de Efectividad de Simulaciones')
@section('content_header_title', 'Reporte de Efectividad de Simulaciones')

@section('content_body')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-project-diagram text-purple"></i> Filtros de Búsqueda</h3>
        <div class="card-tools">
            <a href="{{ route('reports.simulations.export-excel', request()->all()) }}" class="btn btn-success btn-sm mr-1">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('reports.simulations.export-pdf', request()->all()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.simulations') }}" class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $filters['fechaInicio'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ $filters['fechaFin'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Riesgo Mínimo</label>
                    <input type="number" step="0.1" name="fire_risk_min" class="form-control" value="{{ $filters['fireRiskMin'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Riesgo Máximo</label>
                    <input type="number" step="0.1" name="fire_risk_max" class="form-control" value="{{ $filters['fireRiskMax'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['total'] }}" text="Total Simulaciones" icon="fas fa-project-diagram" theme="purple"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['avg_duration'] }} min" text="Duración Promedio" icon="fas fa-clock" theme="info"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['avg_fire_risk'] }}" text="Riesgo Promedio" icon="fas fa-exclamation-triangle" theme="warning"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['total_volunteers'] }}" text="Voluntarios Desplegados" icon="fas fa-users" theme="success"/>
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <x-adminlte-card title="Riesgo de Incendio Promedio" theme="warning" icon="fas fa-gauge">
            <div style="padding: 20px 0;">
                <x-chart-gauge 
                    chartId="fireRiskGauge" 
                    :value="$statistics['avg_fire_risk']"
                    :max="100"
                    label="Nivel de Riesgo"
                    :height="280"
                />
            </div>
        </x-adminlte-card>
    </div>
    <div class="col-md-7">
        <x-adminlte-card title="Estrategias Más Utilizadas (Top 5)" theme="success" icon="fas fa-list-ol">
            @if(isset($statistics['top_strategies']) && count($statistics['top_strategies']) > 0)
                <ul class="list-group list-group-flush">
                    @foreach($statistics['top_strategies'] as $strategy => $count)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-shield-alt text-primary mr-2"></i>{{ $strategy }}</span>
                            <span class="badge badge-primary badge-pill">{{ $count }} usos</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted text-center py-5"><i class="fas fa-info-circle"></i> No hay datos de estrategias disponibles</p>
            @endif
        </x-adminlte-card>
    </div>
</div>

@if($correlations)
<div class="row">
    <div class="col-md-12">
        <x-adminlte-card title="Correlaciones Ambientales con Riesgo de Incendio" theme="dark" icon="fas fa-link">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-danger"><i class="fas fa-temperature-high"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Correlación Temperatura</span>
                            <span class="info-box-number">{{ number_format($correlations['temp_vs_risk'] ?? 0, 4) }}</span>
                            <small class="text-muted">{{ abs($correlations['temp_vs_risk'] ?? 0) > 0.5 ? 'Correlación fuerte' : 'Correlación débil' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-info"><i class="fas fa-tint"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Correlación Humedad</span>
                            <span class="info-box-number">{{ number_format($correlations['humidity_vs_risk'] ?? 0, 4) }}</span>
                            <small class="text-muted">{{ abs($correlations['humidity_vs_risk'] ?? 0) > 0.5 ? 'Correlación fuerte' : 'Correlación débil' }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-light">
                        <span class="info-box-icon bg-success"><i class="fas fa-wind"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Correlación Viento</span>
                            <span class="info-box-number">{{ number_format($correlations['wind_vs_risk'] ?? 0, 4) }}</span>
                            <small class="text-muted">{{ abs($correlations['wind_vs_risk'] ?? 0) > 0.5 ? 'Correlación fuerte' : 'Correlación débil' }}</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i> 
                <strong>Interpretación:</strong> Valores cercanos a 1 o -1 indican correlación fuerte. Valores cercanos a 0 indican poca relación.
                Un valor positivo significa que al aumentar la variable ambiental, aumenta el riesgo. Un valor negativo significa la relación inversa.
            </div>
        </x-adminlte-card>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-12">
        <x-adminlte-card title="Listado de Simulaciones" theme="purple" icon="fas fa-list">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Duración (min)</th>
                            <th>Riesgo</th>
                            <th>Focos Activos</th>
                            <th>Voluntarios</th>
                            <th>Temp (°C)</th>
                            <th>Humedad (%)</th>
                            <th>Viento (km/h)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($simulations as $sim)
                        <tr>
                            <td>{{ $sim->id }}</td>
                            <td>{{ $sim->nombre ?? 'Sin nombre' }}</td>
                            <td>{{ $sim->fecha->format('d/m/Y H:i') }}</td>
                            <td>{{ $sim->duracion ?? 0 }}</td>
                            <td>
                                @php
                                    $risk = $sim->fire_risk ?? 0;
                                    $riskBadge = $risk < 30 ? 'success' : ($risk < 60 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge badge-{{ $riskBadge }}">{{ number_format($risk, 2) }}</span>
                            </td>
                            <td>{{ $sim->focos_activos ?? 0 }}</td>
                            <td>{{ $sim->num_voluntarios_enviados ?? 0 }}</td>
                            <td>{{ number_format($sim->temperature ?? 0, 1) }}</td>
                            <td>{{ number_format($sim->humidity ?? 0, 1) }}</td>
                            <td>{{ number_format($sim->wind_speed ?? 0, 1) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">No se encontraron simulaciones con los filtros aplicados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
    </div>
</div>

@endsection

@push('js')
<script>
// Inicializar charts cuando la página esté lista
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎨 [Simulations Report] Iniciando sistema de charts...');
    console.log('Chart.js disponible:', typeof Chart !== 'undefined');
    console.log('window.initChart disponible:', typeof window.initChart === 'function');
    
    setTimeout(() => {
        const canvases = document.querySelectorAll('canvas[data-chart-type]');
        console.log('Canvas encontrados:', canvases.length);
        
        if (typeof Chart === 'undefined') {
            console.error('❌ Chart.js no está cargado');
            return;
        }
        
        if (typeof window.initChart !== 'function') {
            console.error('❌ window.initChart no está disponible');
            return;
        }
        
        canvases.forEach(canvas => {
            console.log('Inicializando chart:', canvas.id, 'tipo:', canvas.dataset.chartType);
            try {
                window.initChart(canvas.id);
                console.log('✅ Chart inicializado:', canvas.id);
            } catch (error) {
                console.error('❌ Error inicializando', canvas.id, ':', error);
            }
        });
    }, 500);
});
</script>
@endpush

@push('css')
<style>
    @media print {
        .card-tools, .content-header, .main-sidebar, .main-footer {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
