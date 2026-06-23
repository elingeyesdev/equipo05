@extends('layouts.app')

@section('content_header_title', $registro ? 'Editar ' . $tituloSeccion : 'Crear ' . $tituloSeccion)
@section('content_header_subtitle', 'Formulario de registro logístico')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

@include('fusion.modulos.partials.modulo-crud-form', [
    'routePrefix' => 'logistica',
    'moduloKey' => 'logistica',
])
@endsection
