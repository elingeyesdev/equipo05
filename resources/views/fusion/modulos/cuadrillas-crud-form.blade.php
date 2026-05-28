@extends('layouts.app')

@section('content')
    @include('fusion.modulos.partials.modulo-crud-form', [
        'routePrefix' => 'cuadrillas',
        'moduloKey' => 'cuadrillas',
    ])
@endsection
