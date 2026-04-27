@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        .solicitudes-grid .col-md-3 { display: flex; }
        .solicitud-card {
            width: 100%;
            border-radius: 12px;
            border-top: 5px solid transparent;
            transition: all 0.25s ease;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }
        .solicitud-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.18);
        }
        .solicitud-card.estado-aprobada { border-top-color: #28a745; }
        .solicitud-card.estado-negada { border-top-color: #dc3545; }
        .solicitud-card.estado-pendiente { border-top-color: #ffc107; }
    </style>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span style="font-size: larger; font-weight: bolder;">Solicitudes</span>
            <div class="d-flex align-items-center" style="gap: .5rem;">
                <span class="badge badge-info">{{ $solicitudes->count() }} registros</span>
                <a href="{{ route('logistica.solicitud.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Crear nueva
                </a>
            </div>
        </div>
        <div class="px-4 pt-3">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter active" data-filter="todos">Todas</button>
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter" data-filter="aprobada">Aprobadas</button>
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter" data-filter="negada">Negadas</button>
                <button type="button" class="btn btn-outline-secondary btn-solicitud-filter" data-filter="pendiente">Pendientes</button>
            </div>
        </div>
        <div class="card-body bg-white">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="row solicitudes-grid">
                @forelse($solicitudes as $solicitud)
                    @php
                        $estado = strtolower((string) ($solicitud->estado ?? 'pendiente'));
                        $badgeClass = str_contains($estado, 'aprobad') ? 'badge-success' : (str_contains($estado, 'negad') ? 'badge-danger' : 'badge-warning');
                        $estadoFilter = str_contains($estado, 'aprobad') ? 'aprobada' : (str_contains($estado, 'negad') ? 'negada' : 'pendiente');
                    @endphp
                    <div class="col-md-3 solicitud-item" data-estado="{{ $estadoFilter }}">
                        <div class="card mb-3 shadow-sm bg-white solicitud-card estado-{{ $estadoFilter }}">
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

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-solicitud-filter');
    const items = document.querySelectorAll('.solicitud-item');
    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            buttons.forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');
            const value = btn.dataset.filter;
            items.forEach((item) => {
                item.style.display = (value === 'todos' || item.dataset.estado === value) ? '' : 'none';
            });
        });
    });
});
</script>
@endpush
@endsection
