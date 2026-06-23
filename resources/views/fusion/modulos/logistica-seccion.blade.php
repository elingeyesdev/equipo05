@extends('layouts.app')

@section('content_header_title', $tituloSeccion)
@section('content_header_subtitle', $total . ' registros · ' . $nombreTabla)

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

@php
    $seccionesConfig = ['solicitante', 'destino', 'ubicacion', 'marca', 'tipo-vehiculo', 'usuario', 'rol', 'estado', 'tipo-emergencia', 'tipo-licencia', 'reporte'];
@endphp
@if(in_array($seccion, $seccionesConfig, true))
<div class="mb-2">
    <a href="{{ route('logistica.configuracion') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Volver a configuración
    </a>
</div>
@endif

<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            <a href="{{ route('logistica.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Agregar
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        @if(count($columnas) === 0)
            <p class="text-muted mb-0 p-3">La tabla aún no está disponible en la base de datos logística.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-hover logistica-tabla-operativa mb-0">
                <thead>
                    <tr>
                        @foreach($columnas as $col)
                            <th>{{ str_replace('_', ' ', ucfirst($col)) }}</th>
                        @endforeach
                        <th style="width: 6rem;">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $fila)
                    <tr>
                        @foreach($columnas as $col)
                            <td>{{ $fila->$col }}</td>
                        @endforeach
                        <td>
                            <span class="logistica-row-actions">
                                <a href="{{ route('logistica.crud.edit', ['seccion' => $seccion, 'id' => data_get($fila, $primaryKey)]) }}" class="btn btn-outline-warning btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('logistica.crud.destroy', ['seccion' => $seccion, 'id' => data_get($fila, $primaryKey)]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este registro?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" type="submit" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($columnas) + 1 }}" class="text-muted text-center py-4">No hay datos para mostrar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
