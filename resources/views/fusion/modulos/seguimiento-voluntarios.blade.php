@extends('layouts.app')

@section('content_header_title', 'Estadísticas')
@section('content_header_subtitle', 'Actividad y resumen del módulo de voluntarios')
@if($gestionCompleta ?? false)
@section('subtitle')
    <span class="badge badge-info seg-access-badge">Acceso completo — Administrador / Coordinador</span>
@endsection
@endif

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="row seg-kpi-row mb-3">
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="small-box kpi-vol-activos">
            <div class="inner"><h3>{{ $voluntariosActivos }}</h3><p>Voluntarios activos</p></div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('seguimiento.voluntarios') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="small-box kpi-vol-inactivos">
            <div class="inner"><h3>{{ $voluntariosInactivos }}</h3><p>Voluntarios inactivos</p></div>
            <div class="icon"><i class="fas fa-user-slash"></i></div>
            <a href="{{ route('seguimiento.voluntarios-inactivos') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="small-box kpi-alertas">
            <div class="inner"><h3>{{ $alertasRecientes }}</h3><p>Alertas recientes</p></div>
            <div class="icon"><i class="fas fa-heartbeat"></i></div>
            <a href="{{ route('seguimiento.ayudas-solicitadas') }}" class="small-box-footer">Ver reportes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="small-box kpi-evaluaciones">
            <div class="inner"><h3>{{ $evaluacionesCompletadas }}</h3><p>Evaluaciones completadas</p></div>
            <div class="icon"><i class="fas fa-chart-bar"></i></div>
            <a href="{{ route('seguimiento.evaluacion-pruebas') }}" class="small-box-footer">Ver más <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

