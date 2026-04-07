<<<<<<< HEAD
@extends('monitoreo.layout')
=======
@extends('layouts.app')
>>>>>>> origin/santiago

@section('title', 'Monitoreo de incendios')

@section('content')
<<<<<<< HEAD
    <div class="wrap">
        <header class="masthead">
            <div>
                <h1><span class="pulse-dot" aria-hidden="true"></span>Monitoreo de incendios</h1>
                <p>Mapa en vivo de todos los registros. Podés crear, editar o eliminar incidentes desde las tarjetas o el botón superior.</p>
            </div>
            <div class="meta-bar">
                <span class="chip">Registros <strong>{{ $incendios->count() }}</strong></span>
                <a class="btn btn-primary" href="{{ route('incendios.create') }}">+ Registrar incendio</a>
            </div>
        </header>

        @if (session('success'))
            <div class="alert" role="status">{{ session('success') }}</div>
        @endif

        <section class="historial-link" aria-label="Enlace al historial" style="margin-bottom:1.5rem;padding:0.85rem 1.1rem;border-radius:12px;border:1px solid rgba(255,173,128,0.45);background:#fffbf7;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0.75rem">
            <p style="margin:0;font-size:0.9rem;color:#5c5c5c;max-width:48ch">Consultá el listado completo de incendios (incluidos finalizados) con fechas de registro y ubicación.</p>
            <a href="{{ route('historial.index') }}" class="btn btn-primary" style="white-space:nowrap">Historial de incendios</a>
        </section>

        <section class="map-section" aria-labelledby="map-heading">
            <h2 id="map-heading">Mapa</h2>
            <p class="hint">Cada punto muestra un incendio. Hacé clic para ver detalle y acceso rápido a edición.</p>
            <div id="map" role="application" aria-label="Mapa de incendios"></div>
        </section>

        @if ($incendios->isEmpty())
            <div class="empty" role="status">
                <h2>No hay incendios registrados</h2>
                <p>Usá <strong>Registrar incendio</strong> para cargar el primero y verlo en el mapa.</p>
            </div>
        @else
            <h2 style="margin:0 0 1rem;font-size:1.05rem;font-weight:700">Listado</h2>
            <div class="grid">
                @foreach ($incendios as $incendio)
                    @php
                        $estadoSlug = strtolower((string) $incendio->estado);
                        $riesgoSlug = strtolower((string) $incendio->nivel_riesgo);
                        $riesgoClass = match (true) {
                            str_contains($riesgoSlug, 'alto') => 'riesgo-alto',
                            str_contains($riesgoSlug, 'medio') => 'riesgo-medio',
                            default => 'riesgo-bajo',
                        };
                        $mapsUrl = 'https://www.openstreetmap.org/?mlat=' . urlencode((string) $incendio->latitud) . '&mlon=' . urlencode((string) $incendio->longitud) . '#map=14/' . urlencode((string) $incendio->latitud) . '/' . urlencode((string) $incendio->longitud);
                        $estadoTag = match ($estadoSlug) {
                            'activo' => 'tag-estado-activo',
                            'controlado' => 'tag-estado-controlado',
                            default => 'tag-estado-extinguido',
                        };
                    @endphp
                    <article class="card">
                        <div class="card-accent" aria-hidden="true"></div>
                        <div class="card-inner">
                            <h2>{{ $incendio->titulo }}</h2>
                            <div class="tags">
                                <span class="tag {{ $estadoTag }}">
                                    {{ ucfirst($incendio->estado) }}
                                </span>
                                <span class="tag {{ $riesgoClass }}">
                                    Riesgo: {{ ucfirst($incendio->nivel_riesgo) }}
                                </span>
                            </div>
                            <div class="field">
                                <div class="field-label">Ubicación</div>
                                <div class="field-value coords">
                                    {{ $incendio->latitud }}, {{ $incendio->longitud }}
                                </div>
                                <div style="margin-top:0.5rem">
                                    <a class="btn-maps" href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">Abrir en OSM</a>
                                </div>
                            </div>
                            <div class="field">
                                <div class="field-label">Descripción</div>
                                <div class="field-value desc">
                                    {{ $incendio->descripcion ?: 'Sin descripción registrada.' }}
                                </div>
                            </div>
                            <div class="card-foot">
                                Inicio: {{ $incendio->fecha_inicio?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}
                                · Actualizado: {{ $incendio->updated_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}
                            </div>
                            <div class="card-actions">
                                <a class="btn btn-ghost btn-sm" href="{{ route('incendios.edit', $incendio) }}">Editar</a>
                                <form action="{{ route('incendios.destroy', $incendio) }}" method="post" style="display:inline" onsubmit="return confirm('¿Eliminar este incendio? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <p class="footer-note">
            Los datos se guardan en tu base SQLite. Actualizá la página para ver cambios hechos en otra pestaña.
        </p>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const points = @json($mapPoints);
    const mapEl = document.getElementById('map');
    if (!mapEl || typeof L === 'undefined') return;

    const map = L.map('map', { scrollWheelZoom: true });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text == null ? '' : String(text);
        return div.innerHTML;
    }

    function colorForEstado(estado) {
        const e = String(estado || '').toLowerCase();
        if (e === 'activo') return '#ffad80';
        if (e === 'controlado') return '#e8956a';
        return '#9ca3af';
    }

    const bounds = [];
    points.forEach(function (p) {
        const lat = p.lat;
        const lng = p.lng;
        bounds.push([lat, lng]);
        const fill = colorForEstado(p.estado);
        const circle = L.circleMarker([lat, lng], {
            radius: 11,
            color: '#1c1c1c',
            weight: 2,
            fillColor: fill,
            fillOpacity: 0.92
        }).addTo(map);

        const html =
            '<div style="min-width:180px">' +
            '<strong>' + escapeHtml(p.titulo) + '</strong><br>' +
            '<span style="opacity:.85">Estado:</span> ' + escapeHtml(p.estado) + '<br>' +
            '<span style="opacity:.85">Riesgo:</span> ' + escapeHtml(p.nivel_riesgo) + '<br>' +
            '<a href="' + String(p.editUrl).replace(/"/g, '&quot;') + '" style="display:inline-block;margin-top:8px;font-weight:600;color:#e8956a">Editar registro</a>' +
            '</div>';
        circle.bindPopup(html);
    });

    if (bounds.length === 0) {
        map.setView([-34.6, -58.38], 6);
    } else if (bounds.length === 1) {
        map.setView(bounds[0], 13);
    } else {
        map.fitBounds(bounds, { padding: [48, 48] });
    }
})();
</script>
@endpush
=======
    <div class="row">
        <h1 style="margin:0;">Monitoreo de incendios (Tiempo real)</h1>
        <span>Total en pantalla: <strong>{{ $incendios->count() }}</strong></span>
    </div>
    <p style="margin-top:.3rem;">Vista de incidentes activos o controlados. Recarga manual para ver cambios recientes.</p>

    <div class="card">
        @if ($incendios->isEmpty())
            <p>No hay incendios en monitoreo.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Estado</th>
                        <th>Nivel de riesgo</th>
                        <th>Ubicacion</th>
                        <th>Inicio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incendios as $incendio)
                        <tr>
                            <td>{{ $incendio->titulo }}</td>
                            <td>{{ ucfirst($incendio->estado) }}</td>
                            <td>{{ ucfirst($incendio->nivel_riesgo) }}</td>
                            <td>{{ $incendio->latitud }}, {{ $incendio->longitud }}</td>
                            <td>{{ $incendio->fecha_inicio?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '---' }}</td>
                            <td class="row">
                                <a class="btn btn-light" href="{{ route('incendios.edit', $incendio) }}">Editar</a>
                                <form method="POST" action="{{ route('incendios.destroy', $incendio) }}" onsubmit="return confirm('¿Eliminar incendio?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-gray" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
>>>>>>> origin/santiago
