<div class="row">
    <div class="col-md-12">
        <x-adminlte-alert theme="info" icon="fas fa-info-circle">
            <strong>Sistema de Predicción de Propagación de Incendios</strong>
            <p class="mb-0">Seleccione un foco de incendio en el mapa (registrados en BD o NASA FIRMS) y configure los parámetros ambientales.</p>
        </x-adminlte-alert>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-map-marked-alt"></i> Seleccionar Foco de Incendio</h5>
                <span class="badge badge-light" id="map-fire-count">Cargando...</span>
            </div>
            <div class="card-body p-0 position-relative">
                <div id="prediction-map-loading" class="prediction-map-overlay">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mb-0 mt-2">Cargando mapa y focos...</p>
                </div>
                <div id="prediction-map" style="height: 450px; width: 100%; min-height: 450px;"></div>
            </div>
            <div class="card-footer text-muted small">
                <span class="mr-3"><i class="fas fa-database text-primary"></i> Azul = focos en BD</span>
                <span class="mr-3"><i class="fas fa-satellite text-danger"></i> Naranja/rojo = NASA FIRMS</span>
                <span><i class="fas fa-mouse-pointer"></i> Clic en marcador o en el mapa para elegir punto</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card" id="selected-fire-card" style="display: none;">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-check-circle"></i> Foco Seleccionado</h5>
            </div>
            <div class="card-body">
                <div class="mb-2"><strong>Origen:</strong> <span id="selected-source">-</span></div>
                <div class="mb-2"><strong>Ubicación:</strong> <span id="selected-location">-</span></div>
                <div class="mb-2"><strong>Coordenadas:</strong> <span id="selected-coords">-</span></div>
                <div class="mb-2"><strong>Potencia (FRP):</strong> <span id="selected-frp">-</span></div>
                <div class="mb-2"><strong>Intensidad:</strong> <span id="selected-intensity">-</span></div>
                <div class="mb-0"><strong>Fecha:</strong> <span id="selected-date">-</span></div>
            </div>
        </div>

        <div class="card" id="no-fire-card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-hand-pointer"></i> Sin Selección</h5>
            </div>
            <div class="card-body text-center text-muted">
                <i class="fas fa-mouse-pointer fa-3x mb-3"></i>
                <p>Haga clic en un foco en el mapa o elija un punto manualmente</p>
            </div>
        </div>

        <input type="hidden" name="foco_incendio_id" id="foco_incendio_id" value="{{ old('foco_incendio_id', request('foco_incendio_id')) }}">
        <input type="hidden" name="fire_lat" id="fire_lat" value="{{ old('fire_lat', request('fire_lat')) }}">
        <input type="hidden" name="fire_lng" id="fire_lng" value="{{ old('fire_lng', request('fire_lng')) }}">
        <input type="hidden" name="fire_intensity" id="fire_intensity" value="{{ old('fire_intensity', request('fire_intensity', 3)) }}">
        <input type="hidden" name="fire_frp" id="fire_frp" value="{{ old('fire_frp', request('fire_frp')) }}">
    </div>

    <div class="col-md-6 mt-3">
        <x-adminlte-input name="prediction_hours" label="Horas de Predicción" type="number"
            value="{{ old('prediction_hours', 24) }}"
            min="1" max="72" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text"><i class="fas fa-clock text-primary"></i></div>
            </x-slot>
            <x-slot name="bottomSlot">Entre 1 y 72 horas <span class="text-danger">*</span></x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-6 mt-3">
        <div class="form-group mb-3">
            <label for="terrain_type" class="form-label">
                <i class="fas fa-mountain text-success"></i> Tipo de Terreno <span class="text-danger">*</span>
            </label>
            <select name="terrain_type" id="terrain_type" class="form-control @error('terrain_type') is-invalid @enderror" required>
                <option value="bosque_denso" {{ old('terrain_type') == 'bosque_denso' ? 'selected' : '' }}>Bosque denso (alta propagación)</option>
                <option value="bosque_normal" {{ old('terrain_type') == 'bosque_normal' ? 'selected' : '' }}>Bosque normal</option>
                <option value="pastizal" {{ old('terrain_type', 'pastizal') == 'pastizal' ? 'selected' : '' }}>Pastizal (propagación media)</option>
                <option value="matorral" {{ old('terrain_type') == 'matorral' ? 'selected' : '' }}>Matorral</option>
                <option value="rocoso" {{ old('terrain_type') == 'rocoso' ? 'selected' : '' }}>Rocoso (baja propagación)</option>
            </select>
            {!! $errors->first('terrain_type', '<div class="invalid-feedback d-block"><strong>:message</strong></div>') !!}
        </div>
    </div>

    <div class="col-md-12">
        <h5 class="mt-3 mb-3">
            <i class="fas fa-cloud-sun text-warning"></i> Condiciones Ambientales
            <button type="button" id="loadWeatherBtn" class="btn btn-sm btn-info float-right">
                <i class="fas fa-cloud-download-alt"></i> Cargar Clima Actual (Open-Meteo)
            </button>
        </h5>
    </div>

    <div class="col-md-3">
        <x-adminlte-input name="temperature" label="Temperatura (°C)" type="number" value="{{ old('temperature', 25) }}" min="0" max="60" step="0.1" enable-old-support>
            <x-slot name="prependSlot"><div class="input-group-text"><i class="fas fa-thermometer-half text-danger"></i></div></x-slot>
        </x-adminlte-input>
    </div>
    <div class="col-md-3">
        <x-adminlte-input name="humidity" label="Humedad (%)" type="number" value="{{ old('humidity', 50) }}" min="0" max="100" step="0.1" enable-old-support>
            <x-slot name="prependSlot"><div class="input-group-text"><i class="fas fa-tint text-info"></i></div></x-slot>
        </x-adminlte-input>
    </div>
    <div class="col-md-3">
        <x-adminlte-input name="wind_speed" label="Velocidad del Viento (km/h)" type="number" value="{{ old('wind_speed', 10) }}" min="0" max="200" step="0.1" enable-old-support>
            <x-slot name="prependSlot"><div class="input-group-text"><i class="fas fa-wind text-primary"></i></div></x-slot>
        </x-adminlte-input>
    </div>
    <div class="col-md-3">
        <x-adminlte-input name="wind_direction" label="Dirección del Viento (°)" type="number" value="{{ old('wind_direction', 0) }}" min="0" max="360" enable-old-support>
            <x-slot name="prependSlot"><div class="input-group-text"><i class="fas fa-compass text-secondary"></i></div></x-slot>
            <x-slot name="bottomSlot">0° Norte, 90° Este, 180° Sur, 270° Oeste</x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12 mt-3">
        <x-adminlte-button type="submit" label="Generar Predicción" theme="primary" icon="fas fa-chart-line" class="btn-lg" id="submitBtn"/>
        <a href="{{ route('incendios.predictions.index') }}" class="btn btn-secondary btn-lg"><i class="fas fa-times"></i> Cancelar</a>
    </div>
</div>

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
<style>
    #prediction-map { border-radius: 0; z-index: 1; }
    .prediction-map-overlay {
        position: absolute; inset: 0; z-index: 1000;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        background: rgba(248,249,250,0.92); color: #6c757d;
    }
    .prediction-map-overlay.hidden { display: none; }
    .fire-marker-selected { filter: drop-shadow(0 0 10px #22c55e); z-index: 1000 !important; }
    .custom-fire-marker { background: transparent !important; border: none !important; }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { box-shadow: 0 0 0 15px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
(function () {
    const dbFires = @json($focosMap ?? []);
    const firmsUrl = @json(url('api/incendios/fires')) + '?cluster=true&radius=20&days=2';

    let map = null;
    let selectedMarker = null;
    let manualMarker = null;
    const allMarkers = [];

    function hideMapLoading() {
        const el = document.getElementById('prediction-map-loading');
        if (el) el.classList.add('hidden');
    }

    function createFireIcon(fire, isSelected) {
        const fromDb = fire.source === 'database';
        let color = fromDb ? '#2563eb' : '#ff6b35';
        if (!fromDb) {
            if (fire.confidence === 'h') color = '#dc2626';
            else if (fire.confidence === 'l') color = '#fb923c';
        }
        if (isSelected) color = '#22c55e';
        const size = 28;
        const html = `<div style="background:${color};width:${size}px;height:${size}px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 4px 10px rgba(0,0,0,.3);display:flex;align-items:center;justify-content:center;${isSelected?'animation:pulse-green 1.5s infinite':''}"><i class="fas fa-fire" style="color:#fff;font-size:14px;transform:rotate(45deg);"></i></div>`;
        return L.divIcon({ html, className: 'custom-fire-marker' + (isSelected ? ' fire-marker-selected' : ''), iconSize: [size, 38], iconAnchor: [size/2, 38], popupAnchor: [0, -33] });
    }

    function updateFireCount() {
        const badge = document.getElementById('map-fire-count');
        if (badge) badge.textContent = allMarkers.length + ' foco(s) en mapa';
    }

    async function loadWeatherForCoords(lat, lng) {
        const btn = document.getElementById('loadWeatherBtn');
        const temperatureInput = document.querySelector('input[name="temperature"]');
        const humidityInput = document.querySelector('input[name="humidity"]');
        const windSpeedInput = document.querySelector('input[name="wind_speed"]');
        const windDirectionInput = document.querySelector('input[name="wind_direction"]');
        if (!btn) return;
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
        try {
            const res = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lng}&current=temperature_2m,relative_humidity_2m,wind_speed_10m,wind_direction_10m&timezone=America/La_Paz`);
            if (!res.ok) throw new Error('clima');
            const data = await res.json();
            temperatureInput.value = Math.round(data.current.temperature_2m * 10) / 10;
            humidityInput.value = Math.round(data.current.relative_humidity_2m * 10) / 10;
            windSpeedInput.value = Math.round(data.current.wind_speed_10m * 10) / 10;
            windDirectionInput.value = Math.round(data.current.wind_direction_10m);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Clima cargado', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
            }
        } catch (e) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Clima no disponible', text: 'Ingresa los valores manualmente.', toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = original;
        }
    }

    async function selectFire(fire, marker) {
        if (selectedMarker && selectedMarker !== marker) {
            selectedMarker.setIcon(createFireIcon(selectedMarker.fireData, false));
        }
        if (manualMarker) {
            map.removeLayer(manualMarker);
            manualMarker = null;
        }
        selectedMarker = marker;
        marker.setIcon(createFireIcon(fire, true));

        const frp = parseFloat(fire.frp) || 5;
        const intensity = fire.intensidad ? Math.min(10, Math.max(1, Math.round(fire.intensidad))) : Math.min(10, Math.max(1, Math.round(frp / 50)));

        document.getElementById('foco_incendio_id').value = fire.id || '';
        document.getElementById('fire_lat').value = fire.lat;
        document.getElementById('fire_lng').value = fire.lng;
        document.getElementById('fire_intensity').value = intensity;
        document.getElementById('fire_frp').value = frp;

        const sourceLabels = { database: 'Base de datos', firms: 'NASA FIRMS', manual: 'Punto manual' };
        document.getElementById('selected-source').textContent = sourceLabels[fire.source] || 'NASA FIRMS';
        document.getElementById('selected-location').textContent = fire.ubicacion || '-';
        document.getElementById('selected-coords').textContent = `${Number(fire.lat).toFixed(5)}, ${Number(fire.lng).toFixed(5)}`;
        document.getElementById('selected-frp').textContent = `${frp.toFixed(1)} MW`;
        document.getElementById('selected-intensity').textContent = `${intensity}/10`;
        document.getElementById('selected-date').textContent = fire.date || 'N/A';

        document.getElementById('selected-fire-card').style.display = 'block';
        document.getElementById('no-fire-card').style.display = 'none';
        document.getElementById('submitBtn').disabled = false;

        if (marker.closePopup) marker.closePopup();
        await loadWeatherForCoords(fire.lat, fire.lng);
    }

    function addFireMarker(fire) {
        const marker = L.marker([fire.lat, fire.lng], { icon: createFireIcon(fire, false) }).addTo(map);
        marker.fireData = fire;
        const idx = allMarkers.length;
        allMarkers.push(marker);

        const label = fire.source === 'database' ? 'Foco registrado' : 'Foco FIRMS';
        marker.bindPopup(`
            <div style="min-width:200px">
                <strong>${label}</strong><br>
                ${fire.ubicacion ? fire.ubicacion + '<br>' : ''}
                <small>${Number(fire.lat).toFixed(4)}, ${Number(fire.lng).toFixed(4)}</small><br>
                <button type="button" class="btn btn-success btn-sm btn-block mt-2" onclick="window.selectFireMarker(${idx})">Seleccionar</button>
            </div>
        `);
        marker.on('click', () => selectFire(fire, marker));
        return marker;
    }

    function selectManualPoint(lat, lng) {
        const fire = { lat, lng, frp: 25, intensidad: 3, date: 'Manual', source: 'manual', ubicacion: 'Punto manual en mapa' };
        if (manualMarker) map.removeLayer(manualMarker);
        manualMarker = L.marker([lat, lng], { icon: createFireIcon(fire, true) }).addTo(map);
        manualMarker.fireData = fire;
        selectedMarker = manualMarker;
        selectFire(fire, manualMarker);
    }

    window.selectFireMarker = function (index) {
        const marker = allMarkers[index];
        if (marker) selectFire(marker.fireData, marker);
    };

    function fitMapToMarkers() {
        if (!map || allMarkers.length === 0) return;
        const group = L.featureGroup(allMarkers);
        map.fitBounds(group.getBounds().pad(0.15));
    }

    function trySelectFromUrl() {
        const urlLat = parseFloat(document.getElementById('fire_lat').value);
        const urlLng = parseFloat(document.getElementById('fire_lng').value);
        if (!urlLat || !urlLng) return;
        let closest = null;
        let dist = Infinity;
        allMarkers.forEach(m => {
            const d = Math.hypot(m.fireData.lat - urlLat, m.fireData.lng - urlLng);
            if (d < dist) { dist = d; closest = m; }
        });
        if (closest && dist < 0.05) {
            selectFire(closest.fireData, closest);
        } else {
            selectManualPoint(urlLat, urlLng);
        }
        map.setView([urlLat, urlLng], 12);
    }

    async function loadFirmsFires() {
        try {
            const res = await fetch(firmsUrl);
            const result = await res.json();
            const fires = (result && result.data) ? result.data : [];
            fires.forEach(f => addFireMarker({ ...f, source: 'firms' }));
            if (result && result.demo && typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'info', title: 'Datos demo FIRMS', text: result.message || 'Configure FIRMS_API_KEY para datos reales.', toast: true, position: 'top-end', timer: 4000, showConfirmButton: false });
            }
        } catch (e) {
            console.error('FIRMS:', e);
        }
    }

    function initPredictionMap() {
        if (typeof L === 'undefined') {
            setTimeout(initPredictionMap, 150);
            return;
        }
        const container = document.getElementById('prediction-map');
        if (!container || map) return;

        map = L.map(container, { preferCanvas: true }).setView([-17.8857, -60.7556], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap',
            maxZoom: 18,
        }).addTo(map);

        map.on('click', e => selectManualPoint(e.latlng.lat, e.latlng.lng));

        dbFires.forEach(f => addFireMarker(f));
        loadFirmsFires().finally(() => {
            hideMapLoading();
            updateFireCount();
            fitMapToMarkers();
            trySelectFromUrl();
            setTimeout(() => map.invalidateSize(), 200);
            setTimeout(() => map.invalidateSize(), 800);
        });

        document.getElementById('loadWeatherBtn')?.addEventListener('click', async () => {
            const lat = document.getElementById('fire_lat').value;
            const lng = document.getElementById('fire_lng').value;
            if (!lat || !lng) {
                Swal?.fire({ icon: 'warning', title: 'Selecciona un foco en el mapa primero' });
                return;
            }
            await loadWeatherForCoords(parseFloat(lat), parseFloat(lng));
        });

        document.querySelector('form')?.addEventListener('submit', e => {
            if (!document.getElementById('fire_lat').value || !document.getElementById('fire_lng').value) {
                e.preventDefault();
                Swal?.fire({ icon: 'error', title: 'Foco requerido', text: 'Selecciona un punto en el mapa.' });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPredictionMap);
    } else {
        initPredictionMap();
    }
})();
</script>
@endpush

