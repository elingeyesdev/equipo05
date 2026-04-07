<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Monitoreo de incendios')</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    @stack('head')
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
            --danger: #c2410c;
            --danger-soft: #fff7ed;
        }

        * { box-sizing: border-box; }

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
            margin-bottom: 1.75rem;
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
            max-width: 42ch;
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
            50% { box-shadow: 0 0 0 10px rgba(255, 173, 128, 0); }
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

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.55rem 1.15rem;
            border-radius: 999px;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            font-family: inherit;
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s;
        }

        .btn-primary {
            background: var(--accent);
            color: var(--text);
            border-color: rgba(0, 0, 0, 0.06);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-hover);
        }

        .btn-ghost {
            background: var(--surface);
            color: var(--text);
            border-color: var(--accent-border);
        }

        .btn-ghost:hover {
            background: var(--accent-soft);
        }

        .btn-danger {
            background: var(--danger-soft);
            color: var(--danger);
            border-color: rgba(194, 65, 12, 0.35);
        }

        .btn-danger:hover {
            background: #ffedd5;
        }

        .btn-sm {
            padding: 0.4rem 0.85rem;
            font-size: 0.8rem;
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

        .alert {
            padding: 0.85rem 1.1rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.92rem;
            border: 1px solid var(--accent-border);
            background: var(--accent-soft);
            color: var(--text);
        }

        .alert-error {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .map-section {
            margin-bottom: 2rem;
        }

        .map-section h2 {
            margin: 0 0 0.65rem;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .map-section p.hint {
            margin: 0 0 0.75rem;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        #map, #map-picker {
            height: min(52vh, 480px);
            min-height: 280px;
            width: 100%;
            border-radius: var(--radius);
            border: 1px solid var(--accent-border);
            box-shadow: var(--shadow);
            z-index: 0;
        }

        .leaflet-container { font-family: inherit; }

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

        .card-inner { padding: 1.25rem 1.35rem 1.4rem; }

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

        .tag-estado-activo { background: rgba(255, 173, 128, 0.35); }
        .tag-estado-controlado { background: rgba(255, 173, 128, 0.2); }
        .tag-estado-extinguido {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: var(--text-muted);
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

        .field { margin-bottom: 0.85rem; }
        .field:last-of-type { margin-bottom: 0; }

        .field-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 0.2rem;
        }

        .field-value { font-size: 0.9rem; color: var(--text); }

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

        .card-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .empty {
            text-align: center;
            padding: 3.5rem 1.5rem;
            border-radius: var(--radius);
            border: 2px dashed var(--accent-border);
            background: var(--accent-soft);
        }

        .empty h2 { margin: 0 0 0.5rem; font-size: 1.2rem; }

        .empty p {
            margin: 0 auto;
            max-width: 46ch;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .footer-note {
            margin-top: 2.5rem;
            text-align: center;
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        /* Formulario */
        .form-page h1 { margin: 0 0 0.5rem; font-size: 1.45rem; }
        .form-page .back { margin-bottom: 1.25rem; display: inline-block; font-size: 0.9rem; color: var(--text-muted); }

        .form-grid {
            display: grid;
            gap: 1rem;
            max-width: 640px;
        }

        .form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .form-group input[type="text"],
        .form-group input[type="datetime-local"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border-radius: 10px;
            border: 1px solid var(--accent-border);
            font-family: inherit;
            font-size: 0.95rem;
            background: var(--surface);
        }

        .form-group textarea { min-height: 100px; resize: vertical; }

        .form-group .error {
            font-size: 0.8rem;
            color: var(--danger);
            margin-top: 0.25rem;
        }

        .coord-readout {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }

        .coord-readout strong { color: var(--text); font-variant-numeric: tabular-nums; }

        @media (max-width: 520px) {
            .masthead { flex-direction: column; align-items: flex-start; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
