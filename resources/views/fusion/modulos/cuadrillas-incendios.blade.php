@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Cuadrillas Incendios Kardex Cursos - Alas Chiquitanas</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted">
                        Dashboard integrado del modulo de cuadrillas. Usa el menu lateral para navegar por operaciones, informacion y catalogos.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($resumen as $item)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $item['total'] }}</h3>
                        <p>{{ $item['label'] }}</p>
                    </div>
                    <div class="icon"><i class="fas fa-fire"></i></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Reportes recientes</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                @if($reportesRecientes->count() > 0)
                                    @foreach(array_slice(array_keys((array) $reportesRecientes->first()), 0, 6) as $columna)
                                        <th>{{ $columna }}</th>
                                    @endforeach
                                @else
                                    <th>Sin datos</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportesRecientes as $fila)
                                <tr>
                                    @foreach(array_slice(array_keys((array) $fila), 0, 6) as $columna)
                                        <td>{{ $fila->$columna }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted">No hay registros disponibles en la tabla de reportes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
