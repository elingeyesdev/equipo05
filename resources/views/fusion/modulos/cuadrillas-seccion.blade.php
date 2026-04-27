@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h2 class="m-0">{{ $tituloSeccion }}</h2>
        </div>
        <div class="col-sm-6 text-sm-right">
            <a href="{{ route('cuadrillas.dashboard') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Volver al dashboard
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-danger">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title mb-0">{{ $tituloSeccion }} - Cuadrillas Incendios Kardex Cursos</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Tabla:</strong> {{ $nombreTabla }} | <strong>Registros:</strong> {{ $total }}</p>

                    @if(count($columnas) === 0)
                        <p class="text-muted mb-0">La tabla aun no esta disponible en la base de datos cuadrillas.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead>
                                    <tr>
                                        @foreach($columnas as $col)
                                            <th>{{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($filas as $fila)
                                        <tr>
                                            @foreach($columnas as $col)
                                                <td>{{ $fila->$col }}</td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($columnas) }}" class="text-muted">No hay datos para mostrar.</td>
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
