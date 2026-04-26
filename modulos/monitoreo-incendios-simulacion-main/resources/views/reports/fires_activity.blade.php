@extends('layouts.app')

@section('subtitle', 'Reporte de Actividad de Focos de Incendio')
@section('content_header_title', 'Reporte de Actividad de Focos de Incendio')

@section('content_body')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-fire text-danger"></i> Filtros de Búsqueda</h3>
        <div class="card-tools">
            <a href="{{ route('incendios.reports.fires.export-pdf', request()->all()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
            <a href="{{ route('incendios.reports.fires.export-excel', request()->all()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('incendios.reports.fires') }}" class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $filters['fecha_inicio'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ $filters['fecha_fin'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Intensidad Mín</label>
                    <input type="number" step="0.1" name="intensidad_min" class="form-control" value="{{ $filters['intensidad_min'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Intensidad Máx</label>
                    <input type="number" step="0.1" name="intensidad_max" class="form-control" value="{{ $filters['intensidad_max'] ?? '' }}">
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
        <x-adminlte-small-box title="{{ $statistics['total'] }}" text="Total de Focos" icon="fas fa-fire" theme="danger"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['avg_intensity'] }}" text="Intensidad Promedio" icon="fas fa-thermometer-half" theme="warning"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['max_intensity'] }}" text="Intensidad Máxima" icon="fas fa-bolt" theme="orange"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['min_intensity'] }}" text="Intensidad Mínima" icon="fas fa-fire-extinguisher" theme="info"/>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <x-adminlte-card title="Tendencia de Actividad (Últimos 30 días)" theme="dark" icon="fas fa-chart-line">
            @php
                // Asegurar que los datos son arrays indexados correctamente
                $chartData = $statistics['by_date'] ?? collect();
                $chartLabels = [];
                $chartValues = [];
                
                foreach ($chartData as $date => $count) {
                    // Formatear fecha más legible
                    $chartLabels[] = date('d/m/Y', strtotime($date));
                    $chartValues[] = (int)$count;
                }
                
                // Si no hay datos, crear datos por defecto
                if (empty($chartLabels)) {
                    $chartLabels = [now()->format('d/m/Y')];
                    $chartValues = [0];
                }
                
                // Configuración de opciones del chart
                $chartOptions = [
                    'responsive' => true,
                    'maintainAspectRatio' => false,
                    'plugins' => [
                        'legend' => [
                            'display' => true,
                            'position' => 'top',
                        ],
                        'tooltip' => [
                            'enabled' => true,
                            'mode' => 'index',
                            'intersect' => false,
                        ],
                    ],
                    'scales' => [
                        'y' => [
                            'beginAtZero' => true,
                            'ticks' => [
                                'precision' => 0,
                            ],
                            'title' => [
                                'display' => true,
                                'text' => 'Cantidad de Focos',
                            ],
                        ],
                        'x' => [
                            'title' => [
                                'display' => true,
                                'text' => 'Fecha',
                            ],
                            'ticks' => [
                                'maxRotation' => 45,
                                'minRotation' => 45,
                            ],
                        ],
                    ],
                ];
            @endphp
            <x-chart-line 
                chartId="fireTrendChart" 
                :labels="$chartLabels"
                :datasets="[[
                    'label' => 'Focos de Incendio',
                    'data' => $chartValues,
                    'borderColor' => '#DC3545',
                    'backgroundColor' => 'rgba(220, 53, 69, 0.1)',
                    'borderWidth' => 2,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#DC3545',
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'pointHoverRadius' => 6,
                    'tension' => 0.4,
                    'fill' => true
                ]]"
                type="line"
                :height="350"
                :options="$chartOptions"
            />
        </x-adminlte-card>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <x-adminlte-card title="Listado de Focos de Incendio" theme="danger" icon="fas fa-list">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Ubicación</th>
                            <th>Coordenadas</th>
                            <th>Intensidad</th>
                            <th>Biomasa Relacionada</th>
                            <th>Tipo Biomasa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fires as $fire)
                        <tr>
                            <td>{{ $fire->id }}</td>
                            <td>{{ $fire->fecha->format('d/m/Y H:i') }}</td>
                            <td>{{ $fire->ubicacion ?? 'Sin ubicación' }}</td>
                            <td>{{ $fire->formatted_coordinates }}</td>
                            <td>
                                <span class="badge badge-{{ $fire->intensidad >= 7 ? 'danger' : ($fire->intensidad >= 4 ? 'warning' : 'info') }}">
                                    {{ number_format($fire->intensidad, 2) }}
                                </span>
                            </td>
                            <td>{{ $fire->biomasa->ubicacion ?? 'N/A' }}</td>
                            <td>
                                @if($fire->biomasa && $fire->biomasa->tipoBiomasa)
                                    <span style="color: {{ $fire->biomasa->tipoBiomasa->color }}">
                                        {{ $fire->biomasa->tipoBiomasa->tipo_biomasa }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No se encontraron focos de incendio con los filtros aplicados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-adminlte-card>
    </div>
</div>

@endsection

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

@push('js')
<script>
// Inicializar charts cuando la página esté lista
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        document.querySelectorAll('canvas[data-chart-type]').forEach(canvas => {
            if (typeof window.initChart === 'function') {
                console.log('Inicializando chart:', canvas.id);
                window.initChart(canvas.id);
            }
        });
    }, 300);
});
</script>
@endpush