@if($gestionCompleta ?? false)
<div class="row seg-kpi-row mb-3">
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="small-box kpi-admins">
            <div class="inner"><h3>{{ $totalAdministradores ?? 0 }}</h3><p>Administradores</p></div>
            <div class="icon"><i class="fas fa-user-shield"></i></div>
            <a href="{{ route('seguimiento.administradores') }}" class="small-box-footer">Gestionar <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="small-box kpi-universidades">
            <div class="inner"><h3>{{ $totalUniversidades ?? 0 }}</h3><p>Universidades</p></div>
            <div class="icon"><i class="fas fa-university"></i></div>
            <a href="{{ route('seguimiento.universidades') }}" class="small-box-footer">Ver catálogo <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="small-box kpi-consultas">
            <div class="inner"><h3>{{ $consultasAbiertas ?? 0 }}</h3><p>Consultas abiertas</p></div>
            <div class="icon"><i class="fas fa-life-ring"></i></div>
            <a href="{{ route('seguimiento.helpdesk') }}" class="small-box-footer">Centro de soporte <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="small-box kpi-chat">
            <div class="inner"><h3>{{ $conversacionesChat ?? 0 }}</h3><p>Conversaciones activas</p></div>
            <div class="icon"><i class="fas fa-comments"></i></div>
            <a href="{{ route('seguimiento.chat-consulta') }}" class="small-box-footer">Abrir chat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-lg-6 mb-3">
        <div class="card seg-list-card shadow-sm h-100">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-users mr-1 text-info"></i> Últimos voluntarios</h3></div>
            <div class="card-body p-0 seg-panel-list">
                <ul class="list-group list-group-flush mb-0">
                    @forelse($ultimosVoluntarios as $vol)
                        @php
                            $iniciales = mb_substr($vol->nombre ?? '', 0, 1, 'UTF-8') . mb_substr($vol->apellido ?? '', 0, 1, 'UTF-8');
                            if (empty($iniciales)) { $iniciales = 'V'; }
                        @endphp
                        <li class="list-group-item">
                            <div class="seg-vol-avatar mr-3" style="width:36px;height:36px;font-size:0.85rem;">{{ strtoupper($iniciales) }}</div>
                            <div>
                                <strong>{{ $vol->nombre ?? '' }} {{ $vol->apellido ?? '' }}</strong><br>
                                @if($vol->activo ?? false)
                                    <span class="seg-estado-badge activo">Activo</span>
                                @else
                                    <span class="seg-estado-badge inactivo">Inactivo</span>
                                @endif
                                @if(!empty($vol->created_at))
                                    <small class="text-muted d-block mt-1">{{ \Carbon\Carbon::parse($vol->created_at)->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-4">No hay voluntarios registrados.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card seg-list-card seg-accent-danger shadow-sm h-100">
            <div class="card-header"><h3 class="card-title"><i class="fas fa-file-medical mr-1 text-danger"></i> Últimos reportes</h3></div>
            <div class="card-body p-0 seg-panel-list">
                <ul class="list-group list-group-flush mb-0">
                    @forelse($ultimosReportes as $rep)
                        @php
                            $estado = mb_strtolower($rep->estado ?? 'sin estado', 'UTF-8');
                            $estadoClass = in_array($estado, ['pendiente', 'crítico', 'critico']) ? 'inactivo' : ($estado === 'en_proceso' ? 'activo' : 'activo');
                        @endphp
                        <li class="list-group-item">
                            <div class="seg-vol-avatar mr-3" style="width:36px;height:36px;font-size:0.85rem;background:#dc3545;">R</div>
                            <div>
                                <strong>Reporte #{{ $rep->id }}</strong><br>
                                <span class="seg-estado-badge {{ $estadoClass }}">{{ ucfirst($rep->estado ?? '—') }}</span>
                                @if(!empty($rep->created_at))
                                    <small class="text-muted d-block mt-1">{{ \Carbon\Carbon::parse($rep->created_at)->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-4">No hay reportes generados.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    @foreach([
        ['id' => 'Universidades', 'icon' => 'fa-university', 'data' => $universidadesData, 'accent' => ''],
        ['id' => 'Necesidades', 'icon' => 'fa-clipboard-list', 'data' => $necesidadesData, 'accent' => 'seg-accent-warning'],
        ['id' => 'Capacitaciones', 'icon' => 'fa-chalkboard-teacher', 'data' => $capacitacionesData, 'accent' => 'seg-accent-success'],
    ] as $chart)
    <div class="col-lg-4 mb-3">
        <div class="card seg-list-card {{ $chart['accent'] }} seg-chart-card shadow-sm h-100">
            <div class="card-header"><h3 class="card-title"><i class="fas {{ $chart['icon'] }} mr-1"></i> {{ $chart['id'] }}</h3></div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="seg-chart-container w-100" id="container{{ $chart['id'] }}">
                    <canvas id="chart{{ $chart['id'] }}"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const charts = [
        { key: 'Universidades', data: @json($universidadesData), colors: ['#0891b2','#0d9488','#06b6d4','#22d3ee','#2dd4bf'] },
        { key: 'Necesidades', data: @json($necesidadesData), colors: ['#d97706','#f59e0b','#fbbf24','#fcd34d','#fde68a'] },
        { key: 'Capacitaciones', data: @json($capacitacionesData), colors: ['#059669','#10b981','#34d399','#6ee7b7','#a7f3d0'] },
    ];
    const opts = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } } } };

    charts.forEach(function (c) {
        const hasData = c.data && c.data.length && c.data.some(i => i.total > 0);
        const container = document.getElementById('container' + c.key);
        if (!hasData) {
            container.innerHTML = '<div class="text-muted text-center py-5"><i class="fas fa-chart-pie fa-2x mb-2 d-block opacity-50"></i>Sin datos</div>';
            return;
        }
        new Chart(document.getElementById('chart' + c.key).getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: c.data.map(i => i.label),
                datasets: [{ data: c.data.map(i => i.total), backgroundColor: c.colors.slice(0, c.data.length), borderWidth: 2, borderColor: '#fff' }]
            },
            options: opts
        });
    });
});
</script>
@endpush
