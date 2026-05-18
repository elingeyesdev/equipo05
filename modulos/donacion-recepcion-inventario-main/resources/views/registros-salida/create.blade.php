@extends('adminlte::page')

@section('title', 'Nueva Salida')

@section('content_header')
<h1>Registrar Nueva Salida</h1>
@stop

@section('content')
@include('inventario::partials.flash-messages')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @includeif('partials.errors')
                <form method="POST" action="{{ route('inventario.registros-salida.store') }}" role="form"
                    enctype="multipart/form-data">
                    @csrf
                    @include('inventario::registros-salida.form')
                </form>
            </div>
        </div>
    </div>
@endsection

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop





