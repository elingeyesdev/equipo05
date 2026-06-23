@extends('layouts.app')

@section('content_header_title', 'Flota y personal')
@section('content_header_subtitle', 'Vehículos y conductores disponibles para despacho')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

@php
    $tab = request()->query('tab', 'vehiculos');
@endphp

<div class="logistica-flota-tabs card logistica-list-card shadow-sm mb-3">
    <div class="card-body py-2">
        <ul class="nav nav-pills logistica-flota-nav mb-0">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'vehiculos' ? 'active' : '' }}" href="{{ route('logistica.flota', ['tab' => 'vehiculos']) }}">
                    <i class="fas fa-truck mr-1"></i> Vehículos
                    <span class="badge badge-light ml-1">{{ $vehiculos->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'conductores' ? 'active' : '' }}" href="{{ route('logistica.flota', ['tab' => 'conductores']) }}">
                    <i class="fas fa-id-badge mr-1"></i> Conductores
                    <span class="badge badge-light ml-1">{{ $conductores->count() }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>

@if($tab === 'conductores')
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
    <div>
        <h5 class="mb-0">Conductores</h5>
        <small class="text-muted">Contacto directo para coordinar entregas.</small>
    </div>
    <a href="{{ route('logistica.crud.create', ['seccion' => 'conductor']) }}" class="btn btn-primary btn-sm mt-2 mt-md-0">
        <i class="fas fa-plus"></i> Nuevo conductor
    </a>
</div>

@if($conductores->isEmpty())
    <div class="alert alert-light border text-center py-5">
        <i class="fas fa-user-plus fa-2x text-muted mb-2 d-block"></i>
        No hay conductores registrados. Agregue nombre, CI y teléfono para asignar rutas.
    </div>
@else
<div class="row">
    @foreach($conductores as $conductor)
        @php
            $nombre = trim(($conductor->nombre ?? '') . ' ' . ($conductor->apellido ?? ''));
            $inicial = strtoupper(substr($conductor->nombre ?? 'C', 0, 1));
            $telefono = preg_replace('/\s+/', '', (string) ($conductor->telefono ?? ''));
        @endphp
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card logistica-conductor-card h-100 shadow-sm">
                <div class="card-body d-flex">
                    <div class="logistica-avatar mr-3">{{ $inicial }}</div>
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="mb-1 text-truncate">{{ $nombre ?: 'Sin nombre' }}</h6>
                        @if(!empty($conductor->ci))
                            <span class="badge badge-light border mb-2">CI {{ $conductor->ci }}</span>
                        @endif
                        @if($telefono !== '')
                            <a href="tel:{{ $telefono }}" class="btn btn-success btn-sm btn-block mb-2">
                                <i class="fas fa-phone-alt mr-1"></i> {{ $conductor->telefono }}
                            </a>
                        @else
                            <p class="small text-muted mb-2"><i class="fas fa-phone-slash"></i> Sin teléfono</p>
                        @endif
                        @if(!empty($conductor->email))
                            <p class="small text-muted mb-0 text-truncate"><i class="fas fa-envelope mr-1"></i>{{ $conductor->email }}</p>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white text-right py-2">
                    <a href="{{ route('logistica.crud.edit', ['seccion' => 'conductor', 'id' => $conductor->id_conductor]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif

@else
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
    <div>
        <h5 class="mb-0">Vehículos</h5>
        <small class="text-muted">Unidades registradas con marca, tipo y capacidad.</small>
    </div>
    <a href="{{ route('logistica.crud.create', ['seccion' => 'vehiculo']) }}" class="btn btn-primary btn-sm mt-2 mt-md-0">
        <i class="fas fa-plus"></i> Nuevo vehículo
    </a>
</div>

@if($vehiculos->isEmpty())
    <div class="alert alert-light border text-center py-5">
        <i class="fas fa-truck fa-2x text-muted mb-2 d-block"></i>
        No hay vehículos registrados. Indique placa, marca, modelo y capacidad de carga.
    </div>
@else
<div class="row">
    @foreach($vehiculos as $vehiculo)
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card logistica-vehiculo-card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="logistica-placa-badge">{{ $vehiculo->placa ?? 'SIN PLACA' }}</span>
                        @if(!empty($vehiculo->anio))
                            <span class="badge badge-secondary">{{ $vehiculo->anio }}</span>
                        @endif
                    </div>
                    <h6 class="mb-1">{{ $vehiculo->modelo ?? 'Modelo no indicado' }}</h6>
                    <p class="small text-muted mb-2">
                        @if(!empty($vehiculo->marca_nombre))
                            <i class="fas fa-industry mr-1"></i>{{ $vehiculo->marca_nombre }}
                        @endif
                        @if(!empty($vehiculo->tipo_nombre))
                            · {{ $vehiculo->tipo_nombre }}
                        @endif
                    </p>
                    @if(!empty($vehiculo->capacidad))
                        <p class="mb-0 small"><i class="fas fa-weight-hanging mr-1 text-primary"></i> Capacidad: <strong>{{ $vehiculo->capacidad }}</strong></p>
                    @endif
                    @if(!empty($vehiculo->observaciones))
                        <p class="mb-0 small text-muted mt-2">{{ $vehiculo->observaciones }}</p>
                    @endif
                </div>
                <div class="card-footer bg-white text-right py-2">
                    <a href="{{ route('logistica.crud.edit', ['seccion' => 'vehiculo', 'id' => $vehiculo->id_vehiculo]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endif
@endif

<p class="small text-muted mt-2 mb-0">
    Catálogos de <a href="{{ route('logistica.marca') }}">marcas</a> y <a href="{{ route('logistica.tipo-vehiculo') }}">tipos</a> en Configuración.
</p>
@endsection
