@extends('adminlte::page')

@section('title', 'Nueva Solicitud de Recolección')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Nueva Solicitud de Recolección</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.solicitudes-recoleccions.index') }}">
            Volver al Listado
        </a>
    </div>
</div>
@stop

@section('content')
<form method="POST" action="{{ route('inventario.solicitudes-recoleccions.store') }}" role="form" enctype="multipart/form-data">
    @csrf
    @include('solicitudes-recoleccion.form')
</form>
@stop



