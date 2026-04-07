<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle de voluntario</title>
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
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
            background: radial-gradient(1200px 600px at 10% -10%, var(--accent-soft), transparent 55%), #fff;
        }
        .wrap { max-width: 900px; margin: 0 auto; padding: 2rem 1.2rem 3rem; }
        .card {
            border: 1px solid var(--accent-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            background: var(--surface);
            overflow: hidden;
        }
        .card-accent { height: 4px; background: linear-gradient(90deg, var(--accent), var(--accent-deep)); }
        .card-inner { padding: 1.2rem; }
        h1 { margin: 0 0 0.8rem; font-size: 1.5rem; }
        .meta { margin: 0.35rem 0; color: var(--text-muted); }
        .btn {
            display: inline-flex; align-items: center; padding: 0.45rem 1rem; border-radius: 999px;
            font-size: 0.8rem; font-weight: 600; text-decoration: none; color: var(--text);
            border: 1px solid var(--accent-border); background: var(--accent-soft);
        }
        .list { padding-left: 1rem; }
        .list li { margin-bottom: 0.55rem; }
        .role { font-size: 0.75rem; padding: 0.18rem 0.5rem; border: 1px solid var(--accent-border); border-radius: 999px; background: var(--accent-soft); }
    </style>
</head>
<body>
    <div class="wrap">
        <p><a class="btn" href="{{ route('voluntarios.index') }}">Volver a voluntarios</a></p>
        <article class="card">
            <div class="card-accent"></div>
            <div class="card-inner">
                <h1>{{ $voluntario->nombre_completo }}</h1>
                <p class="meta">Telefono: {{ $voluntario->telefono ?: 'No registrado' }}</p>
                <p class="meta">Email: {{ $voluntario->email ?: 'No registrado' }}</p>
                <p class="meta">Estado: {{ ucfirst($voluntario->estado) }}</p>

                <h2>Incendios donde participa</h2>
                @if ($voluntario->incendios->isEmpty())
                    <p class="meta">Sin asignacion actual</p>
                @else
                    <ul class="list">
                        @foreach ($voluntario->incendios as $incendio)
                            <li>
                                <strong>{{ $incendio->titulo }}</strong>
                                <span class="role">Rol: {{ $incendio->pivot->rol }}</span>
                                <div class="meta">
                                    Estado del incendio: {{ ucfirst($incendio->estado) }}
                                    @if ($incendio->fecha_inicio)
                                        · Inicio: {{ $incendio->fecha_inicio->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </article>
    </div>
</body>
</html>
