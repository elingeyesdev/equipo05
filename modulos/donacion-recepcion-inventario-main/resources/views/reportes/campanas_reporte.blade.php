@extends('adminlte::page')

@section('title', 'Reporte de Campañas')

@section('content_header')
    <h1><i class="fas fa-bullhorn"></i> Reporte de Campañas</h1>
@stop

@section('content')
@include('inventario::partials.flash-messages')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <strong>Estado:</strong> {{ ucfirst($request->estado ?? 'Todas') }}
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
                    <div class="info-box bg-danger">
                        <span class="info-box-icon"><i class="fas fa-bullhorn"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Campañas</span>
                            <span class="info-box-number">{{ $totalCampanas }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Recaudado</span>
                            <span class="info-box-number">Bs. {{ number_format($montoTotalRecaudado, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de campañas -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Total Donaciones</th>
                            <th>Monto Recaudado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campanas as $campana)
                            @php
                                $now = \Carbon\Carbon::now();
                                $inicio = \Carbon\Carbon::parse($campana->fecha_inicio);
                                $fin = \Carbon\Carbon::parse($campana->fecha_fin);
                                
                                if ($now->between($inicio, $fin)) {
                                    $estado = 'Activa';
                                    $badgeClass = 'success';
                                } elseif ($now->lt($inicio)) {
                                    $estado = 'Próxima';
                                    $badgeClass = 'info';
                                } else {
                                    $estado = 'Finalizada';
                                    $badgeClass = 'secondary';
                                }
                            @endphp
                            <tr>
                                <td>{{ $campana->id_campana }}</td>
                                <td>{{ $campana->nombre }}</td>
                                <td>{{ $inicio->format('d/m/Y') }}</td>
                                <td>{{ $fin->format('d/m/Y') }}</td>
                                <td><span class="badge badge-{{ $badgeClass }}">{{ $estado }}</span></td>
                                <td>{{ $campana->donaciones->count() }}</td>
                                <td>
                                    Bs. {{ number_format(
                                        $campana->donaciones
                                            ->filter(fn($d) => $d->tipo === 'dinero' && $d->dinero)
                                            ->sum(fn($d) => $d->dinero->monto ?? 0),
                                        2
                                    ) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay campañas con los filtros seleccionados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop





