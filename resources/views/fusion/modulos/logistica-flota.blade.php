@extends('layouts.app')

@section('content_header_title', 'Flota y personal')
@section('content_header_subtitle', 'Vehículos y conductores del módulo logístico')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

@php
    $tab = request()->query('tab', 'vehiculos');
@endphp

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'vehiculos' ? 'active' : '' }}" href="{{ route('logistica.flota', ['tab' => 'vehiculos']) }}">
            <i class="fas fa-truck mr-1"></i> Vehículos ({{ $vehiculos->count() }})
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'conductores' ? 'active' : '' }}" href="{{ route('logistica.flota', ['tab' => 'conductores']) }}">
            <i class="fas fa-id-badge mr-1"></i> Conductores ({{ $conductores->count() }})
        </a>
    </li>
</ul>

@if($tab === 'conductores')
<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            <span class="badge badge-light border mr-auto">Personal de transporte</span>
            <a href="{{ route('logistica.crud.create', ['seccion' => 'conductor']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo conductor
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover logistica-tabla-operativa mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th class="text-right">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conductores as $conductor)
                    <tr>
                        <td>{{ $conductor->nombre ?? '—' }}</td>
                        <td>{{ $conductor->apellido ?? '—' }}</td>
                        <td class="text-right text-nowrap">
                            <a href="{{ route('logistica.crud.edit', ['seccion' => 'conductor', 'id' => $conductor->id_conductor]) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-muted text-center py-4">No hay conductores registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            <span class="badge badge-light border mr-auto">Unidades disponibles</span>
            <a href="{{ route('logistica.crud.create', ['seccion' => 'vehiculo']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo vehículo
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover logistica-tabla-operativa mb-0">
                <thead>
                    <tr>
                        <th>Placa</th>
                        @if($vehiculoTieneModelo)
                        <th>Modelo</th>
                        @endif
                        @if($vehiculoTieneCapacidad)
                        <th>Capacidad</th>
                        @endif
                        <th class="text-right">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehiculos as $vehiculo)
                    <tr>
                        <td>{{ $vehiculo->placa ?? '—' }}</td>
                        @if($vehiculoTieneModelo)
                        <td>{{ $vehiculo->modelo ?? '—' }}</td>
                        @endif
                        @if($vehiculoTieneCapacidad)
                        <td>{{ $vehiculo->capacidad ?? '—' }}</td>
                        @endif
                        <td class="text-right text-nowrap">
                            <a href="{{ route('logistica.crud.edit', ['seccion' => 'vehiculo', 'id' => $vehiculo->id_vehiculo]) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-muted text-center py-4">No hay vehículos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="alert alert-light border mt-3 mb-0 small">
    <i class="fas fa-info-circle mr-1"></i>
    Marcas, tipos de vehículo y licencias se gestionan en
    <a href="{{ route('logistica.configuracion') }}">Configuración</a>.
</div>
@endsection
