@extends('layouts.app')

@section('content')
    @include('fusion.modulos.partials.modulo-crud-form', [
        'routePrefix' => 'logistica',
        'moduloKey' => 'logistica',
    ])
@endsection
