@extends('layouts.app')

@section('content_header_title', 'Ayudas solicitadas')
@section('content_header_subtitle', 'Reportes de apoyo en terreno con mapa operativo')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
@endpush

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="seg-filtros-panel">
    <div class="row align-items-end">
        <div class="col-md-4 mb-2 mb-md-0">
            <label for="buscarNombre" class="small font-weight-bold">Búsqueda</label>
            <input type="text" id="buscarNombre" class="form-control form-control-sm" placeholder="Nombre o detalle">
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <label for="prioridadFiltro" class="small font-weight-bold">Prioridad</label>
            <select id="prioridadFiltro" class="form-control form-control-sm">
                <option value="">Todas</option>
                <option value="alto">Alto</option>
                <option value="medio">Medio</option>
                <option value="bajo">Bajo</option>
            </select>
        </div>
        <div class="col-md-3 mb-2 mb-md-0">
            <label for="estadoFiltro" class="small font-weight-bold">Estado</label>
            <select id="estadoFiltro" class="form-control form-control-sm">
                <option value="">Todos</option>
                <option value="sin responder">Sin responder</option>
                <option value="en progreso">En progreso</option>
                <option value="respondido">Respondido</option>
                <option value="resuelto">Resuelto</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-outline-secondary btn-sm btn-block" id="btnLimpiar">
                <i class="fas fa-times"></i> Limpiar
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5 mb-3">
        <div class="card seg-list-card shadow-sm h-100">
            <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-list mr-1"></i> Lista de ayudas</h3></div>
            <div class="card-body p-2" id="listado" style="max-height: 480px; overflow-y: auto;"></div>
        </div>
    </div>
    <div class="col-lg-7 mb-3">
        <div class="card seg-list-card seg-accent-danger shadow-sm h-100">
            <div class="card-header"><h3 class="card-title mb-0"><i class="fas fa-map-marker-alt mr-1 text-danger"></i> Mapa</h3></div>
            <div class="card-body p-0 position-relative">
                <div id="map" class="seg-map-container"></div>
                <div class="seg-map-legend">
                    <strong class="d-block small text-uppercase text-muted mb-1">Prioridad</strong>
                    <div class="small"><span class="d-inline-block rounded-circle mr-1" style="width:10px;height:10px;background:#dc3545;"></span> Alta</div>
                    <div class="small"><span class="d-inline-block rounded-circle mr-1" style="width:10px;height:10px;background:#d97706;"></span> Media</div>
                    <div class="small"><span class="d-inline-block rounded-circle mr-1" style="width:10px;height:10px;background:#059669;"></span> Baja</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const datos = {!! $solicitudesJson !!};
    const buscarNombre = document.getElementById('buscarNombre');
    const prioridadFiltro = document.getElementById('prioridadFiltro');
    const estadoFiltro = document.getElementById('estadoFiltro');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const listadoDiv = document.getElementById('listado');

    const map = L.map('map').setView([-17.806776, -63.15749], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markersLayer = L.layerGroup().addTo(map);
    let marcadoresPorId = {};

    function colorPorPrioridad(prio) {
        prio = (prio || '').toLowerCase();
        if (prio === 'alto') return '#dc3545';
        if (prio === 'medio') return '#d97706';
        if (prio === 'bajo') return '#059669';
        return '#6c757d';
    }

    function crearIcono(prioridad) {
        const color = colorPorPrioridad(prioridad);
        return L.divIcon({
            html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${color}" width="28" height="28"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="3" fill="#fff"/></svg>`,
            className: '',
            iconSize: [28, 28],
            iconAnchor: [14, 28],
        });
    }

    function renderMapa(lista) {
        markersLayer.clearLayers();
        marcadoresPorId = {};
        const bounds = [];
        lista.forEach(item => {
            if (item.latitud == null || item.longitud == null) return;
            const pos = [item.latitud, item.longitud];
            bounds.push(pos);
            const marker = L.marker(pos, { icon: crearIcono(item.prioridad) })
                .bindPopup(`<strong>${item.voluntario}</strong><br>${item.direccion}<br><em>${item.prioridad}</em>`)
                .addTo(markersLayer);
            marcadoresPorId[item.id] = marker;
        });
        if (bounds.length > 0) map.flyToBounds(bounds, { padding: [50, 50], maxZoom: 14 });
    }

    function renderListado(lista) {
        listadoDiv.innerHTML = '';
        if (lista.length === 0) {
            listadoDiv.innerHTML = '<p class="text-muted text-center py-4 mb-0">No se encontraron resultados.</p>';
            return;
        }
        lista.forEach(item => {
            const prio = (item.prioridad || 'bajo').toLowerCase();
            const card = document.createElement('div');
            card.className = `seg-ayuda-item prio-${prio}`;
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <strong class="small">${item.voluntario || 'Anónimo'}</strong>
                    <span class="badge badge-light border text-uppercase">${item.prioridad}</span>
                </div>
                <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt mr-1"></i>${item.direccion}</p>
                <p class="small mb-1">${item.detalle || 'Solicitud de apoyo.'}</p>
                <div class="d-flex justify-content-between small text-muted">
                    <span class="text-capitalize">${item.estado}</span>
                    <span>${item.fecha}</span>
                </div>`;
            card.addEventListener('click', () => {
                const marker = marcadoresPorId[item.id];
                if (marker) { map.flyTo(marker.getLatLng(), 15); marker.openPopup(); }
            });
            listadoDiv.appendChild(card);
        });
    }

    function aplicarFiltros() {
        const q = buscarNombre.value.trim().toLowerCase();
        const prio = prioridadFiltro.value.toLowerCase();
        const est = estadoFiltro.value.toLowerCase();
        const filtradas = datos.filter(item => {
            const nombreOk = q === '' || item.voluntario.toLowerCase().includes(q) || (item.detalle ?? '').toLowerCase().includes(q);
            return nombreOk && (prio === '' || item.prioridad === prio) && (est === '' || item.estado === est);
        });
        renderListado(filtradas);
        renderMapa(filtradas);
    }

    buscarNombre.addEventListener('input', aplicarFiltros);
    prioridadFiltro.addEventListener('change', aplicarFiltros);
    estadoFiltro.addEventListener('change', aplicarFiltros);
    btnLimpiar.addEventListener('click', () => {
        buscarNombre.value = '';
        prioridadFiltro.value = '';
        estadoFiltro.value = '';
        aplicarFiltros();
    });
    aplicarFiltros();
});
</script>
@endpush
