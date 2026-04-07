<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistema de Gestion de Incendios')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f6f8fb; color: #222; }
        .container { width: min(1100px, 92vw); margin: 0 auto; padding: 1.5rem 0 2.5rem; }
        .topbar { background: #b80000; color: #fff; }
        .topbar .container { display: flex; gap: .75rem; align-items: center; justify-content: space-between; padding: .9rem 0; }
        .brand { font-weight: 700; font-size: 1.05rem; }
        .nav { display: flex; flex-wrap: wrap; gap: .45rem; }
        .nav a { color: #fff; text-decoration: none; border: 1px solid rgba(255,255,255,.38); padding: .35rem .65rem; border-radius: 6px; font-size: .88rem; }
        .card { background: #fff; border: 1px solid #dde3ec; border-radius: 10px; padding: 1rem; margin-top: 1rem; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { border: 1px solid #e1e7ef; padding: .55rem .65rem; text-align: left; font-size: .9rem; }
        th { background: #f3f6fb; }
        .btn { background: #b80000; color: #fff; border: none; border-radius: 6px; padding: .45rem .75rem; cursor: pointer; text-decoration: none; display: inline-block; font-size: .86rem; }
        .btn-gray { background: #616975; }
        .btn-light { background: #fff; color: #b80000; border: 1px solid #b80000; }
        .row { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; }
        .field { display: grid; gap: .25rem; margin-bottom: .65rem; }
        input, select, textarea { border: 1px solid #cfd7e2; border-radius: 6px; padding: .45rem .55rem; font-size: .92rem; width: 100%; }
        .alert { background: #eaf7ee; border: 1px solid #bce7c8; color: #14532d; border-radius: 8px; padding: .65rem .8rem; margin: .5rem 0 1rem; }
        .error { color: #b00020; font-size: .8rem; }
        .status { padding: .2rem .45rem; border-radius: 999px; font-size: .75rem; font-weight: 700; }
        .status-ok { background: #e8f7ea; color: #185f2a; }
        .status-pendiente { background: #fff0f0; color: #b00020; }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="container">
            <div class="brand">Sistema de Gestion de Incendios</div>
            <nav class="nav">
                <a href="{{ route('monitoreo.index') }}">Monitoreo</a>
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
