@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <style>
        .seguimiento-grid .col-md-3 { display: flex; }
        .seguimiento-card {
            width: 100%;
            border-radius: 12px;
            border-top: 5px solid transparent;
            transition: all 0.25s ease;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }
        .seguimiento-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.18);
        }
        .seguimiento-card.estado-entregado { border-top-color: #28a745; }
        .seguimiento-card.estado-camino { border-top-color: #17a2b8; }
        .seguimiento-card.estado-otro { border-top-color: #ffc107; }
    </style>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span style="font-size: larger; font-weight: bolder;">Historial Seguimiento de Paquetes</span>
            <div class="d-flex align-items-center" style="gap: .5rem;">
                <span class="badge badge-info">{{ $seguimientos->count() }} registros</span>
                <a href="{{ route('logistica.crud.create', ['seccion' => 'seguimiento']) }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Registrar avance
                </a>
            </div>
        </div>
        <div class="card-body bg-white">
            <div class="row seguimiento-grid">
                @forelse($seguimientos as $item)
                    @php
                        $estado = strtolower((string) ($item->estado ?? ''));
                        $badgeClass = str_contains($estado, 'entreg') ? 'badge-success' : (str_contains($estado, 'camino') ? 'badge-info' : 'badge-warning');
                        $estadoClass = str_contains($estado, 'entreg') ? 'entregado' : (str_contains($estado, 'camino') ? 'camino' : 'otro');
                    @endphp
                    <div class="col-md-3">
                        <div class="card mb-3 shadow-sm bg-white seguimiento-card estado-{{ $estadoClass }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Paquete {{ $item->paquete_codigo ?? $item->codigo_seguimiento ?? $item->id_paquete }}</strong>
                                <span class="badge {{ $badgeClass }} text-uppercase">{{ $item->estado ?? '-' }}</span>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Actualización:</strong> {{ $item->fecha_actualizacion ?? '-' }}</p>
                                <p class="mb-1"><strong>Conductor:</strong> {{ $item->conductor_nombre ?? '-' }}</p>
                                <p class="mb-1"><strong>CI conductor:</strong> {{ $item->conductor_ci ?? '-' }}</p>
                                <p class="mb-0"><strong>Placa:</strong> {{ $item->vehiculo_placa ?? '-' }}</p>
                            </div>
                            <div class="card-footer bg-light d-flex justify-content-end" style="gap: .5rem;">
                                <a href="{{ route('logistica.crud.edit', ['seccion' => 'seguimiento', 'id' => $item->id_historial]) }}" class="btn btn-warning btn-xs" title="Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('logistica.crud.destroy', ['seccion' => 'seguimiento', 'id' => $item->id_historial]) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro de seguimiento?');">
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
                    <div class="col-12 text-muted">No hay seguimientos registrados.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
