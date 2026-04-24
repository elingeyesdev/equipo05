@extends('adminlte::page')

@section('title', 'Detalles del Almacén')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1><i class="fas fa-warehouse"></i> Detalles del Almacén</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.almacene.index') }}">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>
@stop

@section('content')
{{-- Info Boxes Row --}}
<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-warehouse"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Nombre</span>
                <span class="info-box-number">{{ $almacene->nombre }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-5 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-map-marker-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Dirección</span>
                <span class="info-box-number" style="font-size: 1rem;">{{ $almacene->direccion }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon {{ $almacene->latitud && $almacene->longitud ? 'bg-primary' : 'bg-secondary' }}">
                <i class="fas fa-globe"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Ubicación GPS</span>
                <span class="info-box-number" style="font-size: 0.9rem;">
                    @if($almacene->latitud && $almacene->longitud)
                        {{ number_format($almacene->latitud, 6) }}, {{ number_format($almacene->longitud, 6) }}
                    @else
                        Sin registro
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Main Content Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información Completa</h3>
        <div class="card-tools">
            <a href="{{ route('inventario.almacene.edit', $almacene->id_almacen) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-4">Nombre:</dt>
                    <dd class="col-sm-8"><strong>{{ $almacene->nombre }}</strong></dd>

                    <dt class="col-sm-4">Dirección:</dt>
                    <dd class="col-sm-8">{{ $almacene->direccion }}</dd>

                    @if($almacene->latitud && $almacene->longitud)
                        <dt class="col-sm-4">Latitud:</dt>
                        <dd class="col-sm-8">{{ $almacene->latitud }}</dd>

                        <dt class="col-sm-4">Longitud:</dt>
                        <dd class="col-sm-8">{{ $almacene->longitud }}</dd>
                    @endif
                </dl>
            </div>

            <div class="col-md-6">
                @if($almacene->latitud && $almacene->longitud)
                    <div class="callout callout-info">
                        <h5><i class="fas fa-map"></i> Vista Rápida</h5>
                        <p>Este almacén tiene su ubicación GPS registrada. Puedes verlo en el mapa completo abajo.</p>
                        <a href="https://www.google.com/maps?q={{ $almacene->latitud }},{{ $almacene->longitud }}"
                            target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-external-link-alt"></i> Abrir en Google Maps
                        </a>
                    </div>
                @else
                    <div class="callout callout-warning">
                        <h5><i class="fas fa-exclamation-triangle"></i> Sin Ubicación GPS</h5>
                        <p>Este almacén no tiene registrada su ubicación GPS.</p>
                        <a href="{{ route('inventario.almacene.edit', $almacene->id_almacen) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Agregar Ubicación
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($almacene->latitud && $almacene->longitud)
    {{-- Map Card --}}
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Ubicación en el Mapa</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="maximize">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="map" style="height: 500px; width: 100%;"></div>
        </div>
        <div class="card-footer">
            <div class="text-muted">
                <i class="fas fa-info-circle"></i>
                Coordenadas: {{ $almacene->latitud }}, {{ $almacene->longitud }}
            </div>
        </div>
    </div>
@endif

{{-- Estantes Card --}}
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-inbox"></i> Estantes del Almacén</h3>
        <div class="card-tools">
            <a href="{{ route('inventario.estante.create', ['id_almacen' => $almacene->id_almacen]) }}"
                class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Nuevo Estante
            </a>
        </div>
    </div>
    <div class="card-body">
        @if($estantes->count() > 0)
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th width="60px">#</th>
                        <th><i class="fas fa-barcode"></i> Código</th>
                        <th><i class="fas fa-align-left"></i> Descripción</th>
                        <th width="180px" class="text-center"><i class="fas fa-cogs"></i> Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($estantes as $index => $estante)
                        <tr>
                            <td class="text-center"><strong>{{ $index + 1 }}</strong></td>
                            <td><strong>{{ $estante->codigo_estante }}</strong></td>
                            <td>{{ $estante->descripcion ?? 'Sin descripción' }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a class="btn btn-info btn-sm" href="{{ route('inventario.estante.show', $estante->id_estante) }}"
                                        title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-warning btn-sm" href="{{ route('inventario.estante.edit', $estante->id_estante) }}"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('inventario.estante.destroy', $estante->id_estante) }}" method="POST"
                                        style="display: inline;"
                                        onsubmit="return confirm('¿Está seguro de eliminar este estante?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="callout callout-warning">
                <h5><i class="fas fa-info-circle"></i> Sin Estantes</h5>
                <p>Este almacén no tiene estantes registrados todavía. Haz clic en "Nuevo Estante" para crear uno.</p>
            </div>
        @endif
    </div>
</div>

{{-- Action Buttons --}}
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('inventario.almacene.index') }}" class="btn btn-secondary">
            <i class="fas fa-list"></i> Volver al Listado
        </a>
        <a href="{{ route('inventario.almacene.edit', $almacene->id_almacen) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar Almacén
        </a>
        <form action="{{ route('inventario.almacene.destroy', $almacene->id_almacen) }}" method="POST" style="display: inline;"
            onsubmit="return confirm('¿Está seguro de eliminar este almacén?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@stop

@if($almacene->latitud && $almacene->longitud)
@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<style>
    .info-box-number {
        font-size: 1.2rem;
    }

    .leaflet-popup-content {
        text-align: center;
    }
</style>
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    const almacenCoords = [{{ $almacene->latitud }}, {{ $almacene->longitud }}];

    // Initialize the map
    const map = L.map('map').setView(almacenCoords, 15);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Custom icon
    const warehouseIcon = L.divIcon({
        html: '<i class="fas fa-warehouse fa-2x text-primary"></i>',
        className: 'custom-div-icon',
        iconSize: [30, 42],
        iconAnchor: [15, 42],
        popupAnchor: [0, -42]
    });

    // Add marker with custom icon
    const marker = L.marker(almacenCoords, { icon: warehouseIcon }).addTo(map);

    // Popup content
    const popupContent = `
            <div style="text-align: center;">
                <h6><i class="fas fa-warehouse"></i> <strong>{{ $almacene->nombre }}</strong></h6>
                <p class="mb-1"><i class="fas fa-map-marker-alt"></i> {{ $almacene->direccion }}</p>
                <small class="text-muted">Lat: {{ $almacene->latitud }}, Lng: {{ $almacene->longitud }}</small>
            </div>
        `;

    marker.bindPopup(popupContent).openPopup();

    // Add circle around the warehouse
    L.circle(almacenCoords, {
        color: '#007bff',
        fillColor: '#007bff',
        fillOpacity: 0.1,
        radius: 100
    }).addTo(map);
</script>
@stop
@endif




