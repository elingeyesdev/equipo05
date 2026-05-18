@extends('adminlte::page')

@section('title', 'Nueva Campaña')

@section('content_header')
<h1>Nueva Campaña</h1>
@stop

@section('content')
@include('inventario::partials.flash-messages')
    <div class="row">
        <div class="col-md-12">
            @includeif('partials.errors')

            <form method="POST" action="{{ route('inventario.campana.store') }}" role="form" enctype="multipart/form-data">
                @csrf

                @include('inventario::campana.form')

            </form>
        </div>
    </div>
@endsection





