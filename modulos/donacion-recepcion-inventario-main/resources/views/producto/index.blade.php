@extends('adminlte::page')

@section('title', 'Catálogo de Productos')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Catálogo de Productos</h1>
        <small class="text-muted">Catálogo base para donaciones, lotes e inventario operativo</small>
    </div>
    <div class="col-sm-6">
        <a href="{{ route('inventario.producto.create') }}" class="btn btn-primary float-right">
            <i class="fas fa-plus"></i> Nuevo producto
        </a>
    </div>
</div>
@stop

@section('content')
@include('inventario::partials.flash-messages')

<div class="row">
    <div class="col-lg-2 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ $stats['activos'] ?? 0 }}</h3><p>Activos</p></div>
            <div class="icon"><i class="fas fa-check"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-6">
        <div class="small-box bg-secondary">
            <div class="inner"><h3>{{ $stats['inactivos'] ?? 0 }}</h3><p>Inactivos</p></div>
            <div class="icon"><i class="fas fa-ban"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-6">
        <div class="small-box bg-dark">
            <div class="inner"><h3>{{ $stats['restringidos'] ?? 0 }}</h3><p>Restringidos</p></div>
            <div class="icon"><i class="fas fa-lock"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-6">
        <div class="small-box bg-danger">
            <div class="inner"><h3>{{ $stats['alta_prioridad'] ?? 0 }}</h3><p>Alta prioridad</p></div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3>{{ $stats['requieren_vencimiento'] ?? 0 }}</h3><p>Con vencimiento</p></div>
            <div class="icon"><i class="fas fa-calendar-times"></i></div>
        </div>
    </div>
    <div class="col-lg-2 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ $stats['total'] ?? 0 }}</h3><p>Total catálogo</p></div>
            <div class="icon"><i class="fas fa-boxes"></i></div>
        </div>
    </div>
</div>

<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title">Listado de productos</h3></div>
    <div class="card-body">
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => [
                [
                    'id' => 'filtroCategoria',
                    'label' => 'Categoría',
                    'placeholder' => 'Todas',
                    'options' => $categoriasFiltro->all(),
                ],
                [
                    'id' => 'filtroPrioridad',
                    'label' => 'Prioridad',
                    'placeholder' => 'Todas',
                    'options' => ['Alta' => 'Alta', 'Media' => 'Media', 'Baja' => 'Baja'],
                ],
                [
                    'id' => 'filtroEstado',
                    'label' => 'Estado',
                    'placeholder' => 'Todos',
                    'options' => ['Activo' => 'Activo', 'Inactivo' => 'Inactivo', 'Restringido' => 'Restringido'],
                ],
                [
                    'id' => 'filtroVencimiento',
                    'label' => 'Vencimiento',
                    'placeholder' => 'Todos',
                    'options' => ['Sí' => 'Requiere vencimiento', 'No' => 'Sin vencimiento'],
                ],
                [
                    'id' => 'filtroRestringido',
                    'label' => 'Restringido',
                    'placeholder' => 'Todos',
                    'options' => ['Sí' => 'Sí', 'No' => 'No'],
                ],
            ],
            'sortOptions' => [
                'prioridad_asc' => 'Prioridad (alta primero)',
                'nombre_asc' => 'Nombre (A-Z)',
                'codigo_asc' => 'Código (A-Z)',
            ],
            'defaultSort' => 'prioridad_asc',
        ])

        <table id="productosTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Unidad</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                    <th>Stock mín.</th>
                    <th>Venc.</th>
                    <th>Restr.</th>
                    <th>Indicadores</th>
                    <th class="text-center" width="140">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                    <tr class="{{ $producto->estado === 'inactivo' ? 'table-secondary' : '' }}">
                        <td><code>{{ $producto->codigo }}</code></td>
                        <td>
                            <strong>{{ $producto->nombre }}</strong>
                            @if ($producto->informacionIncompleta())
                                <span class="badge badge-warning ml-1" title="Información incompleta">!</span>
                            @endif
                        </td>
                        <td>
                            @if ($producto->categoriaProducto)
                                <span class="badge badge-info">{{ $producto->categoriaProducto->nombre }}</span>
                            @else
                                <span class="badge badge-secondary">Sin categoría</span>
                            @endif
                        </td>
                        <td>{{ $producto->unidad_medida ?: '—' }}</td>
                        <td><span class="badge badge-{{ $producto->badgePrioridad() }}">{{ $producto->etiquetaPrioridad() }}</span></td>
                        <td><span class="badge badge-{{ $producto->badgeEstado() }}">{{ $producto->etiquetaEstado() }}</span></td>
                        <td>{{ $producto->stock_minimo ?? 0 }}</td>
                        <td>{{ $producto->requiere_vencimiento ? 'Sí' : 'No' }}</td>
                        <td>{{ $producto->producto_restringido ? 'Sí' : 'No' }}</td>
                        <td class="text-nowrap">
                            @if ($producto->prioridad === 'alta')
                                <span class="badge badge-danger" title="Alta prioridad"><i class="fas fa-bolt"></i></span>
                            @endif
                            @if ($producto->requiere_vencimiento)
                                <span class="badge badge-warning" title="Requiere vencimiento"><i class="fas fa-calendar-alt"></i></span>
                            @endif
                            @if ($producto->producto_restringido)
                                <span class="badge badge-dark" title="Restringido"><i class="fas fa-lock"></i></span>
                            @endif
                            @if ($producto->estado === 'inactivo')
                                <span class="badge badge-secondary" title="Inactivo"><i class="fas fa-ban"></i></span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a class="btn btn-info" href="{{ route('inventario.producto.show', $producto->id_producto) }}" title="Ver"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-warning" href="{{ route('inventario.producto.edit', $producto->id_producto) }}" title="Editar"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('inventario.producto.destroy', $producto->id_producto) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('¿Eliminar este producto del catálogo?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
@include('inventario::partials.datatables-inventario-init')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function () {
        initInventarioListTable({
            selector: '#productosTable',
            defaultOrder: [[4, 'asc'], [1, 'asc']],
            sortSelect: '#ordenarPor',
            sortMap: {
                prioridad_asc: [[4, 'asc'], [1, 'asc']],
                nombre_asc: [1, 'asc'],
                codigo_asc: [0, 'asc'],
            },
            filters: [
                { select: '#filtroCategoria', column: 2 },
                { select: '#filtroPrioridad', column: 4 },
                { select: '#filtroEstado', column: 5 },
                { select: '#filtroVencimiento', column: 7 },
                { select: '#filtroRestringido', column: 8 },
            ],
        });
    });
</script>
@stop
