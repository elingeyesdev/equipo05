@extends('adminlte::page')

@section('title', 'Editar Campaña')

@section('content_header')
<h1>Editar Campaña</h1>
@stop

@section('content')
@include('inventario::partials.flash-messages')
    <div class="row">
        <div class="col-md-12">
            @includeif('partials.errors')

            <form method="POST" action="{{ route('inventario.campana.update', $campana->id_campana) }}" role="form"
                enctype="multipart/form-data">
                {{ method_field('PUT') }}
                @csrf

                @include('inventario::campana.form')

            </form>
        </div>
    </div>
@endsection





