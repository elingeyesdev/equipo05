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
<div class="card logistica-list-card shadow-sm">
    <div class="card-header logistica-crud-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <strong>Conductores</strong>
            <p class="mb-0 small text-muted">Personal asignado a rutas y entregas.</p>
        </div>
        <a href="{{ route('logistica.crud.create', ['seccion' => 'conductor']) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nuevo conductor
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm logistica-tabla-operativa logistica-flota-table mb-0">
                <thead>
                    <tr>
                        <th class="col-ref">#</th>
                        <th>Nombre completo</th>
                        <th>Apellido</th>
                        <th class="col-acciones text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conductores as $index => $conductor)
                    <tr>
                        <td class="col-ref text-muted">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</td>
                        <td><strong>{{ $conductor->nombre ?? '—' }}</strong></td>
                        <td>{{ $conductor->apellido ?? '—' }}</td>
                        <td class="col-acciones text-right">
                            <a href="{{ route('logistica.crud.edit', ['seccion' => 'conductor', 'id' => $conductor->id_conductor]) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-muted text-center py-5">No hay conductores registrados. Agregue uno para asignar entregas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="card logistica-list-card shadow-sm">
    <div class="card-header logistica-crud-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <strong>Vehículos</strong>
            <p class="mb-0 small text-muted">Unidades de transporte para despacho de paquetes.</p>
        </div>
        <a href="{{ route('logistica.crud.create', ['seccion' => 'vehiculo']) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Nuevo vehículo
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm logistica-tabla-operativa logistica-flota-table mb-0">
                <thead>
                    <tr>
                        <th class="col-ref">#</th>
                        <th>Placa</th>
                        @if($vehiculoTieneModelo)
                        <th>Modelo</th>
                        @endif
                        @if($vehiculoTieneCapacidad)
                        <th>Capacidad</th>
                        @endif
                        <th class="col-acciones text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehiculos as $index => $vehiculo)
                    <tr>
                        <td class="col-ref text-muted">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</td>
                        <td><span class="badge badge-light border font-weight-bold">{{ $vehiculo->placa ?? 'SIN PLACA' }}</span></td>
                        @if($vehiculoTieneModelo)
                        <td>{{ $vehiculo->modelo ?? '—' }}</td>
                        @endif
                        @if($vehiculoTieneCapacidad)
                        <td>{{ $vehiculo->capacidad ?? '—' }}</td>
                        @endif
                        <td class="col-acciones text-right">
                            <a href="{{ route('logistica.crud.edit', ['seccion' => 'vehiculo', 'id' => $vehiculo->id_vehiculo]) }}" class="btn btn-outline-primary btn-sm" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-muted text-center py-5">No hay vehículos registrados. Agregue la flota disponible.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<p class="small text-muted mt-3 mb-0">
    Marcas, tipos y licencias se configuran en
    <a href="{{ route('logistica.configuracion') }}">Configuración → Flota</a>.
</p>
@endsection
