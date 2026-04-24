@extends('adminlte::page')

@section('title', 'Dashboard de Distribución')

@section('content_header')
<h1><i class="fas fa-shipping-fast"></i> Dashboard de Distribución de Paquetes</h1>
@stop

@section('content')
{{-- Filters Card --}}
<div class="card card-primary collapsed-card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filtros</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('inventario.reportes.distribucion') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control"
                            value="{{ $request->fecha_inicio ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin" class="form-control"
                            value="{{ $request->fecha_fin ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Destino</label>
                        <input type="text" name="destino" class="form-control" placeholder="Buscar destino..."
                            value="{{ $request->destino ?? '' }}" list="destinos-list">
                        <datalist id="destinos-list">
                            @foreach($destinosUnicos as $dest)
                                <option value="{{ $dest }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Encargado</label>
                        <input type="text" name="encargado" class="form-control" placeholder="Buscar encargado..."
                            value="{{ $request->encargado ?? '' }}" list="encargados-list">
                        <datalist id="encargados-list">
                            @foreach($encargadosUnicos as $enc)
                                <option value="{{ $enc }}">
                            @endforeach
                        </datalist>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('inventario.reportes.distribucion') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- KPIs --}}
<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Distribuido</span>
                <span class="info-box-number">{{ $totalDistribuido }}</span>
                <span class="info-box-text">paquetes enviados</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pendiente Distribución</span>
                <span class="info-box-number">{{ $pendienteDistribucion }}</span>
                <span class="info-box-text">paquetes por enviar</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-map-marker-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Destino Más Frecuente</span>
                <span class="info-box-number" style="font-size: 1.2rem;">{{ $destinoMasFrecuente }}</span>
                <span class="info-box-text">{{ $destinoMasFrecuenteCount }} envíos</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="far fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Último Envío</span>
                <span class="info-box-number" style="font-size: 1.2rem;">{{ $ultimoEnvioFecha }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Top 10 Destinos</h3>
            </div>
            <div class="card-body">
                <canvas id="destinosChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> Distribución Mensual</h3>
            </div>
            <div class="card-body">
                <canvas id="mensualChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Detailed Table --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-table"></i> Detalle de Distribuciones</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-success" onclick="exportarExcel()">
                <i class="fas fa-file-excel"></i> Exportar Excel
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="distribucionTable" class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Paquete</th>
                        <th>Fecha Salida</th>
                        <th>Destino</th>
                        <th>Encargado</th>
                        <th>Items</th>
                        <th>Productos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salidasDetalladas as $salida)
                        <tr>
                            <td><code>{{ $salida['codigo_paquete'] }}</code></td>
                            <td>{{ \Carbon\Carbon::parse($salida['fecha_salida'])->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $salida['destino'] ?? '-' }}</strong></td>
                            <td>{{ $salida['encargado'] ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge badge-primary">{{ $salida['total_items'] }}</span>
                            </td>
                            <td>
                                @foreach($salida['productos'] as $producto)
                                    <div class="mb-1">
                                        <i class="fas fa-box text-muted"></i> {{ $producto['nombre'] }}
                                        <span class="badge badge-info">{{ $producto['cantidad'] }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('inventario.registros-salida.show', $salida['id_salida']) }}"
                                    class="btn btn-sm btn-info" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay registros de distribución</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Chart colors
    const colors = {
        primary: '#007bff',
        success: '#28a745',
        info: '#17a2b8',
        warning: '#ffc107',
        danger: '#dc3545',
        secondary: '#6c757d'
    };

    // Top Destinations Chart
    const destinosCtx = document.getElementById('destinosChart').getContext('2d');
    const destinosData = @json($destinosFrecuentes);
    const destinosChart = new Chart(destinosCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(destinosData),
            datasets: [{
                label: 'Cantidad de Envíos',
                data: Object.values(destinosData),
                backgroundColor: colors.primary,
                borderColor: colors.primary,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Monthly Distribution Chart
    const mensualCtx = document.getElementById('mensualChart').getContext('2d');
    const mensualData = @json($distribucionMensual);
    const mensualChart = new Chart(mensualCtx, {
        type: 'line',
        data: {
            labels: Object.keys(mensualData).map(m => {
                const [year, month] = m.split('-');
                return new Date(year, month - 1).toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Paquetes Distribuidos',
                data: Object.values(mensualData),
                backgroundColor: 'rgba(23, 162, 184, 0.2)',
                borderColor: colors.info,
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Export to Excel function
    function exportarExcel() {
        const params = new URLSearchParams(window.location.search);
        params.set('formato', 'excel');
        window.location.href = '{{ route("inventario.reportes.distribucion") }}?' + params.toString();
    }

    // Initialize DataTable
    $(document).ready(function () {
        $('#distribucionTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "pageLength": 10,
            "order": [[1, "desc"]]
        });
    });
</script>
@stop

@section('css')
<style>
    .info-box-number {
        font-weight: bold;
    }

    .info-box {
        min-height: 100px;
    }
</style>
@stop





