<div class="row padding-1 p-1">
    <div class="col-md-12">

        {{-- Nombre Field --}}
        <div class="form-group mb-3">
            <label for="nombre" class="form-label">
                <i class="fas text-info"></i> {{ __('Nombre del Almacén') }}
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-warehouse"></i></span>
                </div>
                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                    value="{{ old('nombre', $almacene?->nombre) }}" id="nombre" placeholder="Ej: Almacén Central"
                    required>
                @error('nombre')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i> Ingrese un nombre descriptivo para identificar el almacén
            </small>
        </div>

        {{-- Direccion Field --}}
        <div class="form-group mb-3">
            <label for="direccion" class="form-label">
                <i class="fas alt text-success"></i> {{ __('Dirección') }}
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                </div>
                <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                    value="{{ old('direccion', $almacene?->direccion) }}" id="direccion"
                    placeholder="Ej: Av. Cristo Redentor #123" required>
                @error('direccion')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
            </div>
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i> Ingrese la dirección completa del almacén
            </small>
        </div>

        {{-- Map Section --}}
        <div class="card card-primary card-outline mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas text-primary"></i> {{ __('Ubicación en el Mapa') }}
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Haz clic en el mapa para seleccionar la ubicación del almacén
                    <span class="badge badge-info ml-2">
                        También puedes arrastrar el marcador
                    </span>
                </p>

                <div id="map"
                    style="height: 450px; width: 100%; border: 2px solid #007bff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,123,255,0.1);">
                </div>

                <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $almacene?->latitud) }}">
                <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $almacene?->longitud) }}">

                <div class="mt-3 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Coordenadas seleccionadas:</strong>
                        </div>
                        <div class="col-md-6">
                            <span id="coords-display" class="badge badge-primary badge-lg">
                                @if(old('latitud', $almacene?->latitud) && old('longitud', $almacene?->longitud))
                                    Lat: {{ old('latitud', $almacene?->latitud) }}, Lng:
                                    {{ old('longitud', $almacene?->longitud) }}
                                @else
                                    No seleccionadas
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-12 mt-3">
        <button type="submit" class="btn btn-primary btn-lg">
            {{ __('Guardar Almacén') }}
        </button>
        <a href="{{ route('inventario.almacene.index') }}" class="btn btn-secondary btn-lg">
            {{ __('Cancelar') }}
        </a>
    </div>
</div>

@push('css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .leaflet-popup-content {
            text-align: center;
            font-family: inherit;
        }

        .custom-div-icon {
            background: transparent;
            border: none;
        }

        #coords-display {
            font-size: 0.95rem;
            padding: 8px 12px;
        }

        .input-group-text {
            background-color: #f4f6f9;
        }
    </style>
@endpush

@push('js')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Coordinates for Santa Cruz de la Sierra, Bolivia
        const santaCruzCoords = [-17.8145819, -63.1560853];

        // Get saved coordinates or use Santa Cruz as default
        let savedLat = document.getElementById('latitud').value;
        let savedLng = document.getElementById('longitud').value;
        let hasSavedCoords = savedLat && savedLng && savedLat !== '' && savedLng !== '';
        let initialCoords = hasSavedCoords
            ? [parseFloat(savedLat), parseFloat(savedLng)]
            : santaCruzCoords;

        // Initialize the map
        const map = L.map('map').setView(initialCoords, hasSavedCoords ? 15 : 13);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Custom warehouse icon
        const warehouseIcon = L.divIcon({
            html: '<i class="fas fa-warehouse fa-2x" style="color: #007bff;"></i>',
            className: 'custom-div-icon',
            iconSize: [30, 42],
            iconAnchor: [15, 42],
            popupAnchor: [0, -42]
        });

        // Create a marker
        let marker = null;
        if (hasSavedCoords) {
            marker = L.marker(initialCoords, {
                draggable: true,
                icon: warehouseIcon
            }).addTo(map);

            marker.bindPopup('<div style="text-align: center;"><i class="fas fa-warehouse"></i><br><strong>Ubicación del Almacén</strong></div>').openPopup();

            // Add circle around marker
            L.circle(initialCoords, {
                color: '#007bff',
                fillColor: '#007bff',
                fillOpacity: 0.1,
                radius: 100
            }).addTo(map);

            // Update coordinates when marker is dragged
            marker.on('dragend', function (e) {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });
        }

        // Add click event to map
        map.on('click', function (e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;

            // Update or create marker
            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, {
                    draggable: true,
                    icon: warehouseIcon
                }).addTo(map);

                marker.bindPopup('<div style="text-align: center;"><i class="fas fa-warehouse"></i><br><strong>Ubicación del Almacén</strong></div>').openPopup();

                // Add circle
                L.circle(e.latlng, {
                    color: '#007bff',
                    fillColor: '#007bff',
                    fillOpacity: 0.1,
                    radius: 100
                }).addTo(map);

                // Update coordinates when marker is dragged
                marker.on('dragend', function (e) {
                    const position = marker.getLatLng();
                    updateCoordinates(position.lat, position.lng);
                });
            }

            // Update hidden inputs and display
            updateCoordinates(lat, lng);
        });

        // Function to update coordinate fields
        function updateCoordinates(lat, lng) {
            document.getElementById('latitud').value = lat.toFixed(7);
            document.getElementById('longitud').value = lng.toFixed(7);
            document.getElementById('coords-display').innerHTML =
                '<i class="fas fa-check-circle"></i> Lat: ' + lat.toFixed(7) + ', Lng: ' + lng.toFixed(7);
            document.getElementById('coords-display').className = 'badge badge-success badge-lg';
        }
    </script>
@endpush




