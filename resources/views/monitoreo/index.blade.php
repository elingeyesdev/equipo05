<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="30">
    <title>Monitoreo de incendios — Tiempo real</title>
    <style>
        :root {
            --accent: #ffad80;
            --accent-deep: #e8956a;
            --accent-soft: #fff3eb;
            --accent-border: rgba(255, 173, 128, 0.45);
            --text: #1c1c1c;
            --text-muted: #5c5c5c;
            --surface: #ffffff;
            --radius: 14px;
            --shadow: 0 8px 32px rgba(28, 28, 28, 0.06);
            --shadow-hover: 0 14px 40px rgba(255, 173, 128, 0.22);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 600px at 10% -10%, var(--accent-soft), transparent 55%),
                radial-gradient(900px 500px at 100% 0%, rgba(255, 173, 128, 0.12), transparent 50%),
                #fff;
            line-height: 1.5;
        }

        .wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 2.25rem 1.5rem 3rem;
        }

        .masthead {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1.25rem;
            margin-bottom: 2rem;
            padding-bottom: 1.75rem;
            border-bottom: 1px solid var(--accent-border);
        }

        .masthead h1 {
            margin: 0 0 0.35rem;
            font-size: clamp(1.55rem, 3vw, 2rem);
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .masthead p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.95rem;
            max-width: 36ch;
        }

        .pulse-dot {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: var(--accent-deep);
            margin-right: 0.45rem;
            vertical-align: middle;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: 0 0 0 0 rgba(255, 173, 128, 0.5);
        }

        @keyframes pulse {
            50% {
                box-shadow: 0 0 0 10px rgba(255, 173, 128, 0);
            }
        }

        .meta-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.65rem;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid var(--accent-border);
            background: var(--surface);
            color: var(--text);
        }

        .chip strong {
            color: var(--accent-deep);
            margin-left: 0.25rem;
        }

        .btn-maps {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 1rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            color: var(--text);
            background: var(--accent);
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-maps:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-hover);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 1rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            color: var(--text);
            background: var(--accent-soft);
            border: 1px solid var(--accent-border);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn-ghost:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-hover);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.35rem;
        }

        .card {
            position: relative;
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--accent-border);
            box-shadow: var(--shadow);
            padding: 0;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        .card-accent {
            height: 4px;
            width: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent-deep));
        }

        .card-inner {
            padding: 1.25rem 1.35rem 1.4rem;
        }

        .card h2 {
            margin: 0 0 0.75rem;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.35;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
            margin-bottom: 1rem;
        }

        .tag {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            border: 1px solid var(--accent-border);
            background: var(--accent-soft);
            color: var(--text);
        }

        .tag-estado-activo {
            background: rgba(255, 173, 128, 0.35);
        }

        .tag-estado-controlado {
            background: rgba(255, 173, 128, 0.2);
        }

        .riesgo-alto {
            border-color: rgba(232, 149, 106, 0.85);
            background: rgba(255, 173, 128, 0.42);
        }

        .riesgo-medio {
            border-color: var(--accent-border);
            background: rgba(255, 173, 128, 0.22);
        }

        .riesgo-bajo {
            border-color: var(--accent-border);
            background: var(--accent-soft);
        }

        .field {
            margin-bottom: 0.85rem;
        }

        .field:last-of-type {
            margin-bottom: 0;
        }

        .field-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 0.2rem;
        }

        .field-value {
            font-size: 0.9rem;
            color: var(--text);
        }

        .coords {
            font-variant-numeric: tabular-nums;
            font-family: ui-monospace, "Cascadia Code", "SF Mono", Menlo, Consolas, monospace;
            font-size: 0.85rem;
        }

        .desc {
            font-size: 0.88rem;
            color: var(--text-muted);
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-foot {
            margin-top: 1rem;
            padding-top: 0.85rem;
            border-top: 1px dashed var(--accent-border);
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .empty {
            text-align: center;
            padding: 3.5rem 1.5rem;
            border-radius: var(--radius);
            border: 2px dashed var(--accent-border);
            background: var(--accent-soft);
        }

        .empty h2 {
            margin: 0 0 0.5rem;
            font-size: 1.2rem;
        }

        .empty p {
            margin: 0 auto;
            max-width: 42ch;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .footer-note {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        @media (max-width: 520px) {
            .masthead {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <header class="masthead">
            <div>
                <h1><span class="pulse-dot" aria-hidden="true"></span>Monitoreo de incendios</h1>
                <p>Vista en tiempo real de incidentes activos o en control. Los datos se actualizan cada 30 segundos.</p>
            </div>
            <div class="meta-bar">
                <span class="chip">En pantalla <strong>{{ $incendios->count() }}</strong></span>
                <span class="chip">Actualización automática</span>
                <a class="btn-ghost" href="{{ route('voluntarios.index') }}">Ver voluntarios activos</a>
            </div>
        </header>

        @if ($incendios->isEmpty())
            <div class="empty" role="status">
                <h2>No hay incendios en monitoreo</h2>
                <p>Cuando existan registros con estado <strong>activo</strong> o <strong>controlado</strong>, aparecerán aquí en forma de tarjetas.</p>
            </div>
        @else
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
                    @endphp
                    <article class="card">
                        <div class="card-accent" aria-hidden="true"></div>
                        <div class="card-inner">
                            <h2>{{ $incendio->titulo }}</h2>
                            <div class="tags">
                                <span class="tag {{ $estadoSlug === 'activo' ? 'tag-estado-activo' : 'tag-estado-controlado' }}">
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
                                    <a class="btn-maps" href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">Ver en mapa</a>
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
                                · Última actualización: {{ $incendio->updated_at?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '—' }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <p class="footer-note">
            Esta página se recarga sola cada 30 segundos para mantener el listado al día.
        </p>
    </div>
</body>
</html>
