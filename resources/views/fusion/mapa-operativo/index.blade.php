@extends('layouts.app')

@section('title', 'Mapa territorial integrado')

@section('content_header_title', 'Comando central territorial')
@section('content_header_subtitle', 'Vista unificada de los 7 módulos operativos')

@section('content')
@php
    $layerMeta = [
        'incendios_firms' => ['label' => 'Detecciones FIRMS', 'group' => 'Incendios', 'color' => '#dc2626'],
        'incendios_registrados' => ['label' => 'Focos registrados', 'group' => 'Incendios', 'color' => '#2563eb'],
        'incendios_biomasas' => ['label' => 'Biomasas aprobadas', 'group' => 'Incendios', 'color' => '#16a34a'],
        'rescate_hallazgos' => ['label' => 'Hallazgos de fauna', 'group' => 'Rescate', 'color' => '#15803d'],
        'rescate_liberaciones' => ['label' => 'Liberaciones', 'group' => 'Rescate', 'color' => '#059669'],
        'rescate_centros' => ['label' => 'Centros de rescate', 'group' => 'Rescate', 'color' => '#0891b2'],
        'logistica_entregas' => ['label' => 'Entregas logísticas', 'group' => 'Logística', 'color' => '#4f46e5'],
        'cuadrillas_equipos' => ['label' => 'Equipos de cuadrilla', 'group' => 'Cuadrillas', 'color' => '#7c3aed'],
        'cuadrillas_reportes' => ['label' => 'Reportes de campo', 'group' => 'Cuadrillas', 'color' => '#ea580c'],
        'voluntarios_ayudas' => ['label' => 'Solicitudes de ayuda', 'group' => 'Voluntarios', 'color' => '#d97706'],
        'inventario_sitios' => ['label' => 'Almacenes y puntos', 'group' => 'Inventario', 'color' => '#0d9488'],
    ];
    $moduleLabels = [
        'incendios' => 'Incendios',
        'rescate' => 'Rescate silvestre',
        'logistica' => 'Logística',
        'cuadrillas' => 'Cuadrillas',
        'seguimiento' => 'Voluntarios',
        'inventario' => 'Inventario',
    ];
    $groups = [];
    foreach ($layerMeta as $key => $meta) {
        $groups[$meta['group']][$key] = $meta;
    }
@endphp

<div class="territorial-total-banner d-flex flex-wrap align-items-center justify-content-between">
    <div>
        <div class="small text-white-50 mb-1"><i class="fas fa-globe-americas mr-1"></i> Puntos georreferenciados activos</div>
        <div class="total-value" id="territorial-total-count">{{ number_format($totalMarkers) }}</div>
    </div>
    <div class="text-right">
        <button type="button" class="btn btn-light btn-sm" id="territorial-refresh">
            <i class="fas fa-sync-alt mr-1"></i> Actualizar capas
        </button>
        <div class="small text-white-50 mt-2" id="territorial-generated-at">Cargando…</div>
    </div>
</div>

<div class="territorial-kpi-grid" id="territorial-kpi-grid">
    @foreach($layerKeys as $key)
        @php $meta = $layerMeta[$key] ?? ['label' => $key, 'color' => '#64748b']; @endphp
        <div class="territorial-kpi" data-kpi-layer="{{ $key }}">
            <div class="kpi-value" style="color: {{ $meta['color'] }}">{{ number_format($summary[$key] ?? 0) }}</div>
            <div class="kpi-label">{{ $meta['label'] }}</div>
        </div>
    @endforeach
</div>

<div class="territorial-map-layout">
    <aside class="territorial-layers-panel">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong class="small text-uppercase text-muted">Capas del mapa</strong>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary btn-xs" id="territorial-layers-all">Todas</button>
                <button type="button" class="btn btn-outline-secondary btn-xs" id="territorial-layers-none">Ninguna</button>
            </div>
        </div>

        @foreach($groups as $groupName => $layers)
            <h6>{{ $groupName }}</h6>
            @foreach($layers as $key => $meta)
                <label class="territorial-layer-toggle mb-0">
                    <input type="checkbox" class="territorial-layer-cb" value="{{ $key }}" checked>
                    <span class="territorial-layer-dot" style="background: {{ $meta['color'] }}"></span>
                    <span>{{ $meta['label'] }}</span>
                    <span class="ml-auto badge badge-light border layer-count" data-count-layer="{{ $key }}">{{ $summary[$key] ?? 0 }}</span>
                </label>
            @endforeach
        @endforeach

        <hr class="my-3">
        <p class="small text-muted mb-0">
            <i class="fas fa-info-circle mr-1"></i>
            Solo visible para el rol <strong>Administrador</strong>. Los marcadores enlazan al módulo de origen.
        </p>
    </aside>

    <div class="territorial-map-card">
        <div class="territorial-map-toolbar">
            <span class="badge badge-primary"><i class="fas fa-layer-group mr-1"></i> Mapa integrado</span>
            <span class="badge badge-light border" id="territorial-visible-count">0 visibles</span>
            <span class="badge badge-light border ml-auto"><i class="fas fa-warehouse text-success mr-1"></i> Origen logística</span>
        </div>
        <div class="territorial-map-wrap">
            <div id="territorial-loading" class="territorial-loading">
                <span><i class="fas fa-spinner fa-spin mr-2"></i>Cargando capas operativas…</span>
            </div>
            <div id="territorial-map"></div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const apiUrl = @json(route('territorial.api.capas'));
    const layerMeta = @json($layerMeta);
    const moduleLabels = @json($moduleLabels);
    const iconMap = {
        satellite: 'fa-satellite',
        fire: 'fa-fire',
        tree: 'fa-tree',
        paw: 'fa-paw',
        leaf: 'fa-leaf',
        hospital: 'fa-hospital',
        truck: 'fa-truck',
        users: 'fa-users',
        'exclamation-triangle': 'fa-exclamation-triangle',
        'hands-helping': 'fa-hands-helping',
        warehouse: 'fa-warehouse',
        'map-pin': 'fa-map-pin',
    };

    const map = L.map('territorial-map', { zoomControl: true }).setView([-17.8857, -60.7556], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const layerGroups = {};
    let polygonLayers = {};
    let origenMarker = null;
    let allBounds = L.latLngBounds();

    Object.keys(layerMeta).forEach(function (key) {
        layerGroups[key] = L.markerClusterGroup({
            maxClusterRadius: 50,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
        });
        map.addLayer(layerGroups[key]);
    });

    function markerIcon(point) {
        const color = point.color || '#64748b';
        const fa = iconMap[point.icon] || 'fa-map-marker-alt';
        return L.divIcon({
            className: 'bg-transparent',
            html: `<div class="territorial-marker-icon" style="background:${color}"><i class="fas ${fa}"></i></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
        });
    }

    function buildPopup(point) {
        const mod = moduleLabels[point.module] || point.module;
        let html = `<span class="territorial-popup-module">${mod}</span><br><strong>${point.label || ''}</strong>`;
        const meta = point.meta || {};
        Object.keys(meta).forEach(function (k) {
            if (meta[k] != null && meta[k] !== '' && k !== 'polygon') {
                html += `<br><small class="text-muted">${k}: ${meta[k]}</small>`;
            }
        });
        if (point.url) {
            html += `<br><a href="${point.url}" class="btn btn-xs btn-outline-primary mt-2 btn-sm">Ver en módulo</a>`;
        }
        return html;
    }

    function setLoading(show) {
        document.getElementById('territorial-loading').style.display = show ? 'flex' : 'none';
    }

    function updateCounts(summary) {
        let total = 0;
        Object.keys(summary || {}).forEach(function (key) {
            total += summary[key] || 0;
            const kpi = document.querySelector(`[data-kpi-layer="${key}"] .kpi-value`);
            if (kpi) kpi.textContent = (summary[key] || 0).toLocaleString('es-BO');
            document.querySelectorAll(`[data-count-layer="${key}"]`).forEach(function (el) {
                el.textContent = summary[key] || 0;
            });
        });
        document.getElementById('territorial-total-count').textContent = total.toLocaleString('es-BO');
    }

    function visibleLayerKeys() {
        return Array.from(document.querySelectorAll('.territorial-layer-cb:checked')).map(function (cb) {
            return cb.value;
        });
    }

    function syncLayerVisibility() {
        const active = new Set(visibleLayerKeys());
        Object.keys(layerGroups).forEach(function (key) {
            if (active.has(key)) {
                if (!map.hasLayer(layerGroups[key])) map.addLayer(layerGroups[key]);
            } else if (map.hasLayer(layerGroups[key])) {
                map.removeLayer(layerGroups[key]);
            }
        });
        Object.keys(polygonLayers).forEach(function (key) {
            polygonLayers[key].forEach(function (poly) {
                if (active.has(key)) {
                    if (!map.hasLayer(poly)) map.addLayer(poly);
                } else if (map.hasLayer(poly)) {
                    map.removeLayer(poly);
                }
            });
        });
        recountVisible();
    }

    function recountVisible() {
        const active = new Set(visibleLayerKeys());
        let n = 0;
        active.forEach(function (key) {
            const g = layerGroups[key];
            if (g) n += g.getLayers().length;
        });
        document.getElementById('territorial-visible-count').textContent = n + ' visibles';
    }

    function clearLayers() {
        Object.keys(layerGroups).forEach(function (key) {
            layerGroups[key].clearLayers();
        });
        Object.keys(polygonLayers).forEach(function (key) {
            polygonLayers[key].forEach(function (poly) { map.removeLayer(poly); });
        });
        polygonLayers = {};
        if (origenMarker) {
            map.removeLayer(origenMarker);
            origenMarker = null;
        }
        allBounds = L.latLngBounds();
    }

    function renderPayload(data) {
        clearLayers();
        polygonLayers = {};

        if (data.origen_logistica && data.origen_logistica.lat) {
            const o = data.origen_logistica;
            origenMarker = L.marker([o.lat, o.lng], {
                icon: L.divIcon({
                    className: 'bg-transparent',
                    html: '<div class="territorial-marker-icon" style="background:#059669"><i class="fas fa-warehouse"></i></div>',
                    iconSize: [28, 28],
                    iconAnchor: [14, 14],
                })
            }).addTo(map).bindPopup('<strong>Almacén central logística</strong>');
            allBounds.extend([o.lat, o.lng]);
        }

        const layers = data.layers || {};
        Object.keys(layers).forEach(function (key) {
            (layers[key] || []).forEach(function (point) {
                if (!point.lat || !point.lng) return;
                const latlng = [point.lat, point.lng];
                const marker = L.marker(latlng, { icon: markerIcon(point) });
                marker.bindPopup(buildPopup(point));
                layerGroups[key].addLayer(marker);
                allBounds.extend(latlng);

                const poly = point.meta && point.meta.polygon;
                if (Array.isArray(poly) && poly.length >= 3) {
                    const latlngs = poly.map(function (p) {
                        return Array.isArray(p) ? [p[0], p[1]] : [p.lat, p.lng];
                    });
                    const color = point.color || '#16a34a';
                    const polygon = L.polygon(latlngs, {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.15,
                        weight: 2,
                    }).bindPopup(buildPopup(point));
                    if (!polygonLayers[key]) polygonLayers[key] = [];
                    polygonLayers[key].push(polygon);
                    latlngs.forEach(function (ll) { allBounds.extend(ll); });
                }
            });
        });

        updateCounts(data.summary || {});
        if (data.generated_at) {
            const d = new Date(data.generated_at);
            document.getElementById('territorial-generated-at').textContent =
                'Actualizado: ' + d.toLocaleString('es-BO');
        }

        syncLayerVisibility();

        if (allBounds.isValid()) {
            map.fitBounds(allBounds.pad(0.08));
        }
        setTimeout(function () { map.invalidateSize(); }, 250);
    }

    async function loadLayers() {
        setLoading(true);
        try {
            const keys = visibleLayerKeys();
            const url = keys.length === layerMeta.length
                ? apiUrl
                : apiUrl + '?layers=' + encodeURIComponent(keys.join(','));
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            renderPayload(await res.json());
        } catch (e) {
            document.getElementById('territorial-generated-at').textContent = 'Error al cargar capas';
            console.error(e);
        } finally {
            setLoading(false);
        }
    }

    document.querySelectorAll('.territorial-layer-cb').forEach(function (cb) {
        cb.addEventListener('change', syncLayerVisibility);
    });

    document.getElementById('territorial-layers-all').addEventListener('click', function () {
        document.querySelectorAll('.territorial-layer-cb').forEach(function (cb) { cb.checked = true; });
        syncLayerVisibility();
    });

    document.getElementById('territorial-layers-none').addEventListener('click', function () {
        document.querySelectorAll('.territorial-layer-cb').forEach(function (cb) { cb.checked = false; });
        syncLayerVisibility();
    });

    document.getElementById('territorial-refresh').addEventListener('click', loadLayers);

    loadLayers();
});
</script>
@endpush
