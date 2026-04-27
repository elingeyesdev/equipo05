@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span style="font-size: larger; font-weight: bolder;">Historial Seguimiento de Paquetes</span>
            <span class="badge badge-info">{{ $seguimientos->count() }} registros</span>
        </div>
        <div class="card-body bg-white">
            <div class="row">
                @forelse($seguimientos as $item)
                    @php
                        $estado = strtolower((string) ($item->estado ?? ''));
                        $badgeClass = str_contains($estado, 'entreg') ? 'badge-success' : (str_contains($estado, 'camino') ? 'badge-info' : 'badge-warning');
                    @endphp
                    <div class="col-md-3 d-flex">
                        <div class="card mb-3 shadow-sm bg-white w-100">
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
