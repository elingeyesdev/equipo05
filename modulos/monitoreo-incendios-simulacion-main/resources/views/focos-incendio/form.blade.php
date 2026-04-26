<div class="row">
    <div class="col-md-6">
        <x-adminlte-input name="fecha" label="Fecha" type="datetime-local"
            value="{{ old('fecha', optional($focosIncendio->fecha)->format('Y-m-d\TH:i')) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-calendar text-danger"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>
    <div class="col-md-6">
        <x-adminlte-input name="ubicacion" label="Ubicación" 
            placeholder="Nombre del lugar"
            value="{{ old('ubicacion', $focosIncendio->ubicacion) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-map-marker-alt text-danger"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-6">
        <x-adminlte-input name="intensidad" label="Intensidad" type="number" step="0.01"
            value="{{ old('intensidad', $focosIncendio->intensidad) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-fire text-danger"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="coordenadas">Coordenadas [lat, lng]</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-location-arrow text-danger"></i>
                    </span>
                </div>
                <input type="text" name="coordenadas" id="coordenadas" 
                    class="form-control @error('coordenadas') is-invalid @enderror" 
                    value="{{ old('coordenadas', $focosIncendio->coordenadas ? json_encode($focosIncendio->coordenadas) : '') }}" 
                    placeholder='[-17.8, -61.5]' readonly>
                @error('coordenadas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <small class="form-text text-muted">Haga clic en el mapa para seleccionar las coordenadas</small>
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group mb-3">
            <label>Seleccionar ubicación en el mapa</label>
            <div id="map" style="height: 400px; border-radius: 8px; border: 1px solid #ddd;"></div>
            <small class="form-text text-muted">Haga clic en el mapa para marcar la ubicación del foco de incendio</small>
        </div>
    </div>

    <div class="col-12 mt-3">
        <x-adminlte-button type="submit" label="Guardar" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('focos-incendios.index') }}" class="btn btn-danger "><i class="fas fa-arrow-left"></i> Cancelar</a>
    </div>
</div>

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container {
        cursor: crosshair;
    }
</style>
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    (function() {
        let focoMap;
        let focoMarker;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Coordenadas por defecto (San José de Chiquitos)
            const defaultLat = -17.8;
            const defaultLng = -61.5;
            
            // Obtener coordenadas existentes si están disponibles
            let initialCoords = [defaultLat, defaultLng];
            const coordsInput = document.getElementById('coordenadas');
            if (coordsInput.value) {
                try {
                    const coords = JSON.parse(coordsInput.value);
                    if (Array.isArray(coords) && coords.length === 2) {
                        initialCoords = coords;
                    }
                } catch (e) {
                    console.log('No se pudieron parsear las coordenadas existentes');
                }
            }
            
            // Inicializar mapa
            focoMap = L.map('map').setView(initialCoords, 12);
            
            // Agregar capa de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(focoMap);
            
            // Si hay coordenadas existentes, agregar marcador
            if (coordsInput.value) {
                focoMarker = L.marker(initialCoords, {
                    draggable: true
                }).addTo(focoMap);
                
                focoMarker.on('dragend', function(e) {
                    const position = focoMarker.getLatLng();
                    updateCoordinates(position.lat, position.lng);
                });
            }
            
            // Agregar evento de clic en el mapa
            focoMap.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                // Eliminar marcador anterior si existe
                if (focoMarker) {
                    focoMap.removeLayer(focoMarker);
                }
                
                // Agregar nuevo marcador
                focoMarker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(focoMap);
                
                // Actualizar coordenadas
                updateCoordinates(lat, lng);
                
                // Evento para cuando se arrastra el marcador
                focoMarker.on('dragend', function(e) {
                    const position = focoMarker.getLatLng();
                    updateCoordinates(position.lat, position.lng);
                });
            });
        });
        
        function updateCoordinates(lat, lng) {
            const coordsInput = document.getElementById('coordenadas');
            const roundedLat = Math.round(lat * 1000000) / 1000000;
            const roundedLng = Math.round(lng * 1000000) / 1000000;
            coordsInput.value = JSON.stringify([roundedLat, roundedLng]);
        }
    })();
</script>
@endpush
