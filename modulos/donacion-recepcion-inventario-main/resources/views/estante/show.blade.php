@extends('adminlte::page')

@section('title', 'Detalles del Estante')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Detalles del Estante</h1>
    </div>
    <div class="col-sm-6">
        <a class="btn btn-secondary float-right" href="{{ route('inventario.almacene.show', $estante->id_almacen) }}">
            <i class="fas fa-arrow-left"></i> Volver al Almacén
        </a>
    </div>
</div>
@stop

@section('content')
{{-- Info Boxes Row --}}
<div class="row">
    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-layer-group"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Código del Estante</span>
                <span class="info-box-number">{{ $estante->codigo_estante }}</span>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-warehouse"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Almacén</span>
                <span class="info-box-number" style="font-size: 1.2rem;">
                    @if($estante->almacene)
                        {{ $estante->almacene->nombre }}
                    @else
                        Sin asignar
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Main Content Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Información Completa</h3>
        <div class="card-tools">
            <a href="{{ route('inventario.estante.edit', $estante->id_estante) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Código del Estante:</dt>
            <dd class="col-sm-9"><strong>{{ $estante->codigo_estante }}</strong></dd>

            <dt class="col-sm-3">Almacén:</dt>
            <dd class="col-sm-9">
                @if($estante->almacene)
                    <span class="badge badge-info badge-lg">
                        {{ $estante->almacene->nombre }}
                    </span>
                    @if($estante->almacene->direccion)
                        <br><small class="text-muted">{{ $estante->almacene->direccion }}</small>
                    @endif
                @else
                    <span class="badge badge-secondary">Sin almacén asignado</span>
                @endif
            </dd>

            <dt class="col-sm-3">Descripción:</dt>
            <dd class="col-sm-9">
                {{ $estante->descripcion ?: 'Sin descripción' }}
            </dd>
        </dl>
    </div>
</div>

{{-- Espacios e Inventario Card --}}
<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-boxes"></i> Espacios e Inventario del Estante</h3>
        <div class="card-tools">
            <span class="badge badge-info">
                {{ $estante->espacios->count() }} espacios totales
            </span>
        </div>
    </div>
    <div class="card-body">
        @if($estante->espacios->count() > 0)
            @foreach($estante->espacios as $espacio)
                <div class="card mb-3">
                    <div class="card-header bg-light" style="cursor: pointer;" data-toggle="collapse"
                        data-target="#productos-{{ $espacio->id_espacio }}" aria-expanded="true">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-box"></i>
                                Espacio: <strong>{{ $espacio->codigo_espacio }}</strong>
                                @if($espacio->estado === 'lleno')
                                    <span class="badge badge-danger ml-2">LLENO</span>
                                @else
                                    <span class="badge badge-success ml-2">DISPONIBLE</span>
                                @endif

                                @if($espacio->productosAgrupados && count($espacio->productosAgrupados) > 0)
                                    <span class="badge badge-info ml-2">
                                        {{ array_sum(array_column($espacio->productosAgrupados, 'cantidad_total')) }} unidades
                                    </span>
                                @else
                                    <span class="badge badge-secondary ml-2">Vacío</span>
                                @endif
                            </h5>
                            <div onclick="event.stopPropagation();">
                                <form action="{{ route('inventario.espacio.toggleStatus', $espacio->id_espacio) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ $espacio->estado === 'lleno' ? 'btn-outline-success' : 'btn-outline-danger' }}"
                                        title="{{ $espacio->estado === 'lleno' ? 'Marcar como disponible' : 'Marcar como lleno' }}">
                                        <i class="fas {{ $espacio->estado === 'lleno' ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                        {{ $espacio->estado === 'lleno' ? 'Marcar Disponible' : 'Marcar Lleno' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="collapse show" id="productos-{{ $espacio->id_espacio }}">
                        @if($espacio->productosAgrupados && count($espacio->productosAgrupados) > 0)
                            <table class="table table-sm table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th><i class="fas fa-tag"></i> Producto</th>
                                        <th class="text-center"><i class="fas fa-boxes"></i> Cantidad</th>
                                        <th class="text-center"><i class="fas fa-ruler"></i> Unidad</th>
                                        <th><i class="fas fa-info-circle"></i> Descripción</th>
                                        <th class="text-center"><i class="fas fa-hand-holding-heart"></i> Donaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($espacio->productosAgrupados as $nombreProducto => $data)
                                        <tr>
                                            <td>
                                                <strong>{{ $nombreProducto }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-barcode"></i> ID: {{ $data['producto']->id_producto }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary badge-lg">
                                                    {{ $data['cantidad_total'] }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                {{ $data['unidad_medida'] ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <small>{{ $data['producto']->descripcion ?? 'Sin descripción' }}</small>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                    data-target="#donacionesModal{{ $espacio->id_espacio }}_{{ $data['producto']->id_producto }}">
                                                    <i class="fas fa-eye"></i> Ver Donaciones
                                                    <span class="badge badge-light">{{ count($data['donaciones']) }}</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- Modals for donation details --}}
                            @foreach($espacio->productosAgrupados as $nombreProducto => $data)
                                <div class="modal fade"
                                    id="donacionesModal{{ $espacio->id_espacio }}_{{ $data['producto']->id_producto }}" tabindex="-1"
                                    role="dialog">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-hand-holding-heart"></i>
                                                    Donaciones de: {{ $nombreProducto }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    <strong>Total acumulado:</strong> {{ $data['cantidad_total'] }}
                                                    {{ $data['unidad_medida'] ?? 'unidades' }}
                                                </div>
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th><i class="fas fa-hashtag"></i> Donación</th>
                                                            <th><i class="fas fa-user"></i> Donante</th>
                                                            <th class="text-center"><i class="fas fa-boxes"></i> Cantidad</th>
                                                            <th><i class="fas fa-calendar"></i> Fecha</th>
                                                            <th><i class="fas fa-comment"></i> Descripción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data['donaciones'] as $donacion)
                                                            <tr>
                                                                <td>
                                                                    @if($donacion['id_donacion'])
                                                                        <a href="{{ url('donaciones/' . $donacion['id_donacion']) }}"
                                                                            target="_blank">
                                                                            #{{ $donacion['id_donacion'] }}
                                                                            <i class="fas fa-external-link-alt"></i>
                                                                        </a>
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </td>
                                                                <td>{{ $donacion['donante'] }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-primary">{{ $donacion['cantidad'] }}</span>
                                                                </td>
                                                                <td>
                                                                    @if($donacion['fecha'])
                                                                        {{ \Carbon\Carbon::parse($donacion['fecha'])->format('d/m/Y') }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <small>{{ $donacion['descripcion'] ?? 'Sin descripción' }}</small>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    <i class="fas fa-times"></i> Cerrar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="callout callout-warning">
                <h5><i class="fas fa-info-circle"></i> Sin Espacios</h5>
                <p>Este estante no tiene espacios definidos todavía.</p>
            </div>
        @endif
    </div>
    @if($estante->espacios->count() > 0)
        <div class="card-footer">
            @php
                $totalUnidades = 0;
                $productosUnicos = [];
                foreach ($estante->espacios as $espacio) {
                    foreach ($espacio->ubicacionesDonaciones as $ubicacion) {
                        $totalUnidades += $ubicacion->cantidad_ubicada;
                        if ($ubicacion->detalle && $ubicacion->detalle->producto) {
                            $productosUnicos[$ubicacion->detalle->producto->id_producto] = $ubicacion->detalle->producto->nombre;
                        }
                    }
                }
            @endphp
            <div class="row">
                <div class="col-md-6">
                    <strong><i class="fas fa-box"></i> Total de productos únicos:</strong>
                    <span class="badge badge-info">{{ count($productosUnicos) }}</span>
                </div>
                <div class="col-md-6 text-right">
                    <strong><i class="fas fa-cubes"></i> Total de unidades almacenadas:</strong>
                    <span class="badge badge-success">{{ $totalUnidades }}</span>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Action Buttons --}}
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('inventario.almacene.show', $estante->id_almacen) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Almacén
        </a>
        <a href="{{ route('inventario.estante.edit', $estante->id_estante) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Editar Estante
        </a>
        <form action="{{ route('inventario.estante.destroy', $estante->id_estante) }}" method="POST" style="display: inline;"
            onsubmit="return confirm('¿Está seguro de eliminar este estante?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Eliminar
            </button>
        </form>
    </div>
</div>
@stop

@section('js')
{{-- Bootstrap collapse works natively with data-toggle="collapse" --}}
@stop



