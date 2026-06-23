<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Plataforma de gestión territorial')</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    @php
        $cssVer = fn (string $file) => file_exists(public_path($file)) ? filemtime(public_path($file)) : time();
    @endphp
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v={{ $cssVer('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/platform-shell.css') }}?v={{ $cssVer('css/platform-shell.css') }}">
    <link rel="stylesheet" href="{{ asset('css/platform-modules.css') }}?v={{ $cssVer('css/platform-modules.css') }}">
    @if(str_contains($bodyModuleClass ?? '', 'module-inventario'))
        <link rel="stylesheet" href="{{ asset('css/inventario-module.css') }}?v={{ $cssVer('css/inventario-module.css') }}">
    @endif
    @if(str_contains($bodyModuleClass ?? '', 'module-incendios'))
        <link rel="stylesheet" href="{{ asset('css/incendios-module.css') }}?v={{ $cssVer('css/incendios-module.css') }}">
    @endif
    @if(str_contains($bodyModuleClass ?? '', 'module-logistica'))
        <link rel="stylesheet" href="{{ asset('css/logistica-module.css') }}?v={{ $cssVer('css/logistica-module.css') }}">
    @endif
    @if(str_contains($bodyModuleClass ?? '', 'module-seguimiento'))
        <link rel="stylesheet" href="{{ asset('css/seguimiento-module.css') }}?v={{ $cssVer('css/seguimiento-module.css') }}">
    @endif
    @if(str_contains($bodyModuleClass ?? '', 'module-cuadrillas'))
        <link rel="stylesheet" href="{{ asset('css/cuadrillas-module.css') }}?v={{ $cssVer('css/cuadrillas-module.css') }}">
    @endif
    @if(str_contains($bodyModuleClass ?? '', 'module-rescate'))
        <link rel="stylesheet" href="{{ asset('css/rescate-module.css') }}?v={{ $cssVer('css/rescate-module.css') }}">
    @endif
    @if(str_contains($bodyModuleClass ?? '', 'module-territorial'))
        <link rel="stylesheet" href="{{ asset('css/mapa-operativo.css') }}?v={{ $cssVer('css/mapa-operativo.css') }}">
    @endif
    @yield('css')
    @stack('css')
    @stack('styles')
    <style>
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
        .total-original, .total-donado, .total-asignado, .total-campanias { background: #e0f2fe; }
        .total-utilizado, .total-especie { background: #fee2e2; }
        .total-disponible, .total-monetaria, .total-registros, .total-activas { background: #dcfce7; }
        .total-meta { background: #fef3c7; }

        .campania-badge-activa { padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; }
        .role-chip { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.75rem; background-color: #e5e7eb; color: #374151; margin: 0 0.15rem 0.15rem 0; }
        .user-avatar-circle { width: 40px; height: 40px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-weight: 600; background: #6366f1; color: white; }
    </style>
</head>
<body class="sidebar-mini layout-fixed platform-sidebar-stable {{ trim($bodyModuleClass ?? 'platform-ui module-transparencia') }}">
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
                @role('Administrador')
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

                        @can('admin.usuarios.gestionar')
                        <a href="{{ route('donaciones.create') }}" class="dropdown-item">
                            <i class="fas fa-hand-holding-heart mr-2"></i> Donación
                        </a>
                        <a href="{{ route('asignaciones.create') }}" class="dropdown-item">
                            <i class="fas fa-tasks mr-2"></i> Asignación
                        </a>
                        <a href="{{ route('mensajes.create') }}" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> Mensaje
                        </a>
                        @endcan
                    </div>
                </li>
                @endrole

                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            @php
                $sidebarBrand = match (true) {
                    str_contains($bodyModuleClass ?? '', 'module-territorial') => ['icon' => 'fa-globe-americas', 'label' => 'Territorial'],
                    str_contains($bodyModuleClass ?? '', 'module-cuadrillas') => ['icon' => 'fa-fire', 'label' => 'Cuadrillas'],
                    str_contains($bodyModuleClass ?? '', 'module-incendios') => ['icon' => 'fa-fire-alt', 'label' => 'Incendios'],
                    str_contains($bodyModuleClass ?? '', 'module-logistica') => ['icon' => 'fa-truck', 'label' => 'Logística'],
                    str_contains($bodyModuleClass ?? '', 'module-seguimiento') => ['icon' => 'fa-hands-helping', 'label' => 'Voluntarios'],
                    str_contains($bodyModuleClass ?? '', 'module-inventario') => ['icon' => 'fa-boxes', 'label' => 'Inventario'],
                    str_contains($bodyModuleClass ?? '', 'module-rescate') => ['icon' => 'fa-paw', 'label' => 'Rescate'],
                    default => ['icon' => 'fa-hand-holding-heart', 'label' => 'Plataforma'],
                };
            @endphp
            <a href="{{ route('home') }}" class="brand-link text-center">
                <i class="fas {{ $sidebarBrand['icon'] }} fa-lg"></i>
                <span class="brand-text font-weight-light ml-2">{{ $sidebarBrand['label'] }}</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-white"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block text-truncate" style="max-width: 200px;" title="{{ trim((Auth::user()->nombre ?? '').' '.(Auth::user()->apellido ?? '')) }}">
                            {{ trim((Auth::user()->nombre ?? 'Usuario').' '.(Auth::user()->apellido ?? '')) }}
                            <small class="d-block text-muted text-truncate">
                                {{ Auth::user()->getRoleNames()->first() ?? 'Sin Rol' }}
                            </small>
                        </a>
                    </div>
                </div>

                <nav class="mt-2">
                    @php
                        use App\Support\AccessControl;
                        $__u = auth()->user();
                        $__showAdmin = AccessControl::showSidebarModule($__u, 'admin');
                        $__showInventario = AccessControl::showSidebarModule($__u, 'inventario');
                        $__showInventarioDonante = AccessControl::showSidebarModule($__u, 'inventario_donante');
                        $__showIncendios = AccessControl::showSidebarModule($__u, 'incendios');
                        $__showIncendiosCiudadano = AccessControl::showSidebarModule($__u, 'incendios_ciudadano');
                        $__showLogistica = AccessControl::showSidebarModule($__u, 'logistica');
                        $__showSeguimiento = AccessControl::showSidebarModule($__u, 'seguimiento');
                        $__showCuadrillas = AccessControl::showSidebarModule($__u, 'cuadrillas');
                        $__showRescate = AccessControl::showSidebarModule($__u, 'rescate');
                        $__showSync = AccessControl::showSidebarModule($__u, 'sync');
                    @endphp
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-header">PROYECTOS</li>

                        @if($__showAdmin)
                        <li class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('roles.*') || request()->routeIs('usuarios.*') || request()->routeIs('campanias.*') || request()->routeIs('donaciones.*') || request()->routeIs('estados.*') || request()->routeIs('asignaciones.*') || request()->routeIs('gateway.trazabilidad.*') || request()->routeIs('reportes.trazabilidad.*') || request()->routeIs('mensajes.*') || request()->routeIs('chat.*') || request()->routeIs('saldosdonaciones.*') || request()->routeIs('reporte.cierreCaja*') ? 'menu-open' : '' }}" data-sidebar-key="mod-transparencia">
                            <a href="#" class="nav-link {{ request()->routeIs('dashboard') || request()->routeIs('roles.*') || request()->routeIs('usuarios.*') || request()->routeIs('campanias.*') || request()->routeIs('donaciones.*') || request()->routeIs('estados.*') || request()->routeIs('asignaciones.*') || request()->routeIs('gateway.trazabilidad.*') || request()->routeIs('reportes.trazabilidad.*') || request()->routeIs('mensajes.*') || request()->routeIs('chat.*') || request()->routeIs('saldosdonaciones.*') || request()->routeIs('reporte.cierreCaja*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p><span class="sidebar-menu-label">Transparencia y donaciones</span> <i class="fas fa-angle-left right"></i></p>
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
                        @endif

                        @if($__showInventarioDonante)
                        <li class="nav-item {{ request()->routeIs('inventario.donaciones.*') || request()->routeIs('inventario.campana.*') || request()->routeIs('inventario.puntos-recoleccion.*') || request()->routeIs('inventario.donante.mi-perfil*') || request()->routeIs('inventario.helpdesk') ? 'menu-open' : '' }}" data-sidebar-key="mod-donante">
                            <a href="#" class="nav-link {{ request()->routeIs('inventario.donaciones.*') || request()->routeIs('inventario.campana.*') || request()->routeIs('inventario.puntos-recoleccion.*') || request()->routeIs('inventario.donante.mi-perfil*') || request()->routeIs('inventario.helpdesk') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-hand-holding-heart"></i>
                                <p><span class="sidebar-menu-label">Mis donaciones</span> <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('inventario.donaciones.index') }}" class="nav-link {{ request()->routeIs('inventario.donaciones.index') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Mis donaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.donaciones.create') }}" class="nav-link {{ request()->routeIs('inventario.donaciones.create') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Registrar donación</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.campana.index') }}" class="nav-link {{ request()->routeIs('inventario.campana.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Campañas públicas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.puntos-recoleccion.index') }}" class="nav-link {{ request()->routeIs('inventario.puntos-recoleccion.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Puntos de recolección</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.donante.mi-perfil') }}" class="nav-link {{ request()->routeIs('inventario.donante.mi-perfil*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Mi perfil</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('inventario.helpdesk') }}" class="nav-link {{ request()->routeIs('inventario.helpdesk') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Ayuda</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if($__showInventario)
                        <li class="nav-item {{ request()->routeIs('inventario.*') ? 'menu-open' : '' }}" data-sidebar-key="mod-inventario">
                            <a href="#" class="nav-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-warehouse"></i>
                                <p><span class="sidebar-menu-label">Inventario</span> <i class="fas fa-angle-left right"></i></p>
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
                        @endif

                        @if($__showIncendiosCiudadano)
                        <li class="nav-item {{ request()->routeIs('incendios.*') ? 'menu-open' : '' }}" data-sidebar-key="mod-ciudadano">
                            <a href="#" class="nav-link {{ request()->routeIs('incendios.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-fire"></i>
                                <p><span class="sidebar-menu-label">Alertas y mis reportes</span> <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('incendios.dashboard') }}" class="nav-link {{ request()->routeIs('incendios.dashboard') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Alertas y mapa</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if($__showIncendios)
                        <li class="nav-item {{ request()->routeIs('incendios.*') || request()->routeIs('fusion.modulos.incendios') ? 'menu-open' : '' }}" data-sidebar-key="mod-incendios">
                            <a href="#" class="nav-link {{ request()->routeIs('incendios.*') || request()->routeIs('fusion.modulos.incendios') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-fire"></i>
                                <p><span class="sidebar-menu-label">Incendios</span> <i class="fas fa-angle-left right"></i></p>
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
                                <li class="nav-item">
                                    <a href="{{ route('incendios.datos-climaticos.index') }}" class="nav-link {{ request()->routeIs('incendios.datos-climaticos.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Datos climáticos</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if($__showRescate)
                        <li class="nav-item {{ request()->routeIs('rescate.*') || request()->routeIs('fusion.modulos.rescate') ? 'menu-open' : '' }}" data-sidebar-key="mod-rescate">
                            <a href="#" class="nav-link {{ request()->routeIs('rescate.*') || request()->routeIs('fusion.modulos.rescate') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-paw"></i>
                                <p><span class="sidebar-menu-label">Rescate silvestre</span> <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('rescate.home') }}" class="nav-link {{ request()->routeIs('rescate.home') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Dashboard</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.reports.index') }}" class="nav-link {{ request()->routeIs('rescate.reports.index') || request()->routeIs('rescate.reports.show') || request()->routeIs('rescate.reports.edit') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Reportes / Hallazgos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.reports.mapa-campo') }}" class="nav-link {{ request()->routeIs('rescate.reports.mapa-campo') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Mapa de Campo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.animal-files.index') }}" class="nav-link {{ request()->routeIs('rescate.animal-files.*') || request()->routeIs('rescate.animals.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Hojas de vida</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.transfers.index') }}" class="nav-link {{ request()->routeIs('rescate.transfers.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Traslados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.medical-evaluations.index') }}" class="nav-link {{ request()->routeIs('rescate.medical-evaluations.*') || request()->routeIs('rescate.medical-evaluation-transactions.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Evaluaciones médicas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.cares.index') }}" class="nav-link {{ request()->routeIs('rescate.cares.*') || request()->routeIs('rescate.animal-care-records.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Cuidados</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.care-feedings.index') }}" class="nav-link {{ request()->routeIs('rescate.care-feedings.*') || request()->routeIs('rescate.animal-feeding-records.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Alimentación</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.releases.index') }}" class="nav-link {{ request()->routeIs('rescate.releases.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Liberaciones</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.animal-histories.index') }}" class="nav-link {{ request()->routeIs('rescate.animal-histories.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Historial de animales</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.reportes.index') }}" class="nav-link {{ request()->routeIs('rescate.reportes.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Reportes internos</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('rescate.species.index') }}" class="nav-link {{ request()->routeIs('rescate.species.*') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i><p>Especies (catálogo)</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if($__showLogistica)
                        @php
                            $__logisticaConfigActive = request()->routeIs('logistica.configuracion')
                                || request()->routeIs('logistica.solicitante')
                                || request()->routeIs('logistica.destino')
                                || request()->routeIs('logistica.ubicacion')
                                || request()->routeIs('logistica.marca')
                                || request()->routeIs('logistica.tipo-vehiculo')
                                || request()->routeIs('logistica.usuario')
                                || request()->routeIs('logistica.rol')
                                || request()->routeIs('logistica.estado')
                                || request()->routeIs('logistica.tipo-emergencia')
                                || request()->routeIs('logistica.tipo-licencia')
                                || request()->routeIs('logistica.reporte');
                        @endphp
                        <li class="nav-item {{ request()->routeIs('logistica.*') || request()->routeIs('fusion.modulos.logistica') ? 'menu-open' : '' }}" data-sidebar-key="mod-logistica">
                            <a href="#" class="nav-link {{ request()->routeIs('logistica.*') || request()->routeIs('fusion.modulos.logistica') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-truck"></i>
                                <p><span class="sidebar-menu-label">Logística</span> <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('logistica.estadisticas') }}" class="nav-link {{ request()->routeIs('logistica.estadisticas') || request()->routeIs('logistica.dashboard') ? 'active' : '' }}">
                                        <i class="fas fa-tachometer-alt nav-icon"></i><p>Estadísticas</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.solicitud') }}" class="nav-link {{ request()->routeIs('logistica.solicitud') || request()->routeIs('logistica.solicitud.*') ? 'active' : '' }}">
                                        <i class="fas fa-file nav-icon"></i><p>Solicitudes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.paquete') }}" class="nav-link {{ request()->routeIs('logistica.paquete') || request()->routeIs('logistica.paquete.*') || request()->routeIs('logistica.seguimiento.tracking') ? 'active' : '' }}">
                                        <i class="fas fa-box nav-icon"></i><p>Paquetes</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.flota') }}" class="nav-link {{ request()->routeIs('logistica.flota') || request()->routeIs('logistica.vehiculo') || request()->routeIs('logistica.conductor') ? 'active' : '' }}">
                                        <i class="fas fa-truck-loading nav-icon"></i><p>Flota</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.mapa') }}" class="nav-link {{ request()->routeIs('logistica.mapa') ? 'active' : '' }}">
                                        <i class="fas fa-map-marked-alt nav-icon"></i><p>Mapa operativo</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('logistica.configuracion') }}" class="nav-link {{ $__logisticaConfigActive ? 'active' : '' }}">
                                        <i class="fas fa-cog nav-icon"></i><p>Configuración</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if($__showSeguimiento)
                        <li class="nav-item {{ request()->routeIs('seguimiento.*') || request()->routeIs('fusion.modulos.seguimiento') ? 'menu-open' : '' }}" data-sidebar-key="mod-seguimiento">
                            <a href="#" class="nav-link {{ request()->routeIs('seguimiento.*') || request()->routeIs('fusion.modulos.seguimiento') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p><span class="sidebar-menu-label">Voluntarios</span> <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.estadisticas') }}" class="nav-link {{ request()->routeIs('seguimiento.estadisticas') || request()->routeIs('seguimiento.dashboard') ? 'active' : '' }}">
                                        <i class="fas fa-chart-bar nav-icon"></i><p>Estadísticas</p>
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
                                    <a href="{{ route('seguimiento.chat-consulta') }}" class="nav-link {{ request()->routeIs('seguimiento.chat-consulta') || request()->routeIs('seguimiento.chat.enviar') ? 'active' : '' }}">
                                        <i class="fas fa-comments nav-icon"></i><p>Chat de Voluntarios</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('seguimiento.helpdesk') }}" class="nav-link {{ request()->routeIs('seguimiento.helpdesk') ? 'active' : '' }}">
                                        <i class="fas fa-headset nav-icon"></i><p>Cuenta Voluntarios</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if($__showCuadrillas)
                        <li class="nav-item {{ request()->routeIs('cuadrillas.*') || request()->routeIs('fusion.modulos.cuadrillas') ? 'menu-open' : '' }}" data-sidebar-key="mod-cuadrillas">
                            <a href="#" class="nav-link {{ request()->routeIs('cuadrillas.*') || request()->routeIs('fusion.modulos.cuadrillas') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-fire"></i>
                                <p><span class="sidebar-menu-label">Cuadrillas e incendios</span> <i class="fas fa-angle-left right"></i></p>
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
                                    <a href="{{ route('cuadrillas.focos-calor') }}" class="nav-link {{ request()->routeIs('cuadrillas.focos-calor') ? 'active' : '' }}">
                                        <i class="fas fa-map-marked-alt nav-icon"></i><p>Mapa en Tiempo Real</p>
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
                            </ul>
                        </li>
                        @endif

                        @if($__showAdmin)
                        <li class="nav-header">COMANDO CENTRAL</li>
                        <li class="nav-item" data-sidebar-key="mod-territorial">
                            <a href="{{ route('territorial.dashboard') }}" class="nav-link {{ request()->routeIs('territorial.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-globe-americas text-info"></i>
                                <p><span class="sidebar-menu-label">Mapa territorial integrado</span></p>
                            </a>
                        </li>
                        @endif

                        @if($__showSync)
                        <li class="nav-header">SISTEMA</li>
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-sync-alt"></i>
                                <p><span class="sidebar-menu-label">Sincronizaciones</span> <i class="right fas fa-angle-left"></i></p>
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
                        @endif

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
            @hasSection('header')
            <div class="content-header">
                <div class="container-fluid">
                    @yield('header')
                </div>
            </div>
            @endif
            @php
                $rescateModuleChrome = str_contains($bodyModuleClass ?? '', 'module-rescate')
                    && ! request()->routeIs('rescate.reports.claim');
            @endphp
            <section class="content">
                <div class="container-fluid{{ $rescateModuleChrome ? ' res-page-shell res-page-shell--layout' : '' }}">
                    @if($rescateModuleChrome)
                        @include('fusion.modulos.partials.rescate-module-nav')
                        @include('fusion.modulos.partials.rescate-flash')
                    @endif

                    @hasSection('content_header_title')
                        @unless($rescateModuleChrome)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <h1 class="m-0 text-dark">@yield('content_header_title')
                                        @hasSection('content_header_subtitle')
                                            <small class="text-muted"> @yield('content_header_subtitle')</small>
                                        @endif
                                    </h1>
                                    @hasSection('subtitle')
                                        <p class="text-muted mb-0 small">@yield('subtitle')</p>
                                    @endif
                                </div>
                            </div>
                        @endunless
                    @endif

                    @hasSection('content_header')
                        <div class="mb-3">
                            @yield('content_header')
                        </div>
                    @endif

                    @hasSection('content_body')
                        @yield('content_body')
                    @else
                        @yield('content')
                    @endif
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <strong>© {{ date('Y') }} Plataforma de gestión territorial e incendios</strong>
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> 1.0.0
            </div>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    @php $sidebarJsVer = file_exists(public_path('js/platform-sidebar-persist.js')) ? filemtime(public_path('js/platform-sidebar-persist.js')) : time(); @endphp
    <script src="{{ asset('js/platform-sidebar-persist.js') }}?v={{ $sidebarJsVer }}"></script>
    @yield('js')
    @stack('js')

    @stack('scripts')
</body>
</html>