@extends('adminlte::page')

@section('template_title')
    Create Donacione
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create Donación') }}</span>
                    </div>
                    <form method="POST" action="{{ route('inventario.donaciones.guardar_manual') }}" role="form"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            @include('inventario::donaciones.form')
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                {{ __('Guardar Donación') }}</button>
                            <a href="{{ route('inventario.donaciones.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i>
                                {{ __('Cancelar') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @include('inventario::donaciones.modals')
@endsection

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@stop

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Initialize map when collection point modal is shown
    var pointMapInstance = null;
    var pointMarker = null;

    $('#createCollectionPointModal').on('shown.bs.modal', function () {
        if (!pointMapInstance) {
            // Coordenadas por defecto (Santa Cruz de la Sierra, Bolivia)
            var defaultLat = -17.8146;
            var defaultLng = -63.1561;

            pointMapInstance = L.map('pointMap').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(pointMapInstance);

            function onMapClick(e) {
                var lat = e.latlng.lat.toFixed(8);
                var lng = e.latlng.lng.toFixed(8);

                document.getElementById('point_latitud').value = lat;
                document.getElementById('point_longitud').value = lng;

                if (pointMarker) {
                    pointMarker.setLatLng(e.latlng);
                } else {
                    pointMarker = L.marker(e.latlng).addTo(pointMapInstance);
                }
            }

            pointMapInstance.on('click', onMapClick);
        }

        // Fix map rendering issue when modal is shown
        setTimeout(function () {
            pointMapInstance.invalidateSize();
        }, 100);
    });

    // Reset map when modal is closed
    $('#createCollectionPointModal').on('hidden.bs.modal', function () {
        if (pointMarker) {
            pointMapInstance.removeLayer(pointMarker);
            pointMarker = null;
        }
        document.getElementById('point_latitud').value = '';
        document.getElementById('point_longitud').value = '';
    });
</script>
@stop





