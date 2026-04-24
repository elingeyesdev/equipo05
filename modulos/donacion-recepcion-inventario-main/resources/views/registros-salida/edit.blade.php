@extends('adminlte::page')

@section('title', 'Editar Salida')

@section('content_header')
<h1>Editar Registro de Salida</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @includeif('partials.errors')
                <form method="POST" action="{{ route('inventario.registros-salida.update', $registrosSalida->id_salida) }}" role="form"
                    enctype="multipart/form-data">
                    {{ method_field('PUT') }}
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





