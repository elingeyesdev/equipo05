@extends('layouts.app')

@section('content')
    @include('fusion.modulos.partials.modulo-crud-form', [
        'routePrefix' => 'seguimiento',
        'moduloKey' => 'seguimiento',
    ])
@endsection
