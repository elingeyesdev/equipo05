@extends('adminlte::page')

@section('title', 'Detalles del Espacio')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Detalles del Espacio</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.espacio.index') }}">
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
            <span class="info-box-icon bg-info"><i class="fas fa-th"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Código del Espacio</span>
                <span class="info-box-number">{{ $espacio->codigo_espacio }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-layer-group"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Estante</span>
                <span class="info-box-number" style="font-size: 1.2rem;">
                    @if($espacio->estante)
                        {{ $espacio->estante->codigo_estante }}
                    @else
                        Sin asignar
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-warehouse"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Almacén</span>
                <span class="info-box-number" style="font-size: 1rem;">
                    @if($espacio->estante && $espacio->estante->almacene)
                        {{ $espacio->estante->almacene->nombre }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Main Content Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Información Completa</h3>
        <div class="card-tools">
            <a href="{{ route('inventario.espacio.edit', $espacio->id_espacio) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Código del Espacio:</dt>
            <dd class="col-sm-9"><strong>{{ $espacio->codigo_espacio }}</strong></dd>

            <dt class="col-sm-3">Estante:</dt>
            <dd class="col-sm-9">
                @if($espacio->estante)
                    <span class="badge badge-info badge-lg">
                        {{ $espacio->estante->codigo_estante }}
                    </span>
                    @if($espacio->estante->descripcion)
                        <br><small class="text-muted">{{ $espacio->estante->descripcion }}</small>
                    @endif
                @else
                    <span class="badge badge-secondary">Sin estante asignado</span>
                @endif
            </dd>

            <dt class="col-sm-3">Almacén:</dt>
            <dd class="col-sm-9">
                @if($espacio->estante && $espacio->estante->almacene)
                    <span class="badge badge-primary badge-lg">
                        {{ $espacio->estante->almacene->nombre }}
                    </span>
                    @if($espacio->estante->almacene->direccion)
                        <br><small class="text-muted">{{ $espacio->estante->almacene->direccion }}</small>
                    @endif
                @else
                    <span class="badge badge-secondary">N/A</span>
                @endif
            </dd>
        </dl>
    </div>
</div>

{{-- Action Buttons --}}
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('inventario.espacio.index') }}" class="btn btn-secondary">
            Volver al Listado
        </a>
        <a href="{{ route('inventario.espacio.edit', $espacio->id_espacio) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar Espacio
        </a>
        <form action="{{ route('inventario.espacio.destroy', $espacio->id_espacio) }}" method="POST" style="display: inline;"
            onsubmit="return confirm('¿Está seguro de eliminar este espacio?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@stop




