@php
    $defaultLat = old('latitud', $latitud ?? -17.8146);
    $defaultLng = old('longitud', $longitud ?? -63.1561);
    $defaultZoom = old('latitud') ? 13 : 6;
@endphp

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapa-ubicacion-logistica { height: 380px; width: 100%; border: 1px solid #dee2e6; border-radius: 0.375rem; z-index: 1; }
    #search-suggestions-logistica { z-index: 1050; max-height: 220px; overflow-y: auto; width: 100%; display: none; }
</style>
@endpush

<div class="col-md-12 mb-3">
    <label class="font-weight-bold">Ubicación para la entrega</label>
    <p class="small text-muted mb-2">Busque una dirección o haga clic en el mapa. Las coordenadas se guardan con la solicitud.</p>
    <div class="position-relative mb-2">
        <div class="input-group col-md-8 p-0">
            <input type="text" id="search-ubicacion-logistica" class="form-control" placeholder="Buscar comunidad o dirección en Bolivia…">
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-info" id="btnBuscarUbicacionLogistica">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        <div id="search-suggestions-logistica" class="list-group position-absolute"></div>
    </div>
    <div id="mapa-ubicacion-logistica"
         data-lat="{{ $defaultLat }}"
         data-lng="{{ $defaultLng }}"
         data-zoom="{{ $defaultZoom }}"></div>
</div>

<input type="hidden" name="latitud" id="latitud-logistica" value="{{ old('latitud', $latitud ?? '') }}">
<input type="hidden" name="longitud" id="longitud-logistica" value="{{ old('longitud', $longitud ?? '') }}">

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    function initLogisticaMapPicker() {
        if (typeof L === 'undefined') {
            setTimeout(initLogisticaMapPicker, 100);
            return;
        }

        const mapContainer = document.getElementById('mapa-ubicacion-logistica');
        if (!mapContainer) return;

        const latInput = document.getElementById('latitud-logistica');
        const lngInput = document.getElementById('longitud-logistica');
        const direccionInput = document.querySelector('[name="direccion"]');
        const provinciaInput = document.querySelector('[name="provincia"]');
        const comunidadInput = document.querySelector('[name="comunidad"]');
        const searchInput = document.getElementById('search-ubicacion-logistica');
        const searchBtn = document.getElementById('btnBuscarUbicacionLogistica');
        const suggestions = document.getElementById('search-suggestions-logistica');

        let defaultLat = parseFloat(mapContainer.dataset.lat || '-17.8146');
        let defaultLng = parseFloat(mapContainer.dataset.lng || '-63.1561');
        let defaultZoom = parseInt(mapContainer.dataset.zoom || '6', 10);

        const map = L.map('mapa-ubicacion-logistica').setView([defaultLat, defaultLng], defaultZoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 19
        }).addTo(map);

        let marker = null;

        function setMarker(lat, lng) {
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    const pos = marker.getLatLng();
                    reverseGeocode(pos.lat, pos.lng);
                    latInput.value = pos.lat.toFixed(6);
                    lngInput.value = pos.lng.toFixed(6);
                });
            }
            map.setView([lat, lng], Math.max(map.getZoom(), 14));
        }

        function clearSuggestions() {
            if (!suggestions) return;
            suggestions.innerHTML = '';
            suggestions.style.display = 'none';
        }

        function reverseGeocode(lat, lng) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=16&addressdetails=1`)
                .then(r => r.json())
                .then(data => {
                    if (!data?.address) return;
                    const a = data.address;
                    let dir = a.road || '';
                    if (a.house_number) dir += (dir ? ' ' : '') + a.house_number;
                    if (a.neighbourhood) dir += (dir ? ', ' : '') + a.neighbourhood;
                    if (direccionInput && !direccionInput.value) direccionInput.value = dir || data.display_name || '';
                    if (provinciaInput && !provinciaInput.value) {
                        provinciaInput.value = a.city || a.town || a.municipality || a.state || provinciaInput.value;
                    }
                    if (comunidadInput && !comunidadInput.value) {
                        comunidadInput.value = a.suburb || a.village || a.neighbourhood || comunidadInput.value;
                    }
                })
                .catch(() => {});
        }

        function renderSuggestions(results) {
            if (!suggestions) return;
            suggestions.innerHTML = '';
            if (!results?.length) {
                suggestions.innerHTML = '<div class="list-group-item text-muted">Sin resultados en Bolivia.</div>';
                suggestions.style.display = 'block';
                return;
            }
            results.forEach(place => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action';
                btn.textContent = place.display_name;
                btn.addEventListener('click', () => {
                    setMarker(parseFloat(place.lat), parseFloat(place.lon));
                    reverseGeocode(parseFloat(place.lat), parseFloat(place.lon));
                    clearSuggestions();
                });
                suggestions.appendChild(btn);
            });
            suggestions.style.display = 'block';
        }

        function buscarUbicacion() {
            const query = searchInput?.value.trim();
            if (!query || query.length < 3) return;
            const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&addressdetails=1&limit=8&countrycodes=bo`;
            fetch(url).then(r => r.json()).then(renderSuggestions).catch(clearSuggestions);
        }

        map.on('click', function (e) {
            clearSuggestions();
            setMarker(e.latlng.lat, e.latlng.lng);
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });

        searchBtn?.addEventListener('click', (e) => { e.preventDefault(); buscarUbicacion(); });
        searchInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); buscarUbicacion(); }
        });

        if (latInput.value && lngInput.value) {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                setMarker(lat, lng);
                return;
            }
        }

        setTimeout(function () { map.invalidateSize(); }, 300);
    }

    document.addEventListener('DOMContentLoaded', initLogisticaMapPicker);
})();
</script>
@endpush
