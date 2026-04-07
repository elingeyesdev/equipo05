<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistema de Gestion de Incendios')</title>
    <style>
        :root {
            --accent: #ffad80;
            --accent-deep: #e8956a;
            --accent-soft: #fff3eb;
            --accent-border: rgba(255, 173, 128, 0.45);
            --text: #1c1c1c;
            --text-muted: #5c5c5c;
            --surface: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(1200px 600px at 10% -10%, var(--accent-soft), transparent 55%),
                radial-gradient(900px 500px at 100% 0%, rgba(255, 173, 128, 0.12), transparent 50%),
                #fff;
        }
        .container { width: min(1100px, 92vw); margin: 0 auto; padding: 1.5rem 0 2.5rem; }
        .topbar { background: var(--accent); color: var(--text); border-bottom: 1px solid var(--accent-border); }
        .topbar .container { display: flex; gap: .75rem; align-items: center; justify-content: space-between; padding: .9rem 0; }
        .brand { font-weight: 700; font-size: 1.05rem; }
        .nav { display: flex; flex-wrap: wrap; gap: .45rem; }
        .nav a {
            color: var(--text);
            text-decoration: none;
            border: 1px solid rgba(0, 0, 0, .12);
            background: rgba(255, 255, 255, .45);
            padding: .35rem .65rem;
            border-radius: 6px;
            font-size: .88rem;
        }
        .card { background: var(--surface); border: 1px solid var(--accent-border); border-radius: 10px; padding: 1rem; margin-top: 1rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #f1d5c4; padding: .55rem .65rem; text-align: left; font-size: .9rem; }
        th { background: #fff8f3; }
        .btn {
            background: var(--accent);
            color: var(--text);
            border: 1px solid rgba(0, 0, 0, .08);
            border-radius: 6px;
            padding: .45rem .75rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: .86rem;
        }
        .btn-gray { background: #616975; color: #fff; border-color: transparent; }
        .btn-light { background: #fff; color: var(--accent-deep); border: 1px solid var(--accent-border); }
        .row { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; }
        .field { display: grid; gap: .25rem; margin-bottom: .65rem; }
        input, select, textarea { border: 1px solid var(--accent-border); border-radius: 6px; padding: .45rem .55rem; font-size: .92rem; width: 100%; }
        .alert { background: #fffbf7; border: 1px solid var(--accent-border); color: var(--text); border-radius: 8px; padding: .65rem .8rem; margin: .5rem 0 1rem; }
        .error { color: #b00020; font-size: .8rem; }
        .status { padding: .2rem .45rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
        .status-ok { background: #e8f7ea; color: #185f2a; }
        .status-pendiente { background: #fff3eb; color: #9a3412; border: 1px solid var(--accent-border); }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container">
            <div class="brand">Sistema de Gestion de Incendios</div>
            <nav class="nav">
                <a href="{{ route('monitoreo.index') }}">Monitoreo</a>
                <a href="{{ route('voluntarios.index') }}">Voluntarios</a>
                <a href="{{ route('notificaciones.index') }}">Notificaciones</a>
                <a href="{{ route('historial.index') }}">Historial</a>
                <a href="{{ route('incendios.create') }}">Registrar incendio</a>
            </nav>
        </div>
    </header>

    <main class="container">
        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
