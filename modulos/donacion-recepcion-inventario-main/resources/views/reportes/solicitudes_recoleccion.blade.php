@extends('adminlte::page')

@section('title', 'Reporte de Solicitudes')

@section('content_header')
    <h1><i class="fas fa-truck"></i> Reporte de Solicitudes de Recolección</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <strong>Filtros aplicados:</strong>
                @if($request->estado) Estado: {{ $request->estado }} @endif
                @if($request->fecha_inicio) | Desde: {{ $request->fecha_inicio }} @endif
                @if($request->fecha_fin) Hasta: {{ $request->fecha_fin }} @endif
            </h3>
            <div class="card-tools">
                <a href="{{ route('inventario.reportes.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Resumen -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-list"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Solicitudes</span>
                            <span class="info-box-number">{{ $totalSolicitudes }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h4>Por Estado</h4>
                            @foreach($solicitudesPorEstado as $estado => $cantidad)
                                <p class="mb-0">{{ $estado }}: <strong>{{ $cantidad }}</strong></p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de solicitudes -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Donante</th>
                            <th>Dirección</th>
                            <th>Fecha Programada</th>
                            <th>Estado</th>
                            <th>Recolector</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            <tr>
                                <td>{{ $solicitud->id_solicitud }}</td>
                                <td>{{ $solicitud->donante->nombre ?? 'N/A' }}</td>
                                <td>{{ $solicitud->direccion_recoleccion }}</td>
                                <td>{{ \Carbon\Carbon::parse($solicitud->fecha_programada)->format('d/m/Y H:i') }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($solicitud->estado) {
                                            'Pendiente' => 'warning',
                                            'En proceso' => 'info',
                                            'Completada' => 'success',
                                            'Cancelada' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-{{ $badgeClass }}">{{ $solicitud->estado }}</span>
                                </td>
                                <td>
                                    @if($solicitud->usuario)
                                        {{ $solicitud->usuario->nombres }} {{ $solicitud->usuario->apellidos }}
                                    @else
                                        Sin asignar
                                    @endif
                                </td>
                                <td>{{ $solicitud->observaciones ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay solicitudes con los filtros seleccionados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop





