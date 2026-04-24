@extends('adminlte::page')

@section('title', 'Detalles del Punto de Recolección')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Detalles del Punto de Recolección</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.puntos-recoleccion.index') }}">
            Volver al Listado
        </a>
    </div>
</div>
@stop

@section('content')
{{-- Info Boxes Row --}}
<div class="row">
    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Nombre</span>
                <span class="info-box-number" style="font-size: 1rem;">{{ $puntosRecoleccion->nombre }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-phone"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Contacto</span>
                <span class="info-box-number"
                    style="font-size: 1rem;">{{ $puntosRecoleccion->contacto ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-map-marker-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Coordenadas</span>
                <span class="info-box-number" style="font-size: 0.8rem;">
                    @if($puntosRecoleccion->latitud)
                        {{ $puntosRecoleccion->latitud }}, {{ $puntosRecoleccion->longitud }}
                    @else
                        No registradas
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Main Information Card --}}
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
                <div class="card-tools">
                    <a href="{{ route('inventario.puntos-recoleccion.edit', $puntosRecoleccion->id_punto) }}"
                        class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nombre:</dt>
                    <dd class="col-sm-8"><strong>{{ $puntosRecoleccion->nombre }}</strong></dd>

                    <dt class="col-sm-4">Dirección:</dt>
                    <dd class="col-sm-8">
                        {{ $puntosRecoleccion->direccion }}
                    </dd>

                    <dt class="col-sm-4">Contacto:</dt>
                    <dd class="col-sm-8">
                        @if($puntosRecoleccion->contacto)
                            {{ $puntosRecoleccion->contacto }}
                        @else
                            <span class="text-muted">No registrado</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Map Card --}}
    <div class="col-md-6">
        <div class="card card-secondary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map"></i> Ubicación</h3>
            </div>
            <div class="card-body p-0">
                <div id="map" style="height: 300px; width: 100%;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Action Buttons --}}
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('inventario.puntos-recoleccion.index') }}" class="btn btn-secondary">
            Volver al Listado
        </a>
        <a href="{{ route('inventario.puntos-recoleccion.edit', $puntosRecoleccion->id_punto) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar Punto
        </a>
        <form action="{{ route('inventario.puntos-recoleccion.destroy', $puntosRecoleccion->id_punto) }}" method="POST"
            style="display: inline;" onsubmit="return confirm('¿Está seguro de eliminar este punto de recolección?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var lat = {{ $puntosRecoleccion->latitud ?? -16.5000 }};
        var lng = {{ $puntosRecoleccion->longitud ?? -68.1500 }};
        var hasLocation = {{ $puntosRecoleccion->latitud ? 'true' : 'false' }};

        var map = L.map('map').setView([lat, lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        if (hasLocation) {
            L.marker([lat, lng]).addTo(map)
                .bindPopup('<b>{{ $puntosRecoleccion->nombre }}</b><br>{{ $puntosRecoleccion->direccion }}')
                .openPopup();
        }
    });
</script>
@stop




