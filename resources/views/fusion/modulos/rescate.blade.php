@extends('layouts.app')

@section('title', 'Rescate de animales silvestres')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title mb-0">Rescate de animales silvestres</h3>
    </div>
    <div class="card-body">
        <p class="mb-3">
            Este módulo está integrado en la aplicación (<code>modulos/rescate-animales-silvestres-main</code>)
            y comparte la autenticación unificada del sistema.
        </p>

        <h5 class="text-muted small text-uppercase mb-2">Rutas web</h5>
        <ul class="mb-3">
            <li>Prefijo integrado: <code>/rescate/modulo/…</code></li>
            <li>Nombres de ruta: <code>rescate.*</code> (por ejemplo <code>rescate.home</code>, <code>rescate.landing</code>).</li>
        </ul>

        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('rescate.landing') }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">
                <i class="fas fa-external-link-alt"></i> Vista pública (landing)
            </a>
            <a href="{{ route('rescate.reporte-rapido.create') }}" class="btn btn-outline-danger btn-sm" target="_blank" rel="noopener">
                <i class="fas fa-heartbeat"></i> Registro rápido de emergencia
            </a>
            @auth
                <a href="{{ route('rescate.home') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-tachometer-alt"></i> Panel del módulo
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt"></i> Iniciar sesión para el panel
                </a>
            @endauth
            <a href="{{ route('rescate.reports.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-clipboard-list"></i> Hallazgos
            </a>
            <a href="{{ route('rescate.animals.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-paw"></i> Animales
            </a>
            <a href="{{ route('rescate.animal-files.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-file-medical"></i> Hojas de vida
            </a>
            <a href="{{ route('rescate.transfers.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-route"></i> Traslados
            </a>
            <a href="{{ route('rescate.medical-evaluations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-stethoscope"></i> Eval. médicas
            </a>
            <a href="{{ route('rescate.releases.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-dove"></i> Liberaciones
            </a>
            <a href="{{ route('rescate.people.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-users"></i> Personas
            </a>
            <a href="{{ route('rescate.users.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-user-cog"></i> Usuarios
            </a>
        </div>

        <p class="text-muted small mb-0">
            Los hallazgos desde la landing y el registro rápido pueden enviarse sin cuenta; luego se puede asociar el reporte al iniciar sesión.
        </p>
    </div>
</div>
@endsection
