@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">{{ $tituloSeccion }} - Logistica Transportacion Donaciones</h3>
                        <a href="{{ route('logistica.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Agregar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Tabla:</strong> {{ $nombreTabla }} | <strong>Registros:</strong> {{ $total }}</p>
                    @if(count($columnas) === 0)
                        <p class="text-muted mb-0">La tabla aun no esta disponible en la base de datos logistica.</p>
                    @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead>
                                <tr>
                                    @foreach($columnas as $col)
                                        <th>{{ $col }}</th>
                                    @endforeach
                                    <th style="width: 140px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filas as $fila)
                                <tr>
                                    @foreach($columnas as $col)
                                        <td>{{ $fila->$col }}</td>
                                    @endforeach
                                    <td>
                                        <div class="d-flex" style="gap:.35rem;">
                                            <a href="{{ route('logistica.crud.edit', ['seccion' => $seccion, 'id' => data_get($fila, $primaryKey)]) }}" class="btn btn-warning btn-xs">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('logistica.crud.destroy', ['seccion' => $seccion, 'id' => data_get($fila, $primaryKey)]) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-xs" type="submit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ count($columnas) + 1 }}" class="text-muted">No hay datos para mostrar.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
