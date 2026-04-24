@extends('adminlte::page')

@section('title', 'Editar Punto de Recolección')

@section('content_header')
<h1>Editar Punto de Recolección</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @includeif('partials.errors')

            <form method="POST" action="{{ route('inventario.puntos-recoleccion.update', $puntosRecoleccion->id_punto) }}" role="form"
                enctype="multipart/form-data">
                {{ method_field('PATCH') }}
                @csrf

                @include('puntos-recoleccion.form')

            </form>
        </div>
    </div>
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Coordenadas iniciales (desde BD o por defecto La Paz)
        var lat = {{ $puntosRecoleccion->latitud ?? -16.5000 }};
        var lng = {{ $puntosRecoleccion->longitud ?? -68.1500 }};
        var hasLocation = {{ $puntosRecoleccion->latitud ? 'true' : 'false' }};

        var map = L.map('map').setView([lat, lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker;

        if (hasLocation) {
            marker = L.marker([lat, lng]).addTo(map);
        }

        function onMapClick(e) {
            var lat = e.latlng.lat.toFixed(8);
            var lng = e.latlng.lng.toFixed(8);

            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }
        }

        map.on('click', onMapClick);
    });
</script>
@stop



