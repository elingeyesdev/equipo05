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
        </div>

        <p class="text-muted small mb-0">
            Los hallazgos desde la landing y el registro rápido pueden enviarse sin cuenta; luego se puede asociar el reporte al iniciar sesión.
        </p>
    </div>
</div>
@endsection
