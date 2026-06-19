@extends('layouts.app')

@section('content_header_title', $registro ? 'Editar ' . $tituloSeccion : 'Crear ' . $tituloSeccion)
@section('content_header_subtitle', 'Formulario de registro')

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

@include('fusion.modulos.partials.modulo-crud-form', [
    'routePrefix' => 'seguimiento',
    'moduloKey' => 'seguimiento',
])
@endsection
