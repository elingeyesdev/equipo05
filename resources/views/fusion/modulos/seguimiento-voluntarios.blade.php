@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 text-center">
            <h2 class="text-primary font-weight-bold mb-1">Dashboard - Seguimiento de Voluntarios</h2>
            <p class="text-muted mb-0">Replica funcional del flujo principal de GEVOPI en el proyecto base</p>
        </div>
    </div>

    <div class="row">
        @foreach($resumen as $item)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="small-box bg-info shadow-sm">
                    <div class="inner">
                        <h3>{{ $item['total'] }}</h3>
                        <p>{{ $item['label'] }}</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-users mr-2"></i>Accesos rápidos de voluntarios</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('seguimiento.voluntarios') }}" class="btn btn-outline-primary btn-block mb-2">Voluntarios</a>
                    <a href="{{ route('seguimiento.voluntarios-inactivos') }}" class="btn btn-outline-secondary btn-block mb-2">Voluntarios Inactivos</a>
                    <a href="{{ route('seguimiento.evaluacion') }}" class="btn btn-outline-danger btn-block mb-2">Evaluación</a>
                    <a href="{{ route('seguimiento.capacitaciones') }}" class="btn btn-outline-success btn-block">Capacitaciones</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header bg-warning">
                    <h3 class="card-title mb-0 text-dark"><i class="fas fa-link mr-2"></i>Accesos administrativos</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('seguimiento.necesidades') }}" class="btn btn-outline-warning btn-block mb-2">Necesidades</a>
                    <a href="{{ route('seguimiento.ayudas-solicitadas') }}" class="btn btn-outline-info btn-block mb-2">Ayudas Solicitadas</a>
                    <a href="{{ route('seguimiento.administradores') }}" class="btn btn-outline-dark btn-block mb-2">Administradores</a>
                    <a href="{{ route('seguimiento.universidades') }}" class="btn btn-outline-primary btn-block">Universidades</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-danger shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title mb-0"><i class="fas fa-history mr-2"></i>Últimos voluntarios registrados</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead>
                            <tr>
                                @if($voluntariosRecientes->count() > 0)
                                    @foreach(array_slice(array_keys((array) $voluntariosRecientes->first()), 0, 6) as $columna)
                                        <th>{{ $columna }}</th>
                                    @endforeach
                                @else
                                    <th>Sin datos</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($voluntariosRecientes as $fila)
                                <tr>
                                    @foreach(array_slice(array_keys((array) $fila), 0, 6) as $columna)
                                        <td>{{ $fila->$columna }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted">No hay registros disponibles en la tabla de voluntarios.</td>
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
