@extends('layouts.app')

@section('content_header_title', 'Dashboard')
@section('content_header_subtitle', 'Alas Chiquitanas — centro de operaciones')

@section('content')
@include('fusion.modulos.partials.cuadrillas-module-nav')
@include('fusion.modulos.partials.cuadrillas-flash')

<div class="card cua-list-card cua-accent-success shadow-sm mb-3">
    <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-leaf mr-1 text-success"></i> Bienvenido</h3></div>
    <div class="card-body">
        <p class="text-muted mb-2">Organización Voluntaria de Conservación Ambiental — gestión de conservación, monitoreo y respuesta ante emergencias en la Chiquitania.</p>
        <p class="text-muted mb-0 small">Accede a reportes, mapa en tiempo real (datos NASA FIRMS sincronizados con el módulo de incendios), noticias y cursos desde la navegación superior.</p>
    </div>
</div>

<div class="row cua-kpi-row">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="small-box cua-kpi-voluntarios">
            <div class="inner"><h3>{{ $voluntariosActivos ?: '—' }}</h3><p>Voluntarios activos</p></div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="small-box cua-kpi-comunarios">
            <div class="inner"><h3>{{ $comunariosApoyo ?: '—' }}</h3><p>Comunarios de apoyo</p></div>
            <div class="icon"><i class="fas fa-handshake"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="small-box cua-kpi-reportes">
            <div class="inner"><h3>{{ $reportesEsteMes ?: '—' }}</h3><p>Reportes este mes</p></div>
            <div class="icon"><i class="fas fa-file-alt"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="small-box cua-kpi-incendios">
            <div class="inner"><h3>{{ $incendiosReportados ?: '—' }}</h3><p>Focos de incendio (30 días)</p></div>
            <div class="icon"><i class="fas fa-fire"></i></div>
            <a href="{{ route('cuadrillas.focos-calor') }}" class="small-box-footer">Ver mapa <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
@endsection
