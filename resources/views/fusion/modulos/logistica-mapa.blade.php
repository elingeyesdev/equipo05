@extends('layouts.app')

@section('content_header_title', 'Mapa operativo')
@section('content_header_subtitle', 'Destinos de solicitudes y rutas de entrega')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            <span class="badge badge-light border mr-auto">
                <i class="fas fa-map-marker-alt text-danger"></i> {{ count($marcadores) }} destinos georreferenciados
            </span>
            <a href="{{ route('logistica.solicitud.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva solicitud
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="logistica-mapa-operativo" class="logistica-mapa-container"></div>
    </div>
    <div class="card-footer bg-white small text-muted">
        <span class="mr-3"><i class="fas fa-circle text-warning"></i> Pendiente</span>
        <span class="mr-3"><i class="fas fa-circle text-primary"></i> Aprobada</span>
        <span class="mr-3"><i class="fas fa-circle text-info"></i> En ruta</span>
        <span class="mr-3"><i class="fas fa-circle text-success"></i> Entregada</span>
        <span><i class="fas fa-circle text-danger"></i> Rechazada</span>
    </div>
</div>

@if(count($marcadores) === 0)
<div class="alert alert-info mt-3">
    <i class="fas fa-info-circle mr-1"></i>
    Aún no hay solicitudes con coordenadas. Al crear una solicitud, seleccione la ubicación en el mapa del formulario.
</div>
@endif
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const marcadores = @json($marcadores);
    const colores = {
        pendiente: '#ffc107',
        aprobada: '#4f46e5',
        en_ruta: '#0891b2',
        entregada: '#059669',
        rechazada: '#dc3545',
    };

    const map = L.map('logistica-mapa-operativo').setView([-17.8146, -63.1561], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const almacen = L.marker([-17.8146, -63.1561]).addTo(map)
        .bindPopup('<strong>Almacén central</strong><br>Punto de despacho Santa Cruz');

    if (!marcadores.length) {
        setTimeout(function () { map.invalidateSize(); }, 200);
        return;
    }

    const bounds = L.latLngBounds([[ -17.8146, -63.1561 ]]);

    marcadores.forEach(function (m) {
        const color = colores[m.tipo] || '#64748b';
        const icon = L.divIcon({
            className: 'bg-transparent',
            html: `<div style="background:${color};width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.35);"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
        });
        const marker = L.marker([m.lat, m.lng], { icon }).addTo(map);
        let popup = `<strong>${m.ref}</strong> — ${m.comunidad}, ${m.provincia}<br>` +
            `<small>${m.solicitante} · ${m.emergencia}</small>`;
        if (m.direccion) popup += `<br><small class="text-muted">${m.direccion}</small>`;
        if (m.tracking_url) {
            popup += `<br><a href="${m.tracking_url}" class="btn btn-sm btn-outline-primary mt-2">Ver recorrido</a>`;
        }
        marker.bindPopup(popup);
        bounds.extend([m.lat, m.lng]);
    });

    map.fitBounds(bounds.pad(0.15));
    setTimeout(function () { map.invalidateSize(); }, 200);
});
</script>
@endpush
