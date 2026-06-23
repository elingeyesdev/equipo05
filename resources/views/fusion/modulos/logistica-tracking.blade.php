@extends('layouts.app')

@php
    use App\Support\LogisticaOperativa;
    $refPaquete = LogisticaOperativa::refPaquete((int) ($paquete->id_paquete ?? 0));
    $codigo = $paquete->codigo_seguimiento ?? $paquete->codigo ?? $refPaquete;
@endphp

@section('content_header_title', 'Ficha del paquete ' . $refPaquete)
@section('content_header_subtitle', 'Mapa, historial y datos de entrega · ' . $codigo)

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
            <div class="card-footer bg-white d-flex flex-wrap">
                <a href="{{ route('logistica.paquete') }}" class="btn btn-outline-secondary btn-sm mr-2 mb-1">
                    <i class="fas fa-arrow-left"></i> Volver a paquetes
                </a>
                <a href="{{ route('logistica.crud.edit', ['seccion' => 'paquete', 'id' => $paquete->id_paquete]) }}" class="btn btn-outline-warning btn-sm mr-2 mb-1">
                    <i class="fas fa-edit"></i> Editar paquete
                </a>
                <a href="{{ route('logistica.crud.create', ['seccion' => 'seguimiento']) }}" class="btn btn-primary btn-sm mb-1">
                    <i class="fas fa-plus"></i> Registrar avance
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb-3">
        <div class="card logistica-list-card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Mapa del recorrido</strong>
                <span class="badge badge-light border" id="logistica-ruta-estado">Calculando ruta…</span>
            </div>
            <div class="card-body p-0 position-relative">
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
@include('fusion.modulos.partials.logistica-routing-script')
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const waypoints = @json($waypoints);
    const statusEl = document.getElementById('logistica-ruta-estado');
    const mapEl = document.getElementById('logistica-tracking-map');

    if (!waypoints.length) {
        mapEl.innerHTML = '<div class="p-4 text-muted text-center">No hay coordenadas para mostrar el recorrido.</div>';
        if (statusEl) statusEl.textContent = 'Sin coordenadas';
        return;
    }

    const map = L.map('logistica-tracking-map').setView([waypoints[0].lat, waypoints[0].lng], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    try {
        await LogisticaRouting.dibujarRuta(map, waypoints, { color: '#4f46e5', animarCamion: waypoints.length >= 2 });
        if (statusEl) {
            statusEl.textContent = waypoints.length >= 2 ? 'Ruta por carretera' : '1 punto';
            statusEl.classList.remove('badge-light');
            statusEl.classList.add('badge-success');
        }
    } catch (e) {
        console.error(e);
        if (statusEl) statusEl.textContent = 'Ruta aproximada';
    }

    setTimeout(function () { map.invalidateSize(); }, 300);
});
</script>
@endpush
