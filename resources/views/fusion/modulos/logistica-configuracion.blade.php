@extends('layouts.app')

@section('content_header_title', 'Configuración logística')
@section('content_header_subtitle', 'Catálogos y parámetros del módulo')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

@php
    $grupos = [
        'Operación y casos' => [
            ['route' => 'logistica.estado', 'icon' => 'fa-flag', 'label' => 'Estados de paquete', 'desc' => 'Pendiente, en tránsito, entregado…'],
            ['route' => 'logistica.tipo-emergencia', 'icon' => 'fa-exclamation-triangle', 'label' => 'Tipos de emergencia', 'desc' => 'Opciones del formulario de solicitud'],
            ['route' => 'logistica.solicitante', 'icon' => 'fa-user-friends', 'label' => 'Solicitantes', 'desc' => 'Personas que piden ayuda (avanzado)'],
            ['route' => 'logistica.destino', 'icon' => 'fa-map-marker-alt', 'label' => 'Destinos', 'desc' => 'Comunidades y provincias (avanzado)'],
            ['route' => 'logistica.ubicacion', 'icon' => 'fa-map-pin', 'label' => 'Ubicaciones', 'desc' => 'Puntos de ruta y almacenes'],
        ],
        'Flota' => [
            ['route' => 'logistica.marca', 'icon' => 'fa-flag-checkered', 'label' => 'Marcas', 'desc' => 'Catálogo de marcas de vehículo'],
            ['route' => 'logistica.tipo-vehiculo', 'icon' => 'fa-th-large', 'label' => 'Tipos de vehículo', 'desc' => 'Camión, pickup, furgón…'],
            ['route' => 'logistica.tipo-licencia', 'icon' => 'fa-id-card', 'label' => 'Licencias', 'desc' => 'Tipos de licencia de conducir'],
        ],
        'Sistema' => [
            ['route' => 'logistica.usuario', 'icon' => 'fa-user', 'label' => 'Voluntarios', 'desc' => 'Usuarios del módulo logístico'],
            ['route' => 'logistica.rol', 'icon' => 'fa-user-shield', 'label' => 'Roles', 'desc' => 'Permisos internos del módulo'],
            ['route' => 'logistica.reporte', 'icon' => 'fa-book', 'label' => 'Reportes', 'desc' => 'Registros administrativos'],
        ],
    ];
@endphp

@foreach($grupos as $titulo => $items)
<div class="card logistica-list-card shadow-sm mb-3">
    <div class="card-header"><strong>{{ $titulo }}</strong></div>
    <div class="card-body">
        <div class="row">
            @foreach($items as $item)
            <div class="col-md-4 col-sm-6 mb-3">
                <a href="{{ route($item['route']) }}" class="d-block p-3 border rounded h-100 text-decoration-none text-dark logistica-config-link">
                    <i class="fas {{ $item['icon'] }} text-primary mr-2"></i>
                    <strong>{{ $item['label'] }}</strong>
                    <p class="mb-0 small text-muted mt-1">{{ $item['desc'] }}</p>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

<div class="alert alert-info mb-0 small">
    <strong>Nota:</strong> Solicitudes, paquetes, seguimiento y galería de entregas se gestionan desde las pantallas operativas.
    La galería pública sigue en
    <a href="{{ route('publico.logistica.galeria') }}" target="_blank" rel="noopener">acceso ciudadano</a>.
</div>
@endsection

@push('css')
<style>
    .logistica-config-link:hover { background: #f8f9fa; }
</style>
@endpush
