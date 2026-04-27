<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Sistema de Donaciones')</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @yield('css')
    @stack('css')
    @stack('styles')
    <style>
        .brand-link { background-color: #0d6efd !important; }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active { background-color: #0d6efd !important; }
        
        /* Estilos Chat */
        .chat-container { display: flex; gap: 1.5rem; flex-wrap: wrap; }
        .chat-sidebar { flex: 1 1 260px; max-width: 320px; }
        .chat-main { flex: 2 1 480px; min-width: 0; }
        .chat-window { max-height: 480px; overflow-y: auto; padding-right: 0.5rem; }
        .chat-message-group { margin-bottom: 1.5rem; }
        .chat-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; margin-bottom: 0.25rem; }
        .chat-bubble { display: inline-block; padding: 0.6rem 0.9rem; border-radius: 1rem; max-width: 80%; font-size: 0.9rem; position: relative; margin-bottom: 0.25rem; }
        .chat-bubble-left { background-color: #e5e7eb; color: #111827; border-bottom-left-radius: 0.2rem; }
        .chat-bubble-right { background-color: #6366f1; color: #ffffff; border-bottom-right-radius: 0.2rem; margin-left: auto; }
        .chat-meta { font-size: 0.75rem; color: #9ca3af; margin-top: 0.15rem; }
        .chat-meta span + span::before { content: "•"; margin: 0 0.25rem; }
        .chat-asunto { font-weight: 600; font-size: 0.85rem; color: #4b5563; margin-bottom: 0.15rem; }
        .chat-empty { text-align: center; color: #9ca3af; padding: 2rem 0; font-size: 0.9rem; }

        /* Estilos Tarjetas Resumen */
        .saldo-summary-grid, .donaciones-summary-grid, .donasig-summary-grid, .campanias-summary-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;
        }
        .saldo-summary-card, .donaciones-summary-card, .donasig-summary-card, .campanias-summary-card {
            border-radius: 0.75rem; padding: 1rem 1.2rem; color: #111827;
        }
        .saldo-summary-card h5, .donaciones-summary-card h5, .donasig-summary-card h5, .campanias-summary-card h5 {
            font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.2rem; opacity: 0.8;
        }
        .saldo-value, .summary-value { font-size: 1.3rem; font-weight: 700; }
        
        /* Colores Tarjetas */
        .total-original, .total-donado, .total-asignado, .total-campanias { background: linear-gradient(135deg, #e0f2fe, #eef2ff); }
        .total-utilizado, .total-especie { background: linear-gradient(135deg, #fee2e2, #fef9c3); }
        .total-disponible, .total-monetaria, .total-registros, .total-activas { background: linear-gradient(135deg, #dcfce7, #bbf7d0); }
        .total-meta { background: linear-gradient(135deg, #fef3c7, #fee2e2); }

        .campania-badge-activa { padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; }
        .role-chip { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.75rem; background-color: #e5e7eb; color: #374151; margin: 0 0.15rem 0.15rem 0; }
        .user-avatar-circle { width: 40px; height: 40px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; background: linear-gradient(135deg, #6366f1, #3b82f6); color: white; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('home') }}" class="nav-link">Inicio</a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                @hasanyrole('Administrador|Reportes')
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="#">
                        <i class="fas fa-plus-circle"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">Crear Nuevo</span>
                        <div class="dropdown-divider"></div>
                        
                        @role('Administrador')
                        <a href="{{ route('roles.create') }}" class="dropdown-item">
                            <i class="fas fa-user-tag mr-2"></i> Rol
                        </a>
                        <a href="{{ route('usuarios.create') }}" class="dropdown-item">
                            <i class="fas fa-user mr-2"></i> Usuario
                        </a>
                        <a href="{{ route('campanias.create') }}" class="dropdown-item">
                            <i class="fas fa-bullhorn mr-2"></i> Campaña
                        </a>
                        @endrole

                        @hasanyrole('Administrador|Reportes')
                        <a href="{{ route('donaciones.create') }}" class="dropdown-item">
                            <i class="fas fa-hand-holding-heart mr-2"></i> Donación
                        </a>
                        <a href="{{ route('asignaciones.create') }}" class="dropdown-item">
                            <i class="fas fa-tasks mr-2"></i> Asignación
                        </a>
                        <a href="{{ route('mensajes.create') }}" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> Mensaje
                        </a>
                        @endhasanyrole
                    </div>
                </li>
                @endhasanyrole

                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('home') }}" class="brand-link text-center">
                <i class="fas fa-hand-holding-heart fa-lg"></i>
                <span class="brand-text font-weight-light ml-2">Donaciones</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-white"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">
                            {{ Auth::user()->nombre ?? 'Usuario' }}
                            <small class="d-block text-muted">
                                {{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}
                            </small>
                        </a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-header">PROYECTOS</li>

                        <li class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('roles.*') || request()->routeIs('usuarios.*') || request()->routeIs('campanias.*') || request()->routeIs('donaciones.*') || request()->routeIs('estados.*') || request()->routeIs('asignaciones.*') || request()->routeIs('gateway.trazabilidad.*') || request()->routeIs('reportes.trazabilidad.*') || request()->routeIs('mensajes.*') || request()->routeIs('chat.*') || request()->routeIs('saldosdonaciones.*') || request()->routeIs('reporte.cierreCaja*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('roles.*') || request()->routeIs('usuarios.*') || request()->routeIs('campanias.*') || request()->routeIs('donaciones.*') || request()->routeIs('estados.*') || request()->routeIs('asignaciones.*') || request()->routeIs('gateway.trazabilidad.*') || request()->routeIs('reportes.trazabilidad.*') || request()->routeIs('mensajes.*') || request()->routeIs('chat.*') || request()->routeIs('saldosdonaciones.*') || request()->routeIs('reporte.cierreCaja*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>transparencia_donaciones_voluntarios-main <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Roles</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Usuarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('campanias.index') }}" class="nav-link {{ request()->routeIs('campanias.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Campañas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('donaciones.index') }}" class="nav-link {{ request()->routeIs('donaciones.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Donaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('asignaciones.index') }}" class="nav-link {{ request()->routeIs('asignaciones.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Asignaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('gateway.trazabilidad.index') }}" class="nav-link {{ request()->routeIs('gateway.trazabilidad.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Situaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('reportes.trazabilidad.index') }}" class="nav-link {{ request()->routeIs('reportes.trazabilidad.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Trazabilidad</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('chat.inbox') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Mensajes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('saldosdonaciones.index') }}" class="nav-link {{ request()->routeIs('saldosdonaciones.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Saldos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('inventario.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p>donacion-recepcion-inventario-main <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('inventario.home') }}" class="nav-link {{ request()->routeIs('inventario.home') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.usuario.index') }}" class="nav-link {{ request()->routeIs('inventario.usuario.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Usuarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.campana.index') }}" class="nav-link {{ request()->routeIs('inventario.campana.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Campañas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.donaciones.index') }}" class="nav-link {{ request()->routeIs('inventario.donaciones.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Donaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.donante.index') }}" class="nav-link {{ request()->routeIs('inventario.donante.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Donantes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.categorias-producto.index') }}" class="nav-link {{ request()->routeIs('inventario.categorias-producto.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Categorías</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.producto.index') }}" class="nav-link {{ request()->routeIs('inventario.producto.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Productos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.puntos-recoleccion.index') }}" class="nav-link {{ request()->routeIs('inventario.puntos-recoleccion.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Puntos de recolección</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.almacene.index') }}" class="nav-link {{ request()->routeIs('inventario.almacene.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Almacenes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.estante.index') }}" class="nav-link {{ request()->routeIs('inventario.estante.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Estantes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.espacio.index') }}" class="nav-link {{ request()->routeIs('inventario.espacio.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Espacios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.solicitudes-recoleccions.index') }}" class="nav-link {{ request()->routeIs('inventario.solicitudes-recoleccions.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Solicitudes de recolección</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.paquete.index') }}" class="nav-link {{ request()->routeIs('inventario.paquete.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Paquetes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.registros-salida.index') }}" class="nav-link {{ request()->routeIs('inventario.registros-salida.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Registros de salida</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.reportes.index') }}" class="nav-link {{ request()->routeIs('inventario.reportes.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Reportes</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('incendios.*') || request()->routeIs('fusion.modulos.incendios') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('incendios.*') || request()->routeIs('fusion.modulos.incendios') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-fire"></i>
                                <p>Monitoreo de Incendios <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('incendios.dashboard') }}" class="nav-link {{ request()->routeIs('incendios.dashboard') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Dashboard + Mapa</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('incendios.focos-incendios.index') }}" class="nav-link {{ request()->routeIs('incendios.focos-incendios.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Focos de Incendio</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('incendios.biomasas.index') }}" class="nav-link {{ request()->routeIs('incendios.biomasas.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Biomasas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('incendios.tipo-biomasas.index') }}" class="nav-link {{ request()->routeIs('incendios.tipo-biomasas.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Tipos de Biomasa</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('incendios.simulaciones.index') }}" class="nav-link {{ request()->routeIs('incendios.simulaciones.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Simulaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('incendios.predictions.index') }}" class="nav-link {{ request()->routeIs('incendios.predictions.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Predicciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('incendios.reports.fires') }}" class="nav-link {{ request()->routeIs('incendios.reports.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Reportes</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('rescate.*') || request()->routeIs('fusion.modulos.rescate') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('rescate.*') || request()->routeIs('fusion.modulos.rescate') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-paw"></i>
                                <p>Rescate de Animales Silvestres <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('rescate.home') }}" class="nav-link {{ request()->routeIs('rescate.home') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.reports.index') }}" class="nav-link {{ request()->routeIs('rescate.reports.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Reportes/Hallazgos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.reports.mapa-campo') }}" class="nav-link {{ request()->routeIs('rescate.reports.mapa-campo') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Mapa de Campo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.animals.index') }}" class="nav-link {{ request()->routeIs('rescate.animals.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Animales</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.animal-files.index') }}" class="nav-link {{ request()->routeIs('rescate.animal-files.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Hojas de Vida</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.transfers.index') }}" class="nav-link {{ request()->routeIs('rescate.transfers.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Traslados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.releases.index') }}" class="nav-link {{ request()->routeIs('rescate.releases.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Liberaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.rescuers.index') }}" class="nav-link {{ request()->routeIs('rescate.rescuers.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Rescatistas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.veterinarians.index') }}" class="nav-link {{ request()->routeIs('rescate.veterinarians.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Veterinarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.centers.index') }}" class="nav-link {{ request()->routeIs('rescate.centers.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Centros</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.species.index') }}" class="nav-link {{ request()->routeIs('rescate.species.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Especies</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('logistica.*') || request()->routeIs('fusion.modulos.logistica') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('logistica.*') || request()->routeIs('fusion.modulos.logistica') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-truck"></i>
                                <p>Logistica Transportacion Donaciones <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('logistica.estadisticas') }}" class="nav-link {{ request()->routeIs('logistica.estadisticas') || request()->routeIs('logistica.dashboard') ? 'active' : '' }}">
                                        <i class="fas fa-tachometer-alt nav-icon"></i><p>Estadisticas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.solicitud') }}" class="nav-link {{ request()->routeIs('logistica.solicitud') ? 'active' : '' }}">
                                        <i class="fas fa-file nav-icon"></i><p>Solicitudes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.paquete') }}" class="nav-link {{ request()->routeIs('logistica.paquete') ? 'active' : '' }}">
                                        <i class="fas fa-box nav-icon"></i><p>Paquetes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.seguimiento') }}" class="nav-link {{ request()->routeIs('logistica.seguimiento') ? 'active' : '' }}">
                                        <i class="fas fa-map nav-icon"></i><p>Seguimiento de Paquetes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.vehiculo') }}" class="nav-link {{ request()->routeIs('logistica.vehiculo') ? 'active' : '' }}">
                                        <i class="fas fa-car nav-icon"></i><p>Vehiculos</p>
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('logistica.solicitante') || request()->routeIs('logistica.destino') || request()->routeIs('logistica.ubicacion') || request()->routeIs('logistica.conductor') || request()->routeIs('logistica.marca') || request()->routeIs('logistica.tipo-vehiculo') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ request()->routeIs('logistica.solicitante') || request()->routeIs('logistica.destino') || request()->routeIs('logistica.ubicacion') || request()->routeIs('logistica.conductor') || request()->routeIs('logistica.marca') || request()->routeIs('logistica.tipo-vehiculo') ? 'active' : '' }}">
                                        <i class="fas fa-book-open nav-icon"></i>
                                        <p>Catalogos <i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.solicitante') }}" class="nav-link {{ request()->routeIs('logistica.solicitante') ? 'active' : '' }}">
                                                <i class="fas fa-user-friends nav-icon"></i><p>Solicitantes</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.destino') }}" class="nav-link {{ request()->routeIs('logistica.destino') ? 'active' : '' }}">
                                                <i class="fas fa-map-marker-alt nav-icon"></i><p>Destinos</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.ubicacion') }}" class="nav-link {{ request()->routeIs('logistica.ubicacion') ? 'active' : '' }}">
                                                <i class="fas fa-map-pin nav-icon"></i><p>Ubicaciones</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.conductor') }}" class="nav-link {{ request()->routeIs('logistica.conductor') ? 'active' : '' }}">
                                                <i class="fas fa-users nav-icon"></i><p>Conductores</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.marca') }}" class="nav-link {{ request()->routeIs('logistica.marca') ? 'active' : '' }}">
                                                <i class="fas fa-flag-checkered nav-icon"></i><p>Marcas</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.tipo-vehiculo') }}" class="nav-link {{ request()->routeIs('logistica.tipo-vehiculo') ? 'active' : '' }}">
                                                <i class="fas fa-th-large nav-icon"></i><p>Tipo de Vehiculo</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-header">ADMIN</li>
                                <li class="nav-item {{ request()->routeIs('logistica.usuario') || request()->routeIs('logistica.rol') || request()->routeIs('logistica.estado') || request()->routeIs('logistica.tipo-emergencia') || request()->routeIs('logistica.tipo-licencia') || request()->routeIs('logistica.reporte') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ request()->routeIs('logistica.usuario') || request()->routeIs('logistica.rol') || request()->routeIs('logistica.estado') || request()->routeIs('logistica.tipo-emergencia') || request()->routeIs('logistica.tipo-licencia') || request()->routeIs('logistica.reporte') ? 'active' : '' }}">
                                        <i class="fas fa-user-shield nav-icon"></i>
                                        <p>Administracion <i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.usuario') }}" class="nav-link {{ request()->routeIs('logistica.usuario') ? 'active' : '' }}">
                                                <i class="fas fa-user nav-icon"></i><p>Voluntarios</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.rol') }}" class="nav-link {{ request()->routeIs('logistica.rol') ? 'active' : '' }}">
                                                <i class="fas fa-user-shield nav-icon"></i><p>Roles</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.estado') }}" class="nav-link {{ request()->routeIs('logistica.estado') ? 'active' : '' }}">
                                                <i class="fas fa-flag nav-icon"></i><p>Estados</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.tipo-emergencia') }}" class="nav-link {{ request()->routeIs('logistica.tipo-emergencia') ? 'active' : '' }}">
                                                <i class="fas fa-plus nav-icon"></i><p>Tipo de Emergencia</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.tipo-licencia') }}" class="nav-link {{ request()->routeIs('logistica.tipo-licencia') ? 'active' : '' }}">
                                                <i class="fas fa-id-card nav-icon"></i><p>Licencias</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ route('logistica.reporte') }}" class="nav-link {{ request()->routeIs('logistica.reporte') ? 'active' : '' }}">
                                                <i class="fas fa-book nav-icon"></i><p>Reportes</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.galeria') }}" class="nav-link {{ request()->routeIs('logistica.galeria') ? 'active' : '' }}">
                                        <i class="fas fa-images nav-icon"></i><p>Galeria de Agradecimiento</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.helpdesk') }}" class="nav-link {{ request()->routeIs('logistica.helpdesk') ? 'active' : '' }}">
                                        <i class="fas fa-phone nav-icon"></i><p>Centro de Soporte</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('seguimiento.*') || request()->routeIs('fusion.modulos.seguimiento') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('seguimiento.*') || request()->routeIs('fusion.modulos.seguimiento') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Seguimiento Voluntarios Comunitarios <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.estadisticas') }}" class="nav-link {{ request()->routeIs('seguimiento.estadisticas') || request()->routeIs('seguimiento.dashboard') ? 'active' : '' }}">
                                        <i class="fas fa-chart-bar nav-icon"></i><p>Estadisticas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.voluntarios') }}" class="nav-link {{ request()->routeIs('seguimiento.voluntarios') ? 'active' : '' }}">
                                        <i class="fas fa-user-friends nav-icon"></i><p>Voluntarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.voluntarios-inactivos') }}" class="nav-link {{ request()->routeIs('seguimiento.voluntarios-inactivos') ? 'active' : '' }}">
                                        <i class="fas fa-user-clock nav-icon"></i><p>Voluntarios Inactivos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.evaluacion') }}" class="nav-link {{ request()->routeIs('seguimiento.evaluacion') ? 'active' : '' }}">
                                        <i class="fas fa-check-circle nav-icon"></i><p>Evaluacion</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.evaluacion-pruebas') }}" class="nav-link {{ request()->routeIs('seguimiento.evaluacion-pruebas') ? 'active' : '' }}">
                                        <i class="fas fa-user-check nav-icon"></i><p>Evaluacion Voluntarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.capacitaciones') }}" class="nav-link {{ request()->routeIs('seguimiento.capacitaciones') ? 'active' : '' }}">
                                        <i class="fas fa-chalkboard-teacher nav-icon"></i><p>Capacitaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.necesidades') }}" class="nav-link {{ request()->routeIs('seguimiento.necesidades') ? 'active' : '' }}">
                                        <i class="fas fa-plus-square nav-icon"></i><p>Necesidades</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.ayudas-solicitadas') }}" class="nav-link {{ request()->routeIs('seguimiento.ayudas-solicitadas') ? 'active' : '' }}">
                                        <i class="fas fa-hand-holding-heart nav-icon"></i><p>Ayudas Solicitadas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.administradores') }}" class="nav-link {{ request()->routeIs('seguimiento.administradores') ? 'active' : '' }}">
                                        <i class="fas fa-user-shield nav-icon"></i><p>Administradores</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.universidades') }}" class="nav-link {{ request()->routeIs('seguimiento.universidades') ? 'active' : '' }}">
                                        <i class="fas fa-graduation-cap nav-icon"></i><p>Universidades</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.chat-consulta') }}" class="nav-link {{ request()->routeIs('seguimiento.chat-consulta') ? 'active' : '' }}">
                                        <i class="fas fa-comments nav-icon"></i><p>Chat de Voluntarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.helpdesk') }}" class="nav-link {{ request()->routeIs('seguimiento.helpdesk') ? 'active' : '' }}">
                                        <i class="fas fa-headset nav-icon"></i><p>Centro de Soporte</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item {{ request()->routeIs('cuadrillas.*') || request()->routeIs('fusion.modulos.cuadrillas') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('cuadrillas.*') || request()->routeIs('fusion.modulos.cuadrillas') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-fire"></i>
                                <p>Cuadrillas Incendios Kardex Cursos <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.estadisticas') }}" class="nav-link {{ request()->routeIs('cuadrillas.estadisticas') || request()->routeIs('cuadrillas.dashboard') ? 'active' : '' }}">
                                        <i class="fas fa-tachometer-alt nav-icon"></i><p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.reportes') }}" class="nav-link {{ request()->routeIs('cuadrillas.reportes') ? 'active' : '' }}">
                                        <i class="fas fa-bullhorn nav-icon"></i><p>Reporte Rapido</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.reportes-incendio') }}" class="nav-link {{ request()->routeIs('cuadrillas.reportes-incendio') ? 'active' : '' }}">
                                        <i class="fas fa-fire-alt nav-icon"></i><p>Reportes de Incendio</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.focos-calor') }}" class="nav-link {{ request()->routeIs('cuadrillas.focos-calor') ? 'active' : '' }}">
                                        <i class="fas fa-map-marked-alt nav-icon"></i><p>Mapa en Tiempo Real</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.recursos') }}" class="nav-link {{ request()->routeIs('cuadrillas.recursos') ? 'active' : '' }}">
                                        <i class="fas fa-boxes nav-icon"></i><p>Recursos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.usuarios') }}" class="nav-link {{ request()->routeIs('cuadrillas.usuarios') ? 'active' : '' }}">
                                        <i class="fas fa-users nav-icon"></i><p>Usuarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.equipos') }}" class="nav-link {{ request()->routeIs('cuadrillas.equipos') ? 'active' : '' }}">
                                        <i class="fas fa-user-friends nav-icon"></i><p>Equipos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.comunarios') }}" class="nav-link {{ request()->routeIs('cuadrillas.comunarios') ? 'active' : '' }}">
                                        <i class="fas fa-hands-helping nav-icon"></i><p>Comunarios de Apoyo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.noticias') }}" class="nav-link {{ request()->routeIs('cuadrillas.noticias') ? 'active' : '' }}">
                                        <i class="fas fa-newspaper nav-icon"></i><p>Noticias</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.cursos') }}" class="nav-link {{ request()->routeIs('cuadrillas.cursos') ? 'active' : '' }}">
                                        <i class="fas fa-graduation-cap nav-icon"></i><p>Cursos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.inscritos') }}" class="nav-link {{ request()->routeIs('cuadrillas.inscritos') ? 'active' : '' }}">
                                        <i class="fas fa-user-plus nav-icon"></i><p>Inscritos</p>
                                    </a>
                                </li>
                                <li class="nav-item {{ request()->routeIs('cuadrillas.roles') || request()->routeIs('cuadrillas.generos') || request()->routeIs('cuadrillas.tipos-sangre') || request()->routeIs('cuadrillas.niveles-entrenamiento') || request()->routeIs('cuadrillas.niveles-gravedad') || request()->routeIs('cuadrillas.tipos-incidente') || request()->routeIs('cuadrillas.tipos-recurso') || request()->routeIs('cuadrillas.condiciones-climaticas') || request()->routeIs('cuadrillas.estados-sistema') ? 'menu-open' : '' }}">
                                    <a href="#" class="nav-link {{ request()->routeIs('cuadrillas.roles') || request()->routeIs('cuadrillas.generos') || request()->routeIs('cuadrillas.tipos-sangre') || request()->routeIs('cuadrillas.niveles-entrenamiento') || request()->routeIs('cuadrillas.niveles-gravedad') || request()->routeIs('cuadrillas.tipos-incidente') || request()->routeIs('cuadrillas.tipos-recurso') || request()->routeIs('cuadrillas.condiciones-climaticas') || request()->routeIs('cuadrillas.estados-sistema') ? 'active' : '' }}">
                                        <i class="fas fa-cog nav-icon"></i><p>Catalogos <i class="fas fa-angle-left right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item"><a href="{{ route('cuadrillas.roles') }}" class="nav-link {{ request()->routeIs('cuadrillas.roles') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Roles</p></a></li>
                                        <li class="nav-item"><a href="{{ route('cuadrillas.generos') }}" class="nav-link {{ request()->routeIs('cuadrillas.generos') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Generos</p></a></li>
                                        <li class="nav-item"><a href="{{ route('cuadrillas.tipos-sangre') }}" class="nav-link {{ request()->routeIs('cuadrillas.tipos-sangre') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Tipos de Sangre</p></a></li>
                                        <li class="nav-item"><a href="{{ route('cuadrillas.niveles-entrenamiento') }}" class="nav-link {{ request()->routeIs('cuadrillas.niveles-entrenamiento') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Niveles de Entrenamiento</p></a></li>
                                        <li class="nav-item"><a href="{{ route('cuadrillas.niveles-gravedad') }}" class="nav-link {{ request()->routeIs('cuadrillas.niveles-gravedad') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Niveles de Gravedad</p></a></li>
                                        <li class="nav-item"><a href="{{ route('cuadrillas.tipos-incidente') }}" class="nav-link {{ request()->routeIs('cuadrillas.tipos-incidente') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Tipos de Incidente</p></a></li>
                                        <li class="nav-item"><a href="{{ route('cuadrillas.tipos-recurso') }}" class="nav-link {{ request()->routeIs('cuadrillas.tipos-recurso') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Tipos de Recurso</p></a></li>
                                        <li class="nav-item"><a href="{{ route('cuadrillas.condiciones-climaticas') }}" class="nav-link {{ request()->routeIs('cuadrillas.condiciones-climaticas') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Condiciones Climaticas</p></a></li>
                                        <li class="nav-item"><a href="{{ route('cuadrillas.estados-sistema') }}" class="nav-link {{ request()->routeIs('cuadrillas.estados-sistema') ? 'active' : '' }}"><i class="far fa-circle nav-icon"></i><p>Estados del Sistema</p></a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.kardex') }}" class="nav-link {{ request()->routeIs('cuadrillas.kardex') ? 'active' : '' }}">
                                        <i class="fas fa-id-card nav-icon"></i><p>Mi Kardex</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('cuadrillas.helpdesk') }}" class="nav-link {{ request()->routeIs('cuadrillas.helpdesk') ? 'active' : '' }}">
                                        <i class="fas fa-headset nav-icon"></i><p>Centro de Soporte</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-header">SISTEMA</li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-sync-alt"></i>
                                <p>Sincronizaciones <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('api.campanias.sync') }}" class="nav-link" onclick="return confirm('¿Sincronizar campañas?')">
                                        <i class="fas fa-bullhorn nav-icon text-primary"></i><p>Campañas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('api.donaciones.dinero.sync') }}" class="nav-link" onclick="return confirm('¿Sincronizar dinero?')">
                                        <i class="fas fa-hand-holding-usd nav-icon text-success"></i><p>Donaciones ($)</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('integracion.sync.trazabilidad.especie') }}" class="nav-link" onclick="return confirm('¿Sincronizar especie?')">
                                        <i class="fas fa-route nav-icon text-warning"></i><p>Donaciones (Especie)</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('integracion.sync.almacenes') }}" class="nav-link" onclick="return confirm('¿Sincronizar almacenes?')">
                                        <i class="fas fa-warehouse nav-icon text-info"></i><p>Almacenes</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                                <p>Cerrar Sesión</p>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    @yield('header')
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <strong>© {{ date('Y') }} Sistema de Donaciones</strong>
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> 1.0.0
            </div>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    @yield('js')
    @stack('js')
    
    <script>
        (function () {
            const REFRESH_MS = 60000; 
            function autoRefreshEnabled() {
                if (document.body.classList.contains('no-auto-refresh')) return false;
                if (document.querySelector('.modal.show')) return false;
                return true;
            }
            setInterval(() => {
                if (!autoRefreshEnabled()) return;
                window.location.reload();
            }, REFRESH_MS);
        })();
    </script>

    @stack('scripts')
</body>
</html>