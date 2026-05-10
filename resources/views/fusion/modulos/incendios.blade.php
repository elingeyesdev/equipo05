@extends('layouts.app')

@section('title', 'Monitoreo de incendios')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title mb-0">Monitoreo de incendios</h3>
    </div>
    <div class="card-body">
        <p class="mb-3">
            Este módulo está integrado en la aplicación (<code>modulos/monitoreo-incendios-simulacion-main</code>)
            y comparte autenticación unificada con el resto del sistema.
        </p>

        <h5 class="text-muted small text-uppercase mb-2">Rutas web</h5>
        <ul class="mb-3">
            <li>Prefijo integrado: <code>/incendios/modulo/…</code></li>
            <li>Nombres de ruta: <code>incendios.*</code> (por ejemplo <code>incendios.dashboard</code>, <code>incendios.biomasas.index</code>).</li>
        </ul>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('incendios.welcome') }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
                <i class="fas fa-door-open"></i> Bienvenida pública
            </a>
            <a href="{{ route('incendios.dashboard') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-fire"></i> Dashboard incendios
            </a>
            <a href="{{ route('incendios.focos-incendios.index') }}" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-map-marker-alt"></i> Focos de incendio
            </a>
            <a href="{{ route('incendios.biomasas.index') }}" class="btn btn-outline-success btn-sm">
                <i class="fas fa-seedling"></i> Biomasas
            </a>
            <a href="{{ route('incendios.predictions.index') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-chart-line"></i> Predicciones
            </a>
            <a href="{{ route('incendios.datos-climaticos.index') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-cloud-sun"></i> Datos climáticos
            </a>
            <a href="{{ route('incendios.simulaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-flask"></i> Simulaciones
            </a>
            <a href="{{ route('incendios.tipo-biomasas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list"></i> Tipos de biomasa
            </a>
            <a href="{{ route('incendios.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-users"></i> Usuarios
            </a>
            <a href="{{ route('incendios.voluntarios.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-user-friends"></i> Voluntarios
            </a>
            <a href="{{ route('incendios.administradores.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-user-shield"></i> Administradores
            </a>
        </div>

        <p class="text-muted small mb-0">
            Utiliza estos accesos directos para revisar rápidamente las áreas principales del módulo.
        </p>
    </div>
</div>
@endsection

