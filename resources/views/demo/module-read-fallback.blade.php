@extends('layouts.app')

@section('title', 'Modo demo sin base de datos')

@section('content')
<div class="container-fluid py-3">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">Modo demo activo (sin base de datos)</h3>
        </div>
        <div class="card-body">
            <p class="mb-2">
                Esta pantalla intentó consultar datos reales en <code>{{ $requestedPath }}</code>, pero actualmente el sistema está en modo demo sin tablas disponibles.
            </p>
            <p class="mb-3">
                Para evitar errores 500 durante tu presentación, la app muestra esta vista de respaldo y mantiene operativas las acciones de guardado en modo simulado.
            </p>

            <div class="d-flex flex-wrap gap-2">
                @if($isRescate)
                    <a href="{{ route('rescate.landing') }}" class="btn btn-outline-primary btn-sm">Ir a landing de rescate</a>
                    <a href="{{ route('rescate.reporte-rapido.create') }}" class="btn btn-outline-danger btn-sm">Registro rápido rescate</a>
                    <a href="{{ route('rescate.home') }}" class="btn btn-primary btn-sm">Panel rescate</a>
                @else
                    <a href="{{ route('incendios.dashboard') }}" class="btn btn-primary btn-sm">Panel incendios</a>
                    <a href="{{ route('incendios.simulaciones.index') }}" class="btn btn-outline-success btn-sm">Simulaciones</a>
                    <a href="{{ route('incendios.predictions.index') }}" class="btn btn-outline-info btn-sm">Predicciones</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

