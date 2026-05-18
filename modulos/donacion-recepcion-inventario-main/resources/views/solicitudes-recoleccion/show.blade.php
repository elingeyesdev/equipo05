@extends('adminlte::page')

@section('title', 'Detalles de la Solicitud')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Detalles de la Solicitud</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.solicitudes-recoleccions.index') }}">
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
            <span class="info-box-icon bg-info"><i class="fas fa-user"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Donante</span>
                <span class="info-box-number"
                    style="font-size: 1rem;">{{ $solicitudesRecoleccion->donante->nombre ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Fecha Programada</span>
                <span class="info-box-number" style="font-size: 0.9rem;">
                    {{ \Carbon\Carbon::parse($solicitudesRecoleccion->fecha_programada)->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            @php
                $badgeClass = match ($solicitudesRecoleccion->estado) {
                    'completada' => 'success',
                    'en_proceso' => 'primary',
                    'cancelada' => 'danger',
                    default => 'warning'
                };
            @endphp
            <span class="info-box-icon bg-{{ $badgeClass }}"><i class="fas fa-clipboard-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Estado</span>
                <span class="info-box-number" style="font-size: 1.1rem;">
                    {{ ucfirst(str_replace('_', ' ', $solicitudesRecoleccion->estado ?? 'pendiente')) }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Main Information Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Información General</h3>
        <div class="card-tools">
            <a href="{{ route('inventario.solicitudes-recoleccions.edit', $solicitudesRecoleccion->id_solicitud) }}"
                class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Donante:</dt>
            <dd class="col-sm-9">
                @if($solicitudesRecoleccion->donante)
                    <strong>{{ $solicitudesRecoleccion->donante->nombre }}</strong>
                    <br><small class="text-muted">{{ $solicitudesRecoleccion->donante->tipo ?? 'N/A' }}</small>
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </dd>

            <dt class="col-sm-3">Recolector:</dt>
            <dd class="col-sm-9">
                @if($solicitudesRecoleccion->usuario)
                    <strong>{{ $solicitudesRecoleccion->usuario->nombres }}
                        {{ $solicitudesRecoleccion->usuario->apellidos }}</strong>
                @else
                    <span class="badge badge-secondary">Sin asignar</span>
                @endif
            </dd>

            <dt class="col-sm-3">Dirección de Recolección:</dt>
            <dd class="col-sm-9">{{ $solicitudesRecoleccion->direccion_recoleccion }}</dd>

            <dt class="col-sm-3">Fecha Programada:</dt>
            <dd class="col-sm-9">
                {{ \Carbon\Carbon::parse($solicitudesRecoleccion->fecha_programada)->format('d/m/Y H:i') }}
            </dd>

            <dt class="col-sm-3">Estado:</dt>
            <dd class="col-sm-9">
                @php
                    $badgeClass = match ($solicitudesRecoleccion->estado) {
                        'completada' => 'success',
                        'en_proceso' => 'primary',
                        'cancelada' => 'danger',
                        default => 'warning'
                    };
                @endphp
                <span class="badge badge-{{ $badgeClass }} badge-lg">
                    {{ ucfirst(str_replace('_', ' ', $solicitudesRecoleccion->estado ?? 'pendiente')) }}
                </span>
            </dd>

            @if($solicitudesRecoleccion->observaciones)
                <dt class="col-sm-3">Observaciones:</dt>
                <dd class="col-sm-9">{{ $solicitudesRecoleccion->observaciones }}</dd>
            @endif

            @if($solicitudesRecoleccion->fecha_creacion)
                <dt class="col-sm-3">Fecha de Creación:</dt>
                <dd class="col-sm-9">
                    {{ \Carbon\Carbon::parse($solicitudesRecoleccion->fecha_creacion)->format('d/m/Y H:i') }}
                </dd>
            @endif
        </dl>
    </div>
</div>

{{-- Action Buttons --}}
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('inventario.solicitudes-recoleccions.index') }}" class="btn btn-secondary">
            Volver al Listado
        </a>
        <a href="{{ route('inventario.solicitudes-recoleccions.edit', $solicitudesRecoleccion->id_solicitud) }}"
            class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar Solicitud
        </a>
        <form action="{{ route('inventario.solicitudes-recoleccions.destroy', $solicitudesRecoleccion->id_solicitud) }}"
            method="POST" style="display: inline;"
            onsubmit="return confirm('¿Está seguro de eliminar esta solicitud?');">
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




