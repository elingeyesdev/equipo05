@extends('adminlte::page')

@section('title', 'Dashboard Almacenista')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-warehouse"></i> Dashboard de Almacén</h1>
    <small class="text-muted">Vista del Almacenista</small>
</div>
@stop

@section('content')
<div class="container-fluid">
    {{-- SECCIÓN 1: KPIs del Almacén --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-warehouse"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Almacenes</span>
                    <span class="info-box-number">{{ number_format($totalAlmacenes) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-th-large"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Estantes</span>
                    <span class="info-box-number">{{ number_format($totalEstantes) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-border-all"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Espacios</span>
                    <span class="info-box-number">{{ number_format($totalEspacios) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Espacios Disponibles</span>
                    <span class="info-box-number">{{ number_format($espaciosDisponibles) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Espacios Llenos</span>
                    <span class="info-box-number">{{ number_format($espaciosLlenos) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-box-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Productos en Inventario</span>
                    <span class="info-box-number">{{ number_format($productosInventario) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 2: Utilización de Almacenes y Estado de Espacios --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Utilización por Almacén</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="utilizacionChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Porcentaje de ocupación por almacén</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-pie-chart"></i> Estado de Espacios</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="espaciosChart" style="min-height: 250px; height: 250px; max-height: 250px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Distribución de espacios disponibles vs llenos</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 3: Productos por Categoría y Top Productos --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tags"></i> Productos por Categoría</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="categoriasChart" style="min-height: 250px; height: 250px; max-height: 250px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Distribución de inventario por categoría</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-trophy"></i> Top 5 Productos Almacenados</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="topProductosChart"
                        style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Productos con mayor cantidad en stock</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 4: Movimientos Recientes --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exchange-alt"></i> Movimientos Recientes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @if(count($movimientosRecientes) > 0)
                        <div class="timeline">
                            @foreach($movimientosRecientes as $movimiento)
                                <div>
                                    <i class="{{ $movimiento['icono'] }} bg-{{ $movimiento['color'] }}"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i>
                                            {{ $movimiento['fecha']->diffForHumans() }}</span>
                                        <h3 class="timeline-header">{{ $movimiento['titulo'] }}</h3>
                                        <div class="timeline-body">
                                            {{ $movimiento['descripcion'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-muted">No hay movimientos recientes</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .info-box-number {
        font-size: 28px;
        font-weight: bold;
    }

    .info-box-text {
        font-size: 13px;
    }

    .timeline {
        position: relative;
        margin: 0 0 30px 0;
        padding: 0;
        list-style: none;
    }

    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #dee2e6;
        left: 31px;
        margin: 0;
    }

    .timeline>div {
        margin-bottom: 15px;
        position: relative;
    }

    .timeline>div>.timeline-item {
        margin-left: 60px;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 3px;
        padding: 10px;
    }

    .timeline>div>.fas,
    .timeline>div>.far,
    .timeline>div>.fab {
        width: 30px;
        height: 30px;
        font-size: 15px;
        line-height: 30px;
        position: absolute;
        color: #fff;
        background: #adb5bd;
        border-radius: 50%;
        text-align: center;
        left: 18px;
        top: 0;
    }

    .timeline>div>.timeline-item>.time {
        color: #999;
        float: right;
        font-size: 12px;
    }

    .timeline>div>.timeline-item>.timeline-header {
        margin: 0;
        color: #555;
        border-bottom: 1px solid #f4f4f4;
        padding-bottom: 5px;
        font-size: 14px;
        font-weight: 600;
    }

    .timeline>div>.timeline-item>.timeline-body {
        padding: 10px 0 0 0;
        font-size: 13px;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    $(function () {
        // VIZ 1: Utilización por Almacén (Horizontal Bar Chart)
        var utilizacionCtx = document.getElementById('utilizacionChart').getContext('2d');
        new Chart(utilizacionCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($nombresAlmacenes) !!},
                datasets: [{
                    label: 'Porcentaje de Ocupación',
                    data: {!! json_encode($porcentajesUtilizacion) !!},
                    backgroundColor: function (context) {
                        var value = context.parsed.y;
                        if (value >= 80) return 'rgba(220, 53, 69, 0.8)';  // Rojo
                        if (value >= 50) return 'rgba(255, 193, 7, 0.8)';  // Amarillo
                        return 'rgba(40, 167, 69, 0.8)';  // Verde
                    },
                    borderColor: function (context) {
                        var value = context.parsed.y;
                        if (value >= 80) return 'rgb(220, 53, 69)';
                        if (value >= 50) return 'rgb(255, 193, 7)';
                        return 'rgb(40, 167, 69)';
                    },
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Ocupación: ' + context.parsed.x + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function (value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // VIZ 2: Estado de Espacios (Doughnut)
        var espaciosCtx = document.getElementById('espaciosChart').getContext('2d');
        new Chart(espaciosCtx, {
            type: 'doughnut',
            data: {
                labels: ['Disponibles', 'Llenos'],
                datasets: [{
                    data: [{{ $espaciosDisponibles }}, {{ $espaciosLlenos }}],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // VIZ 3: Productos por Categoría (Doughnut)
        var categoriasCtx = document.getElementById('categoriasChart').getContext('2d');
        new Chart(categoriasCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($nombresCategorias) !!},
                datasets: [{
                    data: {!! json_encode($cantidadesCategorias) !!},
                    backgroundColor: [
                        '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                        '#6610f2', '#e83e8c', '#fd7e14', '#20c997', '#6c757d'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // VIZ 4: Top 5 Productos Almacenados (Bar Chart)
        var topProductosCtx = document.getElementById('topProductosChart').getContext('2d');
        new Chart(topProductosCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($nombresTopProductos) !!},
                datasets: [{
                    label: 'Cantidad en Stock',
                    data: {!! json_encode($cantidadesTopProductos) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgb(255, 193, 7)',
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
    });
</script>
@stop



