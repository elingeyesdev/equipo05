@extends('adminlte::page')

@section('title', 'Estadísticas')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-chart-line"></i> Estadísticas Principales</h1>
    <small class="text-muted">Sistema de Gestión de Donaciones</small>
</div>
@stop

@section('content')
<div class="container-fluid">
    {{-- SECCIÓN 1: KPIs Principales - Fila 1 --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-gift"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Donaciones</span>
                    <span class="info-box-number">{{ number_format($totalDonaciones) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-box"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Paquetes Creados</span>
                    <span class="info-box-number">{{ number_format($totalPaquetes) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-truck-loading"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Salidas Registradas</span>
                    <span class="info-box-number">{{ number_format($totalSalidas) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-clipboard-list"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Solicitudes Pendientes</span>
                    <span class="info-box-number">{{ number_format($solicitudesPendientes) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 2: KPIs Secundarios - Fila 2 --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-hand-holding-heart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Donantes</span>
                    <span class="info-box-number">{{ number_format($totalDonantes) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-box-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Productos Registrados</span>
                    <span class="info-box-number">{{ number_format($totalProductos) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-teal elevation-1"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Donaciones Dinero</span>
                    <span class="info-box-number">Bs. {{ number_format($totalDonacionesDinero, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-indigo elevation-1"><i class="fas fa-calendar-day"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Promedio Donaciones/Día</span>
                    <span class="info-box-number">{{ $promedioDonacionesDia }}</span>
                    <small class="text-muted">Últimos 30 días</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 3: Análisis de Tendencias --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Tendencia de Donaciones (12 Meses)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="donacionesTrendChart"
                        style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Análisis histórico del último año - Visualización de tendencias</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Tendencia Donaciones en Dinero</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="dineroChart" style="min-height: 250px; height: 250px; max-height: 250px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Evolución de donaciones monetarias (12 meses)</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 4: Distribución y Rankings --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-boxes"></i> Estado de Paquetes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="paquetesChart" style="min-height: 250px; height: 250px; max-height: 250px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Distribución por estado actual</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tags"></i> Top 5 Categorías Más Donadas</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="categoriasChart" style="min-height: 250px; height: 250px; max-height: 250px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Categorías con mayor volumen de donaciones</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 5: Top Donantes y Actividad Reciente --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-medal"></i> Top 5 Donantes Más Activos</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="donantesChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Donantes con mayor cantidad de donaciones registradas</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Actividad Reciente</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 350px; overflow-y: auto;">
                    @if(count($actividadesRecientes) > 0)
                        <div class="timeline">
                            @foreach($actividadesRecientes as $actividad)
                                <div>
                                    <i class="{{ $actividad['icono'] }} bg-{{ $actividad['color'] }}"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i>
                                            {{ $actividad['fecha']->diffForHumans() }}</span>
                                        <h3 class="timeline-header">{{ $actividad['titulo'] }}</h3>
                                        <div class="timeline-body">
                                            {{ $actividad['descripcion'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-muted">No hay actividad reciente</p>
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

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .bg-teal {
        background-color: #20c997 !important;
    }

    .bg-indigo {
        background-color: #6610f2 !important;
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    $(function () {
        // VISUALIZACIÓN 1: Tendencia de Donaciones (Line Chart)
        var donacionesTrendCtx = document.getElementById('donacionesTrendChart').getContext('2d');
        new Chart(donacionesTrendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($mesesLabels) !!},
                datasets: [{
                    label: 'Donaciones',
                    data: {!! json_encode($cantidadesDonaciones) !!},
                    borderColor: 'rgb(60, 141, 188)',
                    backgroundColor: 'rgba(60, 141, 188, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(60, 141, 188)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                return 'Donaciones: ' + context.parsed.y;
                            }
                        }
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


        // VISUALIZACIÓN 2: Tendencia Donaciones en Dinero (Line Chart)
        var dineroCtx = document.getElementById('dineroChart').getContext('2d');
        new Chart(dineroCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($mesesDineroLabels) !!},
                datasets: [{
                    label: 'Monto en Bs.',
                    data: {!! json_encode($montoDonacionesDinero) !!},
                    borderColor: 'rgb(40, 167, 69)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(40, 167, 69)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (context) {
                                return 'Monto: Bs. ' + context.parsed.y.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Bs. ' + value.toLocaleString('es-BO');
                            }
                        }
                    }
                }
            }
        });


        // VISUALIZACIÓN 3: Estado de Paquetes (Doughnut Chart)
        var paquetesCtx = document.getElementById('paquetesChart').getContext('2d');
        new Chart(paquetesCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($estadosPaquetes) !!},
                datasets: [{
                    data: {!! json_encode($cantidadesPaquetes) !!},
                    backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.parsed;
                            }
                        }
                    }
                }
            }
        });

        // VISUALIZACIÓN 4: Top 5 Categorías (Bar Chart)
        var categoriasCtx = document.getElementById('categoriasChart').getContext('2d');
        new Chart(categoriasCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($nombresTopCategorias) !!},
                datasets: [{
                    label: 'Cantidad de Donaciones',
                    data: {!! json_encode($cantidadesTopCategorias) !!},
                    backgroundColor: 'rgba(23, 162, 184, 0.8)',
                    borderColor: 'rgb(23, 162, 184)',
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

        // VISUALIZACIÓN 5: Top 5 Donantes (Horizontal Bar Chart)
        var donantesCtx = document.getElementById('donantesChart').getContext('2d');
        new Chart(donantesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($nombresTopDonantes) !!},
                datasets: [{
                    label: 'Número de Donaciones',
                    data: {!! json_encode($cantidadesTopDonantes) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: 'rgb(0, 123, 255)',
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
                    }
                },
                scales: {
                    x: {
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




