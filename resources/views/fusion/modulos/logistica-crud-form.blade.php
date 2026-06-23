@extends('layouts.app')

@section('content_header_title', ($registro ? 'Editar' : 'Nuevo') . ' · ' . $tituloSeccion)
@section('content_header_subtitle', 'Formulario logístico')

@section('content')
@include('fusion.modulos.partials.logistica-flash')

@include('fusion.modulos.partials.logistica-crud-form-body', [
    'seccion' => $seccion,
    'tituloSeccion' => $tituloSeccion,
    'primaryKey' => $primaryKey,
    'columns' => $columns,
    'options' => $options,
    'registro' => $registro,
    'tieneFotoEntrega' => $tieneFotoEntrega ?? false,
])
@endsection
