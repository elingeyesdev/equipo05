@extends('layouts.app')

@php
    use App\Support\LogisticaOperativa;
    $refPaquete = LogisticaOperativa::refPaquete((int) ($paquete->id_paquete ?? 0));
    $codigo = $paquete->codigo_seguimiento ?? $paquete->codigo ?? $refPaquete;
@endphp

@section('content_header_title', 'Tracking del paquete ' . $refPaquete)
@section('content_header_subtitle', $codigo)

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

<div class="row">
    <div class="col-lg-4 mb-3">
        <div class="card logistica-list-card shadow-sm h-100">
            <div class="card-header"><strong>Información del paquete</strong></div>
            <div class="card-body small">
                <p class="mb-1"><strong>Código:</strong> {{ $codigo }}</p>
                <p class="mb-1"><strong>Estado:</strong> {{ $paquete->nombre_estado ?? '—' }}</p>
                <p class="mb-1"><strong>Emergencia:</strong> {{ $paquete->tipo_emergencia ?? '—' }}</p>
                <hr>
                <p class="mb-1"><strong>Solicitante:</strong>
                    {{ trim(($paquete->solicitante_nombre ?? '') . ' ' . ($paquete->solicitante_apellido ?? '')) ?: '—' }}
                </p>
                <p class="mb-1"><strong>CI:</strong> {{ $paquete->solicitante_ci ?? '—' }}</p>
                <p class="mb-1"><strong>Destino:</strong> {{ $paquete->comunidad ?? '—' }}, {{ $paquete->provincia ?? '—' }}</p>
                @if(!empty($paquete->direccion))
                    <p class="mb-0 text-muted">{{ $paquete->direccion }}</p>
                @endif
            </div>
            <div class="card-footer bg-white">
                <a href="{{ route('logistica.seguimiento') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver al seguimiento
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-3">
        <div class="card logistica-list-card shadow-sm">
            <div class="card-header"><strong>Mapa del recorrido</strong></div>
            <div class="card-body p-0">
                <div id="logistica-tracking-map" class="logistica-mapa-container"></div>
            </div>
        </div>
    </div>
</div>

<div class="card logistica-list-card shadow-sm">
    <div class="card-header"><strong>Historial completo</strong></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 logistica-tabla-operativa">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Conductor</th>
                        <th>Vehículo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historial as $h)
                        <tr>
                            <td>{{ $h->fecha_actualizacion ? \Carbon\Carbon::parse($h->fecha_actualizacion)->format('d/m/Y H:i') : '—' }}</td>
                            <td>{{ $h->estado ?? '—' }}</td>
                            <td>{{ $h->conductor_nombre ?? '—' }}</td>
                            <td>{{ $h->vehiculo_placa ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">Sin historial registrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const points = @json($points);
    const destino = @json($destino);
    const hasDestino = destino.lat !== null && destino.lng !== null;

    if (!points.length && !hasDestino) {
        document.getElementById('logistica-tracking-map').innerHTML =
            '<div class="p-4 text-muted text-center">No hay coordenadas para mostrar el recorrido.</div>';
        return;
    }

    const center = points.length ? [points[0].lat, points[0].lng] : [destino.lat, destino.lng];
    const map = L.map('logistica-tracking-map').setView(center, 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    if (hasDestino) {
        L.marker([destino.lat, destino.lng], {
            icon: L.divIcon({
                className: 'bg-transparent',
                html: '<div style="background:#dc3545;width:28px;height:28px;border-radius:50%;border:2px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,.3);"><i class="fas fa-flag-checkered" style="color:#fff;font-size:12px;"></i></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14],
            })
        }).addTo(map).bindTooltip('Destino final', { permanent: true, direction: 'top', offset: [0, -12] });
    }

    if (points.length === 1) {
        L.marker([points[0].lat, points[0].lng]).addTo(map)
            .bindPopup(`<strong>${points[0].zona || 'Punto'}</strong>`);
    } else if (points.length > 1) {
        const latLngs = points.map(p => [p.lat, p.lng]);
        L.polyline(latLngs, { color: '#4f46e5', weight: 5, opacity: 0.75 }).addTo(map);
        points.forEach((p, i) => {
            L.marker([p.lat, p.lng]).addTo(map)
                .bindPopup(`<strong>Paso ${i + 1}</strong><br>${p.zona || ''}<br><small>${p.fecha || ''}</small>`);
        });
    }

    const bounds = L.latLngBounds([]);
    points.forEach(p => bounds.extend([p.lat, p.lng]));
    if (hasDestino) bounds.extend([destino.lat, destino.lng]);
    if (bounds.isValid()) map.fitBounds(bounds.pad(0.12));

    if (points.length > 1) {
        const truckIcon = L.divIcon({
            className: 'bg-transparent',
            html: '<div style="background:#4f46e5;width:32px;height:32px;border-radius:50%;border:2px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.25);"><i class="fas fa-truck" style="color:#fff;font-size:14px;"></i></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16],
        });
        const coords = points.map(p => L.latLng(p.lat, p.lng));
        let truck = L.marker(coords[0], { icon: truckIcon, zIndexOffset: 1000 }).addTo(map);
        let idx = 0;
        setInterval(function () {
            idx = (idx + 1) % coords.length;
            truck.setLatLng(coords[idx]);
        }, 2500);
    }

    setTimeout(function () { map.invalidateSize(); }, 250);
});
</script>
@endpush
