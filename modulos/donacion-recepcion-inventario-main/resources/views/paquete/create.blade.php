@extends('adminlte::page')

@section('title', 'Nuevo Paquete')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Nuevo Paquete</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.paquete.index') }}">
            Volver al Listado
        </a>
    </div>
</div>
@stop

@section('content')
@include('inventario::partials.flash-messages')
<form method="POST" action="{{ route('inventario.paquete.store') }}" role="form" enctype="multipart/form-data">
    @csrf
    @include('inventario::paquete.form')
</form>
@stop





