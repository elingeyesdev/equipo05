@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        .paquetes-grid .col-md-3 { display: flex; }
        .paquete-card {
            width: 100%;
            border-radius: 12px;
            border-top: 5px solid transparent;
            transition: all 0.25s ease;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }
        .paquete-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.18);
        }
        .paquete-card.estado-entregado { border-top-color: #28a745; }
        .paquete-card.estado-camino { border-top-color: #17a2b8; }
        .paquete-card.estado-armado { border-top-color: #007bff; }
        .paquete-card.estado-pendiente { border-top-color: #ffc107; }
    </style>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span style="font-size: larger; font-weight: bolder;">Paquetes</span>
            <div class="d-flex align-items-center" style="gap: .5rem;">
                <span class="badge badge-info">{{ $paquetes->count() }} registros</span>
                <a href="{{ route('logistica.crud.create', ['seccion' => 'paquete']) }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Crear nuevo paquete
                </a>
            </div>
        </div>
        <div class="px-4 pt-3">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter active" data-filter="todos">Todos</button>
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter" data-filter="pendiente">Pendientes</button>
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter" data-filter="armado">Armados</button>
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter" data-filter="camino">En camino</button>
                <button type="button" class="btn btn-outline-secondary btn-paquete-filter" data-filter="entregado">Entregados</button>
            </div>
        </div>
        <div class="card-body bg-white">
            <div class="row paquetes-grid">
                @forelse($paquetes as $paquete)
                    @php
                        $estado = strtolower((string) ($paquete->estado_nombre ?? 'pendiente'));
                        $badgeClass = str_contains($estado, 'entreg') ? 'badge-success' : (str_contains($estado, 'camino') ? 'badge-info' : (str_contains($estado, 'armad') ? 'badge-primary' : 'badge-warning'));
                        $estadoFilter = str_contains($estado, 'entreg') ? 'entregado' : (str_contains($estado, 'camino') ? 'camino' : (str_contains($estado, 'armad') ? 'armado' : 'pendiente'));
                    @endphp
                    <div class="col-md-3 paquete-item" data-estado="{{ $estadoFilter }}">
                        <div class="card mb-3 shadow-sm bg-white paquete-card estado-{{ $estadoFilter }}">
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
                            <div class="card-footer bg-light d-flex justify-content-end" style="gap: .5rem;">
                                <a href="{{ route('logistica.crud.edit', ['seccion' => 'paquete', 'id' => $paquete->id_paquete]) }}" class="btn btn-warning btn-xs" title="Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('logistica.crud.destroy', ['seccion' => 'paquete', 'id' => $paquete->id_paquete]) }}" method="POST" onsubmit="return confirm('¿Eliminar este paquete?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-paquete-filter');
    const items = document.querySelectorAll('.paquete-item');
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
