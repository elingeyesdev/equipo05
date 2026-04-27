@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h2 class="m-0">Dashboard - Alas Chiquitanas</h2>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Cuadrillas</li>
            </ol>
        </div>
    </div>

    <div class="row">
        @foreach($resumen as $item)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="small-box bg-danger shadow-sm">
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
        <div class="col-md-8">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-fire mr-1"></i>Centro de Operaciones</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('cuadrillas.focos-calor') }}" class="btn btn-outline-danger btn-block mb-2">Mapa en Tiempo Real</a>
                            <a href="{{ route('cuadrillas.reportes') }}" class="btn btn-outline-warning btn-block mb-2">Reportes Rápidos</a>
                            <a href="{{ route('cuadrillas.reportes-incendio') }}" class="btn btn-outline-danger btn-block">Reportes de Incendio</a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('cuadrillas.equipos') }}" class="btn btn-outline-info btn-block mb-2">Equipos</a>
                            <a href="{{ route('cuadrillas.recursos') }}" class="btn btn-outline-success btn-block mb-2">Recursos</a>
                            <a href="{{ route('cuadrillas.kardex') }}" class="btn btn-outline-primary btn-block">Mi Kardex</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-newspaper mr-1"></i>Información</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('cuadrillas.noticias') }}" class="btn btn-outline-info btn-block mb-2">Noticias</a>
                    <a href="{{ route('cuadrillas.cursos') }}" class="btn btn-outline-primary btn-block mb-2">Cursos</a>
                    <a href="{{ route('cuadrillas.inscritos') }}" class="btn btn-outline-secondary btn-block mb-2">Inscritos</a>
                    <a href="{{ route('cuadrillas.comunarios') }}" class="btn btn-outline-dark btn-block">Comunarios de Apoyo</a>
                </div>
            </div>
        </div>
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
