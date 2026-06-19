@extends('layouts.app')

@section('content_header_title', $tituloSeccion)
@section('content_header_subtitle', $total . ' registros · ' . $nombreTabla)

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="card seg-list-card shadow-sm">
    <div class="card-header">
        <div class="seg-btn-toolbar w-100">
            <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Agregar
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        @if(count($columnas) === 0)
            <p class="text-muted mb-0 p-3">La tabla aún no está disponible en la base de datos.</p>
        @else
        <div class="table-responsive">
            <table class="table table-sm table-hover seg-data-table mb-0">
                <thead>
                    <tr>
                        @foreach($columnas as $col)
                            <th>{{ str_replace('_', ' ', ucfirst($col)) }}</th>
                        @endforeach
                        <th style="width:6rem;">Acc.</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $fila)
                    <tr>
                        @foreach($columnas as $col)
                            <td>{{ $fila->$col }}</td>
                        @endforeach
                        <td>
                            <span class="seg-row-actions">
                                <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => data_get($fila, $primaryKey)]) }}" class="btn btn-outline-warning btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('seguimiento.crud.destroy', ['seccion' => $seccion, 'id' => data_get($fila, $primaryKey)]) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ count($columnas) + 1 }}" class="text-muted text-center py-4">No hay datos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
