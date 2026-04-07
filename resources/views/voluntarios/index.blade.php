<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Voluntarios activos</title>
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
        }
        .wrap { max-width: 1180px; margin: 0 auto; padding: 2.25rem 1.5rem 3rem; }
        .masthead {
            display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between;
            gap: 1rem; margin-bottom: 1.2rem; padding-bottom: 1.2rem; border-bottom: 1px solid var(--accent-border);
        }
        .masthead h1 { margin: 0 0 0.3rem; font-size: clamp(1.55rem, 3vw, 2rem); }
        .masthead p { margin: 0; color: var(--text-muted); }
        .actions { display: flex; gap: 0.6rem; flex-wrap: wrap; }
        .btn {
            display: inline-flex; align-items: center; padding: 0.45rem 1rem; border-radius: 999px;
            font-size: 0.8rem; font-weight: 600; text-decoration: none; color: var(--text);
            border: 1px solid var(--accent-border); background: var(--accent-soft);
        }
        .btn-primary { background: var(--accent); border-color: rgba(0, 0, 0, 0.06); }
        .chip {
            display: inline-flex; align-items: center; padding: 0.4rem 0.85rem; border-radius: 999px;
            font-size: 0.8rem; font-weight: 600; border: 1px solid var(--accent-border); background: var(--surface);
        }
        .chip strong { color: var(--accent-deep); margin-left: 0.25rem; }
        .panel {
            border: 1px solid var(--accent-border); background: var(--surface); border-radius: var(--radius);
            padding: 1rem; margin-bottom: 1.2rem; box-shadow: var(--shadow);
        }
        .filters { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.8rem; }
        label { display: block; font-size: 0.78rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.3rem; }
        input, select {
            width: 100%; border: 1px solid var(--accent-border); border-radius: 10px; padding: 0.55rem 0.65rem;
            font-size: 0.92rem; background: #fff;
        }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(310px, 1fr)); gap: 1rem; }
        .card {
            border: 1px solid var(--accent-border); border-radius: var(--radius); background: var(--surface);
            box-shadow: var(--shadow); overflow: hidden;
        }
        .card-accent { height: 4px; background: linear-gradient(90deg, var(--accent), var(--accent-deep)); }
        .card-inner { padding: 1rem 1.1rem 1.2rem; }
        .name { margin: 0 0 0.4rem; font-size: 1.06rem; }
        .meta { font-size: 0.9rem; color: var(--text-muted); margin: 0.16rem 0; }
        .section-title { margin: 0.9rem 0 0.45rem; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); }
        .list { margin: 0; padding-left: 1rem; }
        .list li { margin-bottom: 0.4rem; font-size: 0.9rem; }
        .role { font-size: 0.75rem; padding: 0.18rem 0.5rem; border: 1px solid var(--accent-border); border-radius: 999px; background: var(--accent-soft); }
        .empty {
            text-align: center; padding: 2rem; border: 2px dashed var(--accent-border);
            border-radius: var(--radius); background: var(--accent-soft);
        }
    </style>
</head>
<body>
    <div class="wrap">
        <header class="masthead">
            <div>
                <h1>Voluntarios</h1>
                <p>Gestión de voluntarios y su participación en incendios.</p>
            </div>
            <div class="actions">
                <span class="chip">Activos totales <strong>{{ $totalActivos }}</strong></span>
                <a class="btn" href="{{ route('monitoreo.index') }}">Volver a monitoreo</a>
            </div>
        </header>

        <section class="panel">
            <form method="GET" action="{{ route('voluntarios.index') }}">
                <div class="filters">
                    <div>
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="activo" @selected($estadoFiltro === 'activo')>Activo</option>
                            <option value="inactivo" @selected($estadoFiltro === 'inactivo')>Inactivo</option>
                        </select>
                    </div>
                    <div>
                        <label for="incendio_id">Incendio</label>
                        <select id="incendio_id" name="incendio_id">
                            <option value="">Todos</option>
                            @foreach ($incendios as $incendio)
                                <option value="{{ $incendio->id }}" @selected($incendioIdFiltro === $incendio->id)>
                                    {{ $incendio->titulo }} ({{ ucfirst($incendio->estado) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="q">Buscar por nombre</label>
                        <input id="q" type="text" name="q" value="{{ $busqueda }}" placeholder="Nombre o apellido">
                    </div>
                </div>
                <div class="actions" style="margin-top:0.8rem;">
                    <button class="btn btn-primary" type="submit">Aplicar filtros</button>
                    <a class="btn" href="{{ route('voluntarios.index') }}">Limpiar</a>
                </div>
            </form>
        </section>

        @if ($voluntarios->isEmpty())
            <div class="empty">
                <h2>No hay voluntarios para los filtros seleccionados</h2>
            </div>
        @else
            <section class="grid">
                @foreach ($voluntarios as $voluntario)
                    <article class="card">
                        <div class="card-accent"></div>
                        <div class="card-inner">
                            <h2 class="name">{{ $voluntario->nombre_completo }}</h2>
                            <p class="meta">Telefono: {{ $voluntario->telefono ?: 'No registrado' }}</p>
                            <p class="meta">Email: {{ $voluntario->email ?: 'No registrado' }}</p>
                            <p class="meta">Estado: {{ ucfirst($voluntario->estado) }}</p>

                            <p class="section-title">Incendios asignados</p>
                            @if ($voluntario->incendios->isEmpty())
                                <p class="meta">Sin asignacion actual</p>
                            @else
                                <ul class="list">
                                    @foreach ($voluntario->incendios as $incendio)
                                        <li>
                                            {{ $incendio->titulo }}
                                            <span class="role">Rol: {{ $incendio->pivot->rol }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="actions" style="margin-top:0.9rem;">
                                <a class="btn" href="{{ route('voluntarios.show', $voluntario->id) }}">Ver detalle</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif
    </div>
</body>
</html>
