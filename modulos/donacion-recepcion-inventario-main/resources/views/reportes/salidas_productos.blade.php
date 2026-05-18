@extends('adminlte::page')

@section('title', 'Reporte de Salidas')

@section('content_header')
    <h1><i class="fas fa-arrow-right"></i> Reporte de Salidas de Productos</h1>
@stop

@section('content')
@include('inventario::partials.flash-messages')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                @if($request->fecha_inicio && $request->fecha_fin)
                    <strong>Período:</strong> {{ $request->fecha_inicio }} al {{ $request->fecha_fin }}
                @else
                    <strong>Todas las salidas</strong>
                @endif
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
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-list"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Salidas</span>
                            <span class="info-box-number">{{ $totalSalidas }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box bg-warning">
                        <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Cantidad Total</span>
                            <span class="info-box-number">{{ $cantidadTotal }} unidades</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de salidas -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Fecha Salida</th>
                            <th>Destino</th>
                            <th>Paquete</th>
                            <th>Productos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salidasDetalladas as $salida)
                            <tr>
                                <td>{{ $salida['id_salida'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($salida['fecha_salida'])->format('d/m/Y H:i') }}</td>
                                <td>{{ $salida['destino'] ?? '-' }}</td>
                                <td><code>{{ $salida['paquete_codigo'] }}</code></td>
                                <td>
                                    @foreach($salida['productos'] as $producto)
                                        <div class="mb-1">
                                            <i class="fas fa-box"></i> {{ $producto['nombre'] }}
                                            <span class="badge badge-primary">{{ $producto['cantidad'] }}</span>
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay salidas registradas en este período</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop





