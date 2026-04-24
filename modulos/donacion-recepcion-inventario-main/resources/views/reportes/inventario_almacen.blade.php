@extends('adminlte::page')

@section('title', 'Reporte de Inventario')

@section('content_header')
    <h1><i class="fas fa-warehouse"></i> Reporte de Inventario por Almacén</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <strong>Almacén:</strong> {{ $almacenId ? \Modules\Inventario\Models\Almacene::find($almacenId)->nombre : 'Todos' }}
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
                    <div class="info-box bg-primary">
                        <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Productos</span>
                            <span class="info-box-number">{{ $totalProductos }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-success">
                        <span class="info-box-icon"><i class="fas fa-cubes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Cantidad Total</span>
                            <span class="info-box-number">{{ $cantidadTotal }} unidades</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box bg-info">
                        <span class="info-box-icon"><i class="fas fa-warehouse"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Almacenes</span>
                            <span class="info-box-number">{{ $almacenes->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de productos -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Cantidad Total</th>
                            <th>Ubicaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productosAgrupados as $producto)
                            <tr>
                                <td>{{ $producto->id_producto }}</td>
                                <td>{{ $producto->nombre_producto }}</td>
                                <td>{{ $producto->categoria }}</td>
                                <td><strong>{{ $producto->cantidad_total }}</strong></td>
                                <td>
                                    @foreach($producto->ubicaciones as $ubicacion)
                                        <small class="d-block">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ $ubicacion['almacen'] }} / 
                                            {{ $ubicacion['estante'] }} / 
                                            {{ $ubicacion['espacio'] }}
                                            <span class="badge badge-info">{{ $ubicacion['cantidad'] }}</span>
                                        </small>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay productos en inventario</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop




