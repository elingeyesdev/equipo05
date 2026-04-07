<<<<<<< HEAD
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial de incendios</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        :root {
            --accent: #ffad80;
            --accent-deep: #e8956a;
            --text: #1c1c1c;
            --muted: #6b7280;
            --surface: #ffffff;
            --radius: 12px;
            --border: rgba(255, 173, 128, 0.35);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: var(--text);
            background: #fff;
            line-height: 1.5;
        }

        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3rem;
        }

        .back {
            display: inline-block;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
            color: var(--muted);
            text-decoration: none;
        }

        .back:hover { color: var(--accent-deep); }

        .page-head {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--accent);
        }

        .page-head h1 {
            margin: 0 0 0.35rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }

        .page-head p {
            margin: 0;
            font-size: 0.95rem;
            color: var(--muted);
        }

        .table-shell {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: var(--surface);
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        thead th {
            text-align: left;
            padding: 0.85rem 1rem;
            background: linear-gradient(180deg, #fff8f3 0%, #fff 100%);
            border-bottom: 1px solid var(--border);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            white-space: nowrap;
        }

        tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }

        tbody tr:hover td { background: #fffbf7; }

        .mono {
            font-family: ui-monospace, Menlo, Consolas, monospace;
            font-size: 0.82rem;
            font-variant-numeric: tabular-nums;
        }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-activo {
            background: #fecaca;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        .badge-controlado {
            background: #fed7aa;
            color: #9a3412;
            border: 1px solid #fb923c;
        }

        .badge-finalizado {
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #d1d5db;
        }

        .btn-mapa {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text);
            background: var(--accent);
            border: 1px solid rgba(0, 0, 0, 0.06);
            cursor: pointer;
            font-family: inherit;
        }

        .btn-mapa:hover {
            background: var(--accent-deep);
            color: #fff;
        }

        .empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--muted);
            border-radius: var(--radius);
            border: 1px dashed var(--border);
            background: #fffbf7;
        }

        /* Modal */
        .modal {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
        }

        .modal.is-open {
            opacity: 1;
            visibility: visible;
        }

        .modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
        }

        .modal-box {
            position: relative;
            z-index: 1;
            width: min(520px, 100%);
            max-height: 90vh;
            overflow: auto;
            background: #fff;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            padding: 1rem 1rem 1.25rem;
        }

        .modal-box h2 {
            margin: 0 0 0.75rem;
            font-size: 1.05rem;
            padding-right: 2rem;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 0.5rem;
        }

        .modal-close {
            position: absolute;
            top: 0.65rem;
            right: 0.65rem;
            width: 2rem;
            height: 2rem;
            border: none;
            background: #f3f4f6;
            border-radius: 8px;
            font-size: 1.25rem;
            line-height: 1;
            cursor: pointer;
            color: var(--muted);
        }

        .modal-close:hover { background: #e5e7eb; }

        #mapa-mini {
            height: 260px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
    </style>
</head>
<body>
    <div class="wrap">
        <a class="back" href="{{ route('home') }}">← Volver al monitoreo</a>

        <header class="page-head">
            <h1>Historial de incendios</h1>
            <p>Registro completo ordenado por fecha de alta (más recientes primero).</p>
        </header>

        @if ($incendios->isEmpty())
            <div class="empty" role="status">No hay incendios registrados todavía.</div>
        @else
            <div class="table-shell">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Ubicación (Lat / Lng)</th>
                            <th>Estado</th>
                            <th>Nivel de riesgo</th>
                            <th>Fecha de registro</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incendios as $incendio)
                            @php
                                $est = strtolower((string) $incendio->estado);
                                $badgeClass = match ($est) {
                                    'activo' => 'badge-activo',
                                    'controlado' => 'badge-controlado',
                                    default => 'badge-finalizado',
                                };
                                $estadoEtiqueta = match ($est) {
                                    'activo' => 'Activo',
                                    'controlado' => 'Controlado',
                                    default => 'Finalizado',
                                };
                            @endphp
                            <tr>
                                <td class="mono">{{ $incendio->id }}</td>
                                <td>{{ $incendio->titulo }}</td>
                                <td class="mono">{{ $incendio->latitud }}, {{ $incendio->longitud }}</td>
                                <td><span class="badge {{ $badgeClass }}">{{ $estadoEtiqueta }}</span></td>
                                <td>{{ ucfirst($incendio->nivel_riesgo) }}</td>
                                <td>{{ $incendio->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn-mapa js-ver-mapa"
                                        data-lat="{{ $incendio->latitud }}"
                                        data-lng="{{ $incendio->longitud }}"
                                        data-titulo="{{ $incendio->titulo }}"
                                    >Ver mapa</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div id="mapa-modal" class="modal" aria-hidden="true" role="dialog" aria-labelledby="modal-titulo-text">
        <div class="modal-backdrop js-modal-cerrar" tabindex="-1"></div>
        <div class="modal-box">
            <button type="button" class="modal-close js-modal-cerrar" aria-label="Cerrar">&times;</button>
            <h2 id="modal-titulo-text"></h2>
            <div id="mapa-mini"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
(function () {
    const modal = document.getElementById('mapa-modal');
    const tituloEl = document.getElementById('modal-titulo-text');
    const mapEl = document.getElementById('mapa-mini');
    let miniMap = null;

    function cerrar() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (miniMap) {
            miniMap.remove();
            miniMap = null;
        }
        mapEl.innerHTML = '';
    }

    function abrir(lat, lng, titulo) {
        if (miniMap) {
            miniMap.remove();
            miniMap = null;
        }
        mapEl.innerHTML = '';

        tituloEl.textContent = titulo || 'Ubicación';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(function () {
            miniMap = L.map('mapa-mini').setView([lat, lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(miniMap);
            L.marker([lat, lng]).addTo(miniMap);
            setTimeout(function () { miniMap.invalidateSize(); }, 200);
        });
    }

    document.querySelectorAll('.js-ver-mapa').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const lat = parseFloat(btn.getAttribute('data-lat'));
            const lng = parseFloat(btn.getAttribute('data-lng'));
            const titulo = btn.getAttribute('data-titulo') || '';
            if (Number.isNaN(lat) || Number.isNaN(lng)) return;
            abrir(lat, lng, titulo);
        });
    });

    document.querySelectorAll('.js-modal-cerrar').forEach(function (el) {
        el.addEventListener('click', cerrar);
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) cerrar();
    });
})();
    </script>
</body>
</html>
=======
@extends('layouts.app')

@section('title', 'Historial de incendios')

@section('content')
    <h1 style="margin:0;">Modulo web de historial de incendios</h1>

    <div class="card">
        <form method="GET" action="{{ route('historial.index') }}" class="row">
            <div style="min-width:220px;flex:1;">
                <label for="incendio_id">Incendio</label>
                <select id="incendio_id" name="incendio_id">
                    <option value="">Todos</option>
                    @foreach($incendios as $incendio)
                        <option value="{{ $incendio->id }}" @selected((int)$incendioId === $incendio->id)>{{ $incendio->titulo }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:180px;flex:1;">
                <label for="estado_nuevo">Estado nuevo</label>
                <select id="estado_nuevo" name="estado_nuevo">
                    <option value="">Todos</option>
                    @foreach(['activo', 'controlado', 'extinguido'] as $estado)
                        <option value="{{ $estado }}" @selected($estadoNuevo === $estado)>{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="align-self:end;">
                <button class="btn" type="submit">Filtrar</button>
                <a class="btn btn-light" href="{{ route('historial.index') }}">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card">
        @if($historial->isEmpty())
            <p>No hay registros de historial.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Fecha de cambio</th>
                        <th>Incendio</th>
                        <th>Estado anterior</th>
                        <th>Estado nuevo</th>
                        <th>Descripcion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $registro)
                        <tr>
                            <td>{{ $registro->fecha_cambio?->format('d/m/Y H:i') ?? '---' }}</td>
                            <td>{{ $registro->incendio?->titulo ?? 'Sin incendio' }}</td>
                            <td>{{ $registro->estado_anterior ?? '---' }}</td>
                            <td>{{ $registro->estado_nuevo ?? '---' }}</td>
                            <td>{{ $registro->descripcion ?? 'Sin descripcion' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
>>>>>>> origin/santiago
