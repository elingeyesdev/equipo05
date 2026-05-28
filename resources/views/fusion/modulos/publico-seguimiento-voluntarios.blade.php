<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguimiento de Voluntarios | Transparencia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('css/public-seguimiento.css') }}?v={{ file_exists(public_path('css/public-seguimiento.css')) ? filemtime(public_path('css/public-seguimiento.css')) : time() }}">
</head>
<body class="public-seguimiento-page">
    <header class="public-hero">
        <div class="container hero-inner">
            <div class="hero-text">
                <p class="hero-kicker"><i class="fas fa-user-friends mr-2"></i>Red de apoyo comunitario</p>
                <h1>Seguimiento de Voluntarios</h1>
                <p class="hero-lead">
                    Conoce la red de personas que colaboran en emergencias, capacitaciones y apoyo solidario.
                    Esta vista es informativa: no muestra datos personales sensibles.
                </p>
            </div>
            <div class="hero-actions">
                <a href="{{ route('login') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>Iniciar sesión
                </a>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-hand-holding-heart mr-2"></i>Quiero ser voluntario
                </a>
            </div>
        </div>
    </header>

    <main class="container public-main">
        <section class="row stats-row">
            <div class="col-6 col-lg-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-teal"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['voluntarios_activos'] }}</div>
                        <div class="stat-label">Voluntarios activos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-indigo"><i class="fas fa-graduation-cap"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['capacitaciones'] }}</div>
                        <div class="stat-label">Capacitaciones</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-amber"><i class="fas fa-life-ring"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['solicitudes_abiertas'] }}</div>
                        <div class="stat-label">Ayudas en curso</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon bg-green"><i class="fas fa-check-circle"></i></div>
                    <div>
                        <div class="stat-value">{{ $stats['solicitudes_atendidas'] }}</div>
                        <div class="stat-label">Ayudas atendidas</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="panel-card h-100">
                    <div class="panel-header">
                        <h2><i class="fas fa-id-badge mr-2 text-info"></i>Voluntarios registrados</h2>
                        <span class="badge badge-pill badge-info">{{ $voluntarios->count() }} visibles</span>
                    </div>
                    <p class="panel-desc text-muted">
                        Solo nombre y estado de participación. Correos, identificadores internos y roles administrativos no se publican.
                    </p>
                    @if($voluntarios->isEmpty())
                        <p class="text-muted mb-0">Aún no hay voluntarios registrados para mostrar.</p>
                    @else
                        <div class="volunteer-list">
                            @foreach($voluntarios as $voluntario)
                                <article class="volunteer-item">
                                    <div class="volunteer-avatar" aria-hidden="true">{{ $voluntario->inicial }}</div>
                                    <div class="volunteer-body">
                                        <strong>{{ $voluntario->nombre }}</strong>
                                        @if($voluntario->desde)
                                            <small class="text-muted d-block">En la red desde {{ $voluntario->desde }}</small>
                                        @endif
                                    </div>
                                    <span class="badge {{ $voluntario->activo ? 'badge-success' : 'badge-secondary' }}">
                                        {{ $voluntario->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="panel-card mb-4">
                    <div class="panel-header">
                        <h2><i class="fas fa-chalkboard-teacher mr-2 text-info"></i>Capacitaciones</h2>
                    </div>
                    <p class="panel-desc text-muted">Temas en los que la red se prepara para responder ante emergencias.</p>
                    @if($capacitaciones->isEmpty())
                        <p class="text-muted mb-0">No hay capacitaciones registradas.</p>
                    @else
                        <ul class="tag-list">
                            @foreach($capacitaciones as $nombre)
                                <li><span class="tag-pill">{{ $nombre }}</span></li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                @if($solicitudesPorEstado->isNotEmpty())
                    <div class="panel-card">
                        <div class="panel-header">
                            <h2><i class="fas fa-chart-pie mr-2 text-info"></i>Solicitudes de ayuda</h2>
                        </div>
                        <p class="panel-desc text-muted">Resumen agregado (sin datos personales de solicitantes).</p>
                        <ul class="estado-list">
                            @foreach($solicitudesPorEstado as $etiqueta => $total)
                                <li>
                                    <span>{{ $etiqueta }}</span>
                                    <strong>{{ $total }}</strong>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        @if($actividad->isNotEmpty())
            <section class="panel-card mb-4">
                <div class="panel-header">
                    <h2><i class="fas fa-bullhorn mr-2 text-info"></i>Actividad reciente de brigadas</h2>
                </div>
                <p class="panel-desc text-muted">Mensajes operativos publicados por la coordinación (sin identificar personas).</p>
                <ul class="activity-feed">
                    @foreach($actividad as $item)
                        <li>
                            <i class="fas fa-comment-dots text-info"></i>
                            <div>
                                <p class="mb-0">{{ $item->texto }}</p>
                                @if($item->fecha)
                                    <small class="text-muted">{{ $item->fecha }}</small>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="cta-card text-center">
            <h3 class="mb-2">¿Quieres colaborar o ver el detalle operativo?</h3>
            <p class="text-muted mb-3">
                Inicia sesión para acceder al módulo completo de voluntarios, evaluaciones y coordinación interna.
            </p>
            <a href="{{ route('login') }}" class="btn btn-info btn-lg">
                <i class="fas fa-sign-in-alt mr-2"></i>Acceder al sistema
            </a>
        </section>
    </main>

    <footer class="public-footer text-center text-muted">
        <small>Sistema de Donaciones y Emergencias — vista pública informativa</small>
    </footer>
</body>
</html>
