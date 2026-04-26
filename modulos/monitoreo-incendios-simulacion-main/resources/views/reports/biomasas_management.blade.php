@extends('layouts.app')

@section('subtitle', 'Reporte de Gestión de Biomasas')
@section('content_header_title', 'Reporte de Gestión de Biomasas')

@section('content_body')
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-leaf text-success"></i> Filtros de Búsqueda</h3>
        <div class="card-tools">
            <a href="{{ route('reports.biomasas.export-excel', request()->all()) }}" class="btn btn-success btn-sm mr-1">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </a>
            <a href="{{ route('reports.biomasas.export-pdf', request()->all()) }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Exportar PDF
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reports.biomasas') }}" class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ ($filters['estado'] ?? '') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobada" {{ ($filters['estado'] ?? '') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ ($filters['estado'] ?? '') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Tipo de Biomasa</label>
                    <select name="tipo_biomasa_id" class="form-control">
                        <option value="">Todos</option>
                        @foreach(\Modules\Incendios\Models\TipoBiomasa::all() as $tipo)
                            <option value="{{ $tipo->id }}" {{ ($filters['tipoBiomasaId'] ?? '') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->tipo_biomasa }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $filters['fechaInicio'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ $filters['fechaFin'] ?? '' }}">
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
        <x-adminlte-small-box title="{{ $statistics['total'] }}" text="Total Biomasas" icon="fas fa-leaf" theme="success"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['aprobadas'] }}" text="Aprobadas" icon="fas fa-check-circle" theme="primary"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['pendientes'] }}" text="Pendientes" icon="fas fa-clock" theme="warning"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $statistics['tasa_aprobacion'] }}%" text="Tasa de Aprobación" icon="fas fa-percent" theme="info"/>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <x-adminlte-card title="Distribución por Estado" theme="success" icon="fas fa-chart-pie">
            <x-chart-pie 
                chartId="biomasaStatusChart" 
                :labels="['Aprobadas', 'Pendientes', 'Rechazadas']"
                :data="[$statistics['aprobadas'], $statistics['pendientes'], $statistics['rechazadas']]"
                :colors="['#28a745', '#ffc107', '#dc3545']"
                :height="300"
                :options="[
                    'plugins' => [
                        'legend' => [
                            'position' => 'right',
                            'align' => 'center',
                            'labels' => [
                                'boxWidth' => 15,
                                'padding' => 15,
                                'font' => ['size' => 13]
                            ]
                        ]
                    ],
                    'maintainAspectRatio' => false
                ]"
            />
        </x-adminlte-card>
    </div>
    <div class="col-md-4">
        <x-adminlte-card title="Métricas de Gestión" theme="info" icon="fas fa-chart-bar">
            <div class="info-box bg-light mb-3">
                <span class="info-box-icon bg-success"><i class="fas fa-map"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Área Total</span>
                    <span class="info-box-number">{{ $statistics['area_total_ha'] }} ha</span>
                </div>
            </div>
            <div class="info-box bg-light">
                <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tiempo Prom. Revisión</span>
                    <span class="info-box-number">{{ $statistics['tiempo_promedio_revision_horas'] }} hrs</span>
                </div>
            </div>
        </x-adminlte-card>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <x-adminlte-card title="Listado de Biomasas" theme="success" icon="fas fa-list">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ubicación</th>
                            <th>Área (ha)</th>
                            <th>Densidad</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Creado Por</th>
                            <th>Fecha</th>
                            @if($isAdmin)
                            <th>Aprobado Por</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($biomasas as $biomasa)
                        <tr>
                            <td>{{ $biomasa->id }}</td>
                            <td>{{ $biomasa->ubicacion ?? 'Sin ubicación' }}</td>
                            <td>{{ number_format($biomasa->area_m2 / 10000, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $biomasa->densidad == 'alta' ? 'success' : ($biomasa->densidad == 'media' ? 'warning' : 'info') }}">
                                    {{ ucfirst($biomasa->densidad ?? 'N/A') }}
                                </span>
                            </td>
                            <td style="color: {{ $biomasa->tipoBiomasa->color ?? '#000' }}">
                                {{ $biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $biomasa->estado == 'aprobada' ? 'success' : ($biomasa->estado == 'pendiente' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($biomasa->estado) }}
                                </span>
                            </td>
                            <td>{{ $biomasa->user->name ?? 'N/A' }}</td>
                            <td>{{ $biomasa->created_at->format('d/m/Y') }}</td>
                            @if($isAdmin)
                            <td>{{ $biomasa->aprobadaPor->name ?? '-' }}</td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 9 : 8 }}" class="text-center text-muted">No se encontraron biomasas con los filtros aplicados</td>
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
    console.log('🎨 [Biomasas Report] Iniciando sistema de charts...');
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
