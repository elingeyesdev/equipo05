@extends('adminlte::page')

@section('title', 'Nuevo Punto de Recolección')

@section('content_header')
<h1>Nuevo Punto de Recolección</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            @includeif('partials.errors')

            <form method="POST" action="{{ route('inventario.puntos-recoleccion.store') }}" role="form" enctype="multipart/form-data">
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
        // Coordenadas por defecto (La Paz, Bolivia)
        var defaultLat = -16.5000;
        var defaultLng = -68.1500;

        var map = L.map('map').setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker;

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



