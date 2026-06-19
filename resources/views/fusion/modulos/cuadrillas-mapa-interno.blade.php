@extends('layouts.app')

@section('content_header_title', 'Mapa en tiempo real')
@section('content_header_subtitle', 'NASA FIRMS + focos del sistema de incendios, equipos y reportes')

@section('content')
@include('fusion.modulos.partials.cuadrillas-module-nav')
@include('fusion.modulos.partials.cuadrillas-flash')

<div class="card cua-list-card cua-accent-danger shadow-sm mb-3">
    <div class="card-header">
        <div class="cua-btn-toolbar w-100">
            <div class="cua-map-filters flex-grow-1 mr-md-3 mb-2 mb-md-0 p-0 border-0 bg-transparent">
                <div class="d-flex flex-wrap align-items-center" style="gap:0.5rem;">
                    <span class="small font-weight-bold text-muted mr-1"><i class="fas fa-satellite text-danger"></i> Período FIRMS:</span>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" data-cua-days="1">24 h</button>
                        <button type="button" class="btn btn-outline-secondary active" data-cua-days="2">2 días</button>
                        <button type="button" class="btn btn-outline-secondary" data-cua-days="7">7 días</button>
                    </div>
                </div>
            </div>
            <a href="{{ route('cuadrillas.reportes') }}" class="btn btn-warning btn-sm">
                <i class="fas fa-exclamation-triangle"></i> Nuevo reporte
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="mapa-tiempo-real" class="cua-map-container"></div>
    </div>
    <div class="card-footer py-2 small text-muted d-flex flex-wrap justify-content-between">
        <span><i class="fas fa-sync-alt mr-1"></i> Actualización automática cada 5 min · misma API que módulo Incendios</span>
        <span>Última carga: <strong id="lbl-update-time">{{ now()->format('d/m/Y H:i') }}</strong></span>
    </div>
</div>

<div class="row cua-kpi-row">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="small-box bg-primary">
            <div class="inner"><h3 id="lbl-equipos-count">{{ $countEquiposDesplegados }}</h3><p>Equipos desplegados</p></div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="small-box bg-warning">
            <div class="inner"><h3 id="lbl-reportes-count">{{ $countReportes }}</h3><p>Reportes en mapa</p></div>
            <div class="icon"><i class="fas fa-bullhorn"></i></div>
            <a href="{{ route('cuadrillas.reportes') }}" class="small-box-footer text-dark">Ver reportes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="small-box bg-danger">
            <div class="inner"><h3 id="lbl-nasa-count">0</h3><p>Focos NASA FIRMS</p></div>
            <div class="icon"><i class="fas fa-satellite"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="small-box bg-info">
            <div class="inner"><h3 id="lbl-registrados-count">0</h3><p>Focos registrados (BD)</p></div>
            <div class="icon"><i class="fas fa-fire"></i></div>
            <div class="small-box-footer py-2">Último reporte: <strong id="lbl-ultimo-reporte">{{ $ultimoReporte }}</strong></div>
        </div>
    </div>
</div>
@endsection

@include('fusion.modulos.partials.cuadrillas-mapa-script')
