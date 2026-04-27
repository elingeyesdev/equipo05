@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span style="font-size: larger; font-weight: bolder;">Paquetes</span>
            <span class="badge badge-info">{{ $paquetes->count() }} registros</span>
        </div>
        <div class="card-body bg-white">
            <div class="row">
                @forelse($paquetes as $paquete)
                    @php
                        $estado = strtolower((string) ($paquete->estado_nombre ?? 'pendiente'));
                        $badgeClass = str_contains($estado, 'entreg') ? 'badge-success' : (str_contains($estado, 'camino') ? 'badge-info' : (str_contains($estado, 'armad') ? 'badge-primary' : 'badge-warning'));
                    @endphp
                    <div class="col-md-3 d-flex">
                        <div class="card mb-3 shadow-sm bg-white w-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Paquete {{ $paquete->codigo ?? $paquete->id_paquete }}</strong>
                                <span class="badge {{ $badgeClass }} text-uppercase">{{ $paquete->estado_nombre ?? 'Pendiente' }}</span>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Solicitante:</strong> {{ $paquete->solicitante_nombre ?? '-' }} {{ $paquete->solicitante_apellido ?? '' }}</p>
                                <p class="mb-1"><strong>CI:</strong> {{ $paquete->solicitante_ci ?? '-' }}</p>
                                <p class="mb-1"><strong>Emergencia:</strong> {{ $paquete->tipo_emergencia ?? '-' }}</p>
                                <p class="mb-1"><strong>Ubicación:</strong> {{ $paquete->ubicacion_actual ?? '-' }}</p>
                                <p class="mb-1"><strong>Creación:</strong> {{ $paquete->fecha_creacion ?? '-' }}</p>
                                <p class="mb-0"><strong>Entrega:</strong> {{ $paquete->fecha_entrega ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">No hay paquetes registrados.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
