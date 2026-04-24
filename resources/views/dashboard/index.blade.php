@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <h1 class="m-0 text-dark">
        <i class="fas fa-tachometer-alt mr-2"></i>
        Información general
    </h1>
    <p class="text-muted mb-0">
        Resumen de campañas, donaciones, donantes, mensajes y asignaciones.
    </p>
@endsection

@section('content')
<div class="container-fluid">

    {{-- FILA 1: INFO-BOXES PRINCIPALES INTERACTIVOS --}}
    <div class="row">
        
        {{-- 1. CAMPAÑAS ACTIVAS -> Ir a lista de campañas --}}
        <div class="col-12 col-sm-6 col-md-3">
            <a href="{{ route('campanias.index') }}" class="info-box shadow-sm" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span class="info-box-icon bg-info elevation-1">
                    <i class="fas fa-bullhorn"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Campañas activas</span>
                    <span class="info-box-number">
                        {{ $campaniasActivas }}
                        <small class="text-muted"> / {{ $totalCampanias }}</small>
                    </span>
                </div>
            </a>
        </div>

        {{-- 2. DONACIONES REGISTRADAS -> Ir a lista de donaciones --}}
        <div class="col-12 col-sm-6 col-md-3">
            <a href="{{ route('donaciones.index') }}" class="info-box mb-3 shadow-sm" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span class="info-box-icon bg-danger elevation-1">
                    <i class="fas fa-hand-holding-heart"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Donaciones registradas</span>
                    <span class="info-box-number">{{ $totalDonaciones }}</span>
                </div>
            </a>
        </div>

        {{-- Separador para móviles --}}
        <div class="clearfix hidden-md-up"></div>

        {{-- 3. TOTAL RECAUDADO -> Ir a Reporte Financiero --}}
        <div class="col-12 col-sm-6 col-md-3">
            <a href="{{ route('reporte.cierreCaja') }}" class="info-box mb-3 shadow-sm" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span class="info-box-icon bg-success elevation-1">
                    <i class="fas fa-coins"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total recaudado</span>
                    <span class="info-box-number">
                        Bs {{ number_format($montoDonadoTotal, 2, ',', '.') }}
                    </span>
                </div>
            </a>
        </div>

        {{-- 4. DONANTES ÚNICOS -> Ir a lista de usuarios --}}
        <div class="col-12 col-sm-6 col-md-3">
            <a href="{{ route('donaciones.index') }}" class="info-box mb-3 shadow-sm" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <span class="info-box-icon bg-warning elevation-1">
                    <i class="fas fa-users"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Donantes únicos</span>
                    <span class="info-box-number">
                        {{ $donantesUnicos }}
                        <small class="text-muted"> / {{ $totalUsuarios }} usuarios</small>
                    </span>
                </div>
            </a>
        </div>
    </div>

    {{-- FILA 2: GRÁFICO DE DONACIONES + METAS DE CAMPAÑAS --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Reporte de donaciones</h5>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <p class="text-center">
                                <strong>Donaciones monetarias - Últimos meses</strong>
                            </p>
                            <div class="chart">
                                <canvas id="donacionesMesChart" height="180" style="height:180px;"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <p class="text-center">
                                <strong>Metas de recaudación</strong>
                            </p>
                                @forelse($topCampanias as $c)
                                    @php
                                        $meta = $c->metarecaudacion ?? 0;
                                        // NUEVO: usar lo que viene calculado desde la consulta
                                        $reca = $c->recaudado_monetario ?? 0;
                                        $avance = $meta > 0 ? round(($reca / $meta) * 100) : 0;
                                    @endphp
                                <div class="progress-group">
                                    {{ $c->titulo }}
                                    <span class="float-right">
                                        <b>Bs {{ number_format($reca, 0, ',', '.') }}</b>
                                        / {{ number_format($meta, 0, ',', '.') }}
                                    </span>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar bg-primary" style="width: {{ min($avance, 100) }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No hay campañas registradas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 3: MENSAJES + ÚLTIMOS DONANTES --}}
    <div class="row">
        {{-- Resumen de mensajes --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Mensajes</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">
                            {{ $mensajesNoLeidos }} sin leer
                        </span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="p-3">
                        <p class="text-muted mb-2">
                            Total de mensajes: <strong>{{ $mensajesTotales }}</strong>
                        </p>
                    </div>

                    {{-- Lista de últimos mensajes --}}
                    <ul class="list-group list-group-flush">
                        @forelse($ultimosMensajes as $m)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <strong>{{ $m->asunto }}</strong><br>
                                    <small class="text-muted">
                                        De:
                                        {{ optional($m->remitente)->nombre }}
                                        {{ optional($m->remitente)->apellido }}
                                        — {{ $m->fechaenvio }}
                                    </small>
                                </div>
                                <span class="badge badge-{{ $m->leido ? 'secondary' : 'success' }}">
                                    {{ $m->leido ? 'Leído' : 'Nuevo' }}
                                </span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted text-center">
                                No hay mensajes registrados.
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-right">
                    <a href="{{ route('mensajes.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-arrow-right mr-1"></i>
                        Ir al módulo de mensajes
                    </a>
                </div>
            </div>
        </div>

        {{-- Últimos usuarios (donantes) --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Últimos usuarios / donantes</h3>
                    <div class="card-tools">
                        <span class="badge badge-danger">{{ $ultimosUsuarios->count() }} nuevos</span>
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="users-list clearfix">
                        @forelse($ultimosUsuarios as $u)
                            <li>
                                <i class="fas fa-user-circle fa-3x mb-2 text-secondary"></i>
                                <a class="users-list-name" href="#">
                                    {{ $u->nombre }} {{ $u->apellido }}
                                </a>
                                <span class="users-list-date">
                                    {{ $u->email }}
                                </span>
                            </li>
                        @empty
                            <li class="text-center text-muted w-100">No hay usuarios registrados.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('usuarios.index') }}">Ver todos los usuarios</a>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 4: ÚLTIMAS DONACIONES + ASIGNACIONES --}}
    <div class="row align-items-stretch">

        {{-- IZQUIERDA --}}
        <div class="col-md-8 d-flex">
            <div class="card w-100 h-100">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Últimas donaciones</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Campaña</th>
                                <th>Donante</th>
                                <th>Estado</th>
                                <th class="text-right">Monto</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($ultimasDonaciones as $d)
                                <tr>
                                    <td><a href="#">#{{ $d->donacionid }}</a></td>
                                    <td>{{ optional($d->campania)->titulo ?? '—' }}</td>
                                    <td>
                                        @if($d->esanonima)
                                            <span class="text-muted">Anónimo</span>
                                        @else
                                            {{ optional($d->usuario)->nombre }} {{ optional($d->usuario)->apellido }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if(optional($d->estado)->nombre === 'Confirmada') badge-success
                                            @elseif(optional($d->estado)->nombre === 'Pendiente') badge-warning
                                            @elseif(optional($d->estado)->nombre === 'Utilizada') badge-primary
                                            @else badge-secondary @endif">
                                            {{ optional($d->estado)->nombre ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        Bs {{ number_format($d->monto, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        No hay donaciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer clearfix">
                    <a href="{{ route('donaciones.index') }}" class="btn btn-sm btn-secondary float-right">
                        Ver todas las donaciones
                    </a>
                </div>
            </div>
        </div>

    {{-- DERECHA --}}
    <div class="col-md-4 d-flex">
        <div class="card w-100 h-100 d-flex flex-column">
            <div class="card-header">
                <h3 class="card-title mb-0">Resumen de asignaciones</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>

            <div class="card-body d-flex flex-column">

                {{-- 1. ASIGNACIONES REGISTRADAS -> Ir a lista de asignaciones --}}
                <a href="{{ route('asignaciones.index') }}" class="info-box bg-info mb-3 shadow-sm" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <span class="info-box-icon"><i class="fas fa-tasks"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Asignaciones registradas</span>
                        <span class="info-box-number">{{ $asignacionesTotal }}</span>
                        <span class="progress-description" style="font-size: 10px">Click para ver detalles</span>
                    </div>
                </a>

                {{-- 2. MONTO TOTAL ASIGNADO -> Ir a lista de asignaciones --}}
                <a href="{{ route('asignaciones.index') }}" class="info-box bg-success mb-3 shadow-sm" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Monto total asignado</span>
                        <span class="info-box-number">
                            Bs {{ number_format($asignacionesMonto, 2, ',', '.') }}
                        </span>
                        <span class="progress-description" style="font-size: 10px">Click para ver desglose</span>
                    </div>
                </a>

                {{-- 3. FONDOS UTILIZADOS -> Ir a Reporte de Cierre de Caja (Comparativa) --}}
                <a href="{{ route('reporte.cierreCaja') }}" class="info-box bg-warning mb-3 shadow-sm" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Fondos utilizados</span>
                        <span class="info-box-number">
                            @php
                                $porcentaje = $montoDonadoTotal > 0 ? round(($asignacionesMonto / $montoDonadoTotal) * 100, 1) : 0;
                            @endphp
                            {{ $porcentaje }}%
                        </span>
                        <span class="progress-description" style="font-size: 10px">Ir a reporte financiero</span>
                    </div>
                </a>

                {{-- Botón inferior (Lo mantengo como acceso rápido extra) --}}
                <div class="mt-auto">
                    <a href="{{ route('asignaciones.index') }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-list mr-2"></i>
                        Ver detalle de asignaciones
                    </a>
                </div>

            </div>
        </div>
    </div>

    </div>

</div>
@endsection

@push('scripts')
    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const ctx = document.getElementById('donacionesMesChart');
            if (!ctx) return;

            const labels = @json($chartMeses);
            const data   = @json($chartMontos);

            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Bs donados',
                        data: data,
                        backgroundColor: 'rgba(60,141,188,0.1)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(60,141,188,1)',
                        fill: true,
                        borderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Bs ' + context.parsed.y.toLocaleString('es-BO', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Bs ' + value.toLocaleString('es-BO');
                                }
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endpush