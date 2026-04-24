@extends('adminlte::page')

@section('title', 'Dashboard Voluntario')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-hands-helping"></i> Dashboard de Donaciones</h1>
    <small class="text-muted">Vista del Voluntario</small>
</div>
@stop

@section('content')
<div class="container-fluid">
    {{-- SECCIÓN 1: KPIs de Donaciones --}}
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-gift"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Donaciones</span>
                    <span class="info-box-number">{{ number_format($totalDonaciones) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Donaciones Este Mes</span>
                    <span class="info-box-number">{{ number_format($donacionesMesActual) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Donantes</span>
                    <span class="info-box-number">{{ number_format($totalDonantes) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Donaciones en Dinero</span>
                    <span class="info-box-number">Bs. {{ number_format($totalDonacionesDinero, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Promedio Donaciones/Día</span>
                    <span class="info-box-number">{{ $promedioDonacionesDia }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-truck-loading"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Solicitudes Pendientes</span>
                    <span class="info-box-number">{{ number_format($solicitudesPendientes) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 2: Tendencia de Donaciones --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-line"></i> Tendencia de Donaciones (Últimos 12 Meses)
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="tendenciaDonacionesChart"
                        style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Número de donaciones recibidas por mes</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 3: Top Categorías y Estado de Solicitudes --}}
    <div class="row">
        <div class="col-md-7">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tags"></i> Top 5 Categorías de Productos Donados</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="topCategoriasChart"
                        style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Categorías más donadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Estado de Solicitudes</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="solicitudesChart" style="min-height: 250px; height: 250px; max-height: 250px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Distribución de solicitudes de recolección</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 4: Comparación Donaciones Especie vs Dinero --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-balance-scale"></i> Donaciones en Especie vs Dinero (Últimos
                        12 Meses)</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="comparacionChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Comparación de tipos de donaciones recibidas</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 5: Top Donantes --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-trophy"></i> Top 5 Donantes Más Activos</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="topDonantesChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small class="text-muted">Donantes con mayor número de contribuciones</small>
                </div>
            </div>
        </div>
    </div>

    {{-- SECCIÓN 6: Actividad Reciente --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock"></i> Actividad Reciente</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                class="fas fa-minus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
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
                        <p class="text-center text-muted">No hay actividades recientes</p>
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
        // VIZ 1: Tendencia de Donaciones (Line Chart)
        var tendenciaCtx = document.getElementById('tendenciaDonacionesChart').getContext('2d');
        new Chart(tendenciaCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($mesesLabels) !!},
                datasets: [{
                    label: 'Donaciones',
                    data: {!! json_encode($cantidadesDonaciones) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgb(40, 167, 69)',
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

        // VIZ 2: Top 5 Categorías (Horizontal Bar Chart)
        var categoriasCtx = document.getElementById('topCategoriasChart').getContext('2d');
        new Chart(categoriasCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($nombresTopCategorias) !!},
                datasets: [{
                    label: 'Cantidad Donada',
                    data: {!! json_encode($cantidadesTopCategorias) !!},
                    backgroundColor: [
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)',
                        'rgba(23, 162, 184, 0.8)'
                    ],
                    borderColor: [
                        'rgb(0, 123, 255)',
                        'rgb(40, 167, 69)',
                        'rgb(255, 193, 7)',
                        'rgb(220, 53, 69)',
                        'rgb(23, 162, 184)'
                    ],
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

        // VIZ 3: Estado de Solicitudes (Doughnut)
        var solicitudesCtx = document.getElementById('solicitudesChart').getContext('2d');
        new Chart(solicitudesCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($estadosSolicitudes) !!},
                datasets: [{
                    data: {!! json_encode($cantidadesSolicitudes) !!},
                    backgroundColor: [
                        '#ffc107',
                        '#28a745',
                        '#dc3545',
                        '#17a2b8',
                        '#6c757d'
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
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // VIZ 4: Donaciones Especie vs Dinero (Line Chart con 2 series)
        var comparacionCtx = document.getElementById('comparacionChart').getContext('2d');
        new Chart(comparacionCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($mesesComparacionLabels) !!},
                datasets: [{
                    label: 'Donaciones en Especie',
                    data: {!! json_encode($cantidadesDonacionesEspecie) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderColor: 'rgb(0, 123, 255)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Donaciones en Dinero',
                    data: {!! json_encode($cantidadesDonacionesDinero) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.2)',
                    borderColor: 'rgb(255, 193, 7)',
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
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
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

        // VIZ 5: Top 5 Donantes (Bar Chart)
        var donantesCtx = document.getElementById('topDonantesChart').getContext('2d');
        new Chart(donantesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($nombresTopDonantes) !!},
                datasets: [{
                    label: 'Número de Donaciones',
                    data: {!! json_encode($cantidadesTopDonantes) !!},
                    backgroundColor: 'rgba(108, 117, 125, 0.8)',
                    borderColor: 'rgb(108, 117, 125)',
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




