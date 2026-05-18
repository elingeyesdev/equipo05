@extends('adminlte::page')

@section('title', 'Detalles del Paquete')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Detalles del Paquete</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.paquete.index') }}">
            Volver al Listado
        </a>
    </div>
</div>
@stop

@section('content')
@include('inventario::partials.flash-messages')
{{-- Info Boxes Row --}}
<div class="row">
    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-barcode"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Código del Paquete</span>
                <span class="info-box-number" style="font-size: 1rem;">{{ $paquete->codigo_paquete ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Fecha de Creación</span>
                <span class="info-box-number" style="font-size: 0.9rem;">
                    {{ $paquete->fecha_creacion ? \Carbon\Carbon::parse($paquete->fecha_creacion)->format('d/m/Y H:i') : 'N/A' }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            @php
                $badgeClass = match ($paquete->estado) {
                    'despachado' => 'success',
                    'en_proceso' => 'primary',
                    'cancelado' => 'danger',
                    default => 'warning'
                };
            @endphp
            <span class="info-box-icon bg-{{ $badgeClass }}"><i class="fas fa-clipboard-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Estado</span>
                <span class="info-box-number" style="font-size: 1.1rem;">
                    {{ ucfirst(str_replace('_', ' ', $paquete->estado ?? 'pendiente')) }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Main Information Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Información del Paquete</h3>
        <div class="card-tools">
            <a href="{{ route('inventario.paquete.edit', $paquete->id_paquete) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Código del Paquete:</dt>
            <dd class="col-sm-9"><strong>{{ $paquete->codigo_paquete ?? 'N/A' }}</strong></dd>

            <dt class="col-sm-3">Fecha de Creación:</dt>
            <dd class="col-sm-9">
                {{ $paquete->fecha_creacion ? \Carbon\Carbon::parse($paquete->fecha_creacion)->format('d/m/Y H:i') : 'N/A' }}
            </dd>

            <dt class="col-sm-3">Estado:</dt>
            <dd class="col-sm-9">
                @php
                    $badgeClass = match ($paquete->estado) {
                        'despachado' => 'success',
                        'en_proceso' => 'primary',
                        'cancelado' => 'danger',
                        default => 'warning'
                    };
                @endphp
                <span class="badge badge-{{ $badgeClass }} badge-lg">
                    {{ ucfirst(str_replace('_', ' ', $paquete->estado ?? 'Pendiente')) }}
                </span>
            </dd>

            @if($paquete->ci_usuario_registro)
                <dt class="col-sm-3">Registrado por (CI):</dt>
                <dd class="col-sm-9">
                    <span class="badge badge-info badge-lg">
                        <i class="fas fa-id-card"></i> {{ $paquete->ci_usuario_registro }}
                    </span>
                </dd>
            @endif
        </dl>
    </div>
</div>

{{-- Productos del Paquete --}}
<div class="card card-secondary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-box-open"></i> Contenido del Paquete</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Origen (Donación)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($paquete->paqueteDetalles as $detalle)
                    <tr>
                        <td>{{ $detalle->donacionDetalle?->producto->nombre ?? 'N/A' }}</td>
                        <td>{{ $detalle->donacionDetalle?->producto->descripcion ?? '-' }}</td>
                        <td><strong>{{ $detalle->cantidad_usada }}</strong>
                            {{ $detalle->donacionDetalle?->unidad_medida ?? 'unidades' }}</td>
                        <td>
                            @if($detalle->donacionDetalle?->donacion)
                                <a href="{{ route('inventario.donante.show', $detalle->donacionDetalle->donacion->id_donante) }}">
                                    Donación #{{ $detalle->donacionDetalle->id_donacion }}
                                </a>
                                <small class="text-muted d-block">
                                    {{ $detalle->donacionDetalle->donacion->donante->nombre ?? 'Anónimo' }}
                                </small>
                            @else
                                <span class="text-muted">Origen desconocido</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">Este paquete no tiene productos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Action Buttons --}}
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('inventario.paquete.index') }}" class="btn btn-secondary">
            Volver al Listado
        </a>
        <a href="{{ route('inventario.paquete.edit', $paquete->id_paquete) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar Paquete
        </a>
        <form action="{{ route('inventario.paquete.destroy', $paquete->id_paquete) }}" method="POST" style="display: inline;"
            onsubmit="return confirm('¿Está seguro de eliminar este paquete?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.4rem 0.6rem;
    }
</style>
@stop




