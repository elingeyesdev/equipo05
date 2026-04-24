@extends('layouts.app')

@section('title', 'Fusion Fase 1')

@section('header')
    <h1 class="m-0 text-dark">
        <i class="fas fa-project-diagram mr-2"></i>
        Fusion Fase 1
    </h1>
    <p class="text-muted mb-0">
        Integracion inicial de transparencia + recepcion e inventario sin alterar los proyectos originales.
    </p>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-7">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cubes mr-1"></i> Modulos en integracion</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>Modulo</th>
                            <th>Estado</th>
                            <th>Detalle</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($modulos as $modulo)
                            <tr>
                                <td>{{ $modulo['nombre'] }}</td>
                                <td><span class="badge badge-info">{{ $modulo['estado'] }}</span></td>
                                <td>{{ $modulo['descripcion'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list-ol mr-1"></i> Roadmap tecnico inmediato</h3>
                </div>
                <div class="card-body">
                    <ol class="pl-3">
                        @foreach($fases as $fase)
                            <li class="mb-2">{{ $fase }}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
