@extends('adminlte::page')

@section('title', 'Editar Solicitud de Recolección')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Editar Solicitud de Recolección</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.solicitudes-recoleccions.index') }}">
            Volver al Listado
        </a>
    </div>
</div>
@stop

@section('content')
<form method="POST" action="{{ route('inventario.solicitudes-recoleccions.update', $solicitudesRecoleccion->id_solicitud) }}"
    role="form" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    @include('inventario::solicitudes-recoleccion.form')
</form>
@stop





