@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span style="font-size: larger; font-weight: bolder;">Solicitudes</span>
            <span class="badge badge-info">{{ $solicitudes->count() }} registros</span>
        </div>
        <div class="card-body bg-white">
            <div class="row">
                @forelse($solicitudes as $solicitud)
                    @php
                        $estado = strtolower((string) ($solicitud->estado ?? 'pendiente'));
                        $badgeClass = str_contains($estado, 'aprobad') ? 'badge-success' : (str_contains($estado, 'negad') ? 'badge-danger' : 'badge-warning');
                    @endphp
                    <div class="col-md-3 d-flex">
                        <div class="card mb-3 shadow-sm bg-white w-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Solicitud {{ $solicitud->codigo_seguimiento ?? $solicitud->id_solicitud }}</strong>
                                <span class="badge {{ $badgeClass }} text-uppercase">{{ $solicitud->estado ?? 'pendiente' }}</span>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Nombre:</strong> {{ $solicitud->solicitante_nombre ?? '-' }} {{ $solicitud->solicitante_apellido ?? '' }}</p>
                                <p class="mb-1"><strong>CI:</strong> {{ $solicitud->solicitante_ci ?? '-' }}</p>
                                <p class="mb-1"><strong>Celular:</strong> {{ $solicitud->solicitante_telefono ?? '-' }}</p>
                                <p class="mb-1"><strong>Comunidad:</strong> {{ $solicitud->destino_comunidad ?? '-' }}</p>
                                <p class="mb-1"><strong>Provincia:</strong> {{ $solicitud->destino_provincia ?? '-' }}</p>
                                <p class="mb-1"><strong>Emergencia:</strong> {{ $solicitud->tipo_emergencia ?? '-' }}</p>
                                <p class="mb-1"><strong>Afectados:</strong> {{ $solicitud->cantidad_personas ?? '-' }}</p>
                                <p class="mb-0"><strong>Fecha necesidad:</strong> {{ $solicitud->fecha_necesidad ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-muted">No hay solicitudes registradas.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
