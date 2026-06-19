@extends('layouts.app')

@section('content_header_title', 'Dashboard')
@section('content_header_subtitle', 'Alas Chiquitanas — centro de operaciones')

@section('content')
@include('fusion.modulos.partials.cuadrillas-module-nav')
@include('fusion.modulos.partials.cuadrillas-flash')

<div class="card cua-welcome-card shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="m-0 font-weight-bold text-dark d-flex align-items-center">
            <i class="fas fa-leaf text-success mr-2"></i> Bienvenido al Sistema de Gestión — Alas Chiquitanas
        </h5>
    </div>
    <div class="card-body py-4">
        <div class="row align-items-center">
            <div class="col-md-9 col-sm-8">
                <h5 class="cua-welcome-org mb-3">
                    <i class="fas fa-leaf mr-1"></i> Organización Voluntaria de Conservación Ambiental
                </h5>
                <p class="text-secondary cua-welcome-text">
                    Este dashboard es el centro de operaciones para la gestión de actividades de conservación, monitoreo ambiental y respuesta ante emergencias en la Chiquitania.
                </p>
                <p class="text-secondary cua-welcome-text mb-0">
                    Desde aquí podrás acceder a todas las herramientas necesarias para documentar, reportar y coordinar nuestras acciones de protección de la biodiversidad y los ecosistemas locales.
                </p>
            </div>
            <div class="col-md-3 col-sm-4 text-center d-none d-sm-block">
                <i class="fas fa-tree cua-welcome-tree" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</div>

<div class="card cua-quickview-card shadow-sm mb-3">
    <div class="card-header cua-quickview-header py-3">
        <h5 class="m-0 font-weight-bold d-flex align-items-center">
            <i class="fas fa-chart-line mr-2"></i> Vista rápida del sistema
        </h5>
    </div>
    <div class="card-body py-3">
        <div class="row cua-kpi-row mb-0">
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
    </div>
    <div class="card-footer bg-white text-center text-muted small py-2">
        Los contadores se actualizan conforme uses el sistema
    </div>
</div>
@endsection
