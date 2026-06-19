@extends('layouts.app')

@section('content_header_title', 'Seguimiento de paquetes')
@section('content_header_subtitle', 'Historial de avances en ruta y entregas')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            <span class="badge badge-light border mr-auto">{{ $seguimientos->count() }} registros</span>
            <a href="{{ route('logistica.crud.create', ['seccion' => 'seguimiento']) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Registrar avance
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row seguimiento-grid">
            @forelse($seguimientos as $item)
                @php
                    $estado = strtolower((string) ($item->estado ?? ''));
                    $badgeClass = str_contains($estado, 'entreg') ? 'badge-success' : (str_contains($estado, 'camino') ? 'badge-info' : 'badge-warning');
                    $estadoClass = str_contains($estado, 'entreg') ? 'entregado' : (str_contains($estado, 'camino') ? 'camino' : 'otro');
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                    <div class="card logistica-seg-card estado-{{ $estadoClass }} h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Paquete {{ $item->paquete_ref ?? ('#'.str_pad((string) ($item->id_paquete ?? 0), 4, '0', STR_PAD_LEFT)) }}</strong>
                            <span class="badge {{ $badgeClass }} text-uppercase">{{ $item->estado ?? '-' }}</span>
                        </div>
                        <div class="card-body py-2">
                            <p class="mb-1 small"><strong>Actualización:</strong> {{ $item->fecha_actualizacion ?? '-' }}</p>
                            <p class="mb-1 small"><strong>Conductor:</strong> {{ $item->conductor_nombre ?? '-' }}</p>
                            <p class="mb-1 small"><strong>CI:</strong> {{ $item->conductor_ci ?? '-' }}</p>
                            <p class="mb-0 small"><strong>Placa:</strong> {{ $item->vehiculo_placa ?? '-' }}</p>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-end py-2">
                            <span class="logistica-row-actions">
                                <a href="{{ route('logistica.crud.edit', ['seccion' => 'seguimiento', 'id' => $item->id_historial]) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('logistica.crud.destroy', ['seccion' => 'seguimiento', 'id' => $item->id_historial]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro de seguimiento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted text-center py-4">No hay seguimientos registrados.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .seguimiento-grid .col-lg-3,
    .seguimiento-grid .col-md-4,
    .seguimiento-grid .col-sm-6 { display: flex; }
    .seguimiento-grid .logistica-seg-card { width: 100%; }
</style>
@endpush
