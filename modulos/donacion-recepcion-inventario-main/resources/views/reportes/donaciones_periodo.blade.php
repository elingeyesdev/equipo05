@extends('adminlte::page')

@section('title', 'Reporte de Donaciones')

@section('content_header')
    <h1><i class="fas fa-donate"></i> Reporte de Donaciones por Período</h1>
@stop

@section('content')
@include('inventario::partials.flash-messages')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <strong>Período:</strong> {{ $request->fecha_inicio }} al {{ $request->fecha_fin }}
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
                <div class="col-md-4">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-list"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Donaciones</span>
                            <span class="info-box-number">{{ $totalDonaciones }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Monto Total</span>
                            <span class="info-box-number">Bs. {{ number_format($totalMonto, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-chart-pie"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tipos de Donación</span>
                            <span class="info-box-number">{{ $donacionesPorTipo->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de donaciones -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Donante</th>
                            <th>Campaña</th>
                            <th>Tipo</th>
                            <th>Monto / Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donaciones as $donacion)
                            <tr>
                                <td>{{ $donacion->id_donacion }}</td>
                                <td>{{ \Carbon\Carbon::parse($donacion->fecha)->format('d/m/Y H:i') }}</td>
                                <td>{{ $donacion->donante->nombre ?? 'N/A' }}</td>
                                <td>{{ $donacion->campana->nombre ?? 'General' }}</td>
                                <td>
                                    <span class="badge badge-{{ $donacion->tipo == 'especie' ? 'primary' : ($donacion->tipo == 'dinero' ? 'success' : 'info') }}">
                                        {{ ucfirst($donacion->tipo) }}
                                    </span>
                                </td>
                                <td>
                                    @if($donacion->tipo === 'dinero' && $donacion->dinero)
                                        Bs. {{ number_format($donacion->dinero->monto, 2) }}
                                    @else
                                        {{ $donacion->detalles->count() }} items
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay donaciones en este período</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop





