@extends('layouts.app')

@section('content_header_title', 'Universidades')
@section('content_header_subtitle', 'Catálogo de instituciones vinculadas a brigadas')

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="row seg-kpi-row mb-3">
    <div class="col-md-4 mb-2">
        <div class="small-box kpi-universidades">
            <div class="inner"><h3>{{ $universidades->count() }}</h3><p>Universidades</p></div>
            <div class="icon"><i class="fas fa-university"></i></div>
        </div>
    </div>
    <div class="col-md-4 mb-2">
        <div class="small-box kpi-vol-activos">
            <div class="inner"><h3>{{ $totalVoluntarios }}</h3><p>Voluntarios vinculados</p></div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
</div>

<div class="card seg-list-card shadow-sm mb-3">
    <div class="card-header">
        <div class="seg-btn-toolbar w-100">
            @if(\App\Support\FusionModuloAccess::canWriteSeguimientoSection($seccion))
            <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Agregar universidad
            </a>
            @endif
        </div>
    </div>
</div>

@if($universidades->isEmpty())
    <div class="alert alert-light border text-center py-5">
        <i class="fas fa-university fa-3x text-muted mb-3 d-block"></i>
        <p class="mb-0 text-muted">No hay universidades registradas.</p>
    </div>
@else
    <div class="seg-uni-grid">
        @foreach($universidades as $uni)
            <div class="seg-uni-card shadow-sm">
                <h3 class="h6 font-weight-bold mb-1">{{ $uni->nombre }}</h3>
                <div class="text-muted small mb-2">
                    @if(!empty($uni->sigla))<span class="badge badge-light border mr-1">{{ $uni->sigla }}</span>@endif
                    @if(!empty($uni->ciudad))<i class="fas fa-map-marker-alt mr-1"></i>{{ $uni->ciudad }}@endif
                </div>
                <p class="mb-2 small"><strong>{{ $uni->voluntarios_count ?? 0 }}</strong> voluntario(s)</p>
                <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $uni->id_universidad]) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        @endforeach
    </div>
@endif
@endsection
