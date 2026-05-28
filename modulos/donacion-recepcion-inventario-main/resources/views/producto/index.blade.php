@extends('adminlte::page')

@section('title', 'Inventario de Productos')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Inventario de Productos</h1>
    </div>
    <div class="col-sm-6">
        <a href="{{ route('inventario.producto.create') }}" class="btn btn-primary float-right">
            Nuevo Producto
        </a>
    </div>
</div>
@stop

@section('content')
@include('inventario::partials.flash-messages')
{{-- Statistics Row --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $productos->total() }}</h3>
                <p>Total de Productos</p>
            </div>
            <div class="icon">
                <i class="fas fa-boxes"></i>
            </div>
        </div>
    </div>
</div>

{{-- Alert Messages --}}
@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Main Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Listado de Productos</h3>
    </div>
    <div class="card-body">
        @php
            $categoriasFiltro = collect();
            foreach ($productos as $productoFiltro) {
                $nombreCat = $productoFiltro->categoriasProducto?->nombre;
                if ($nombreCat) {
                    $categoriasFiltro->put($nombreCat, $nombreCat);
                }
            }
            $categoriasFiltro = $categoriasFiltro->sortKeys();
        @endphp
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => $categoriasFiltro->isNotEmpty() ? [[
                'id' => 'filtroCategoria',
                'label' => 'Filtrar por categoría',
                'options' => $categoriasFiltro->all(),
            ]] : [],
            'sortOptions' => [
                'nombre_asc' => 'Nombre (A-Z)',
                'nombre_desc' => 'Nombre (Z-A)',
                'categoria_asc' => 'Categoría (A-Z)',
                'categoria_desc' => 'Categoría (Z-A)',
            ],
            'defaultSort' => 'nombre_asc',
        ])

        <table id="productosTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Categora</th>
                    <th>Nombre</th>
                    <th>Descripcin</th>
                    <th>Unidad de Medida</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $producto)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td>
                            @if($producto->categoriasProducto)
                                <span class="badge badge-info">
                                    {{ $producto->categoriasProducto->nombre }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Sin categora</span>
                            @endif
                        </td>
                        <td><strong>{{ $producto->nombre }}</strong></td>
                        <td>{{ $producto->descripcion }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $producto->unidad_medida }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="{{ route('inventario.producto.show', $producto->id_producto) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm"
                                    href="{{ route('inventario.producto.edit', $producto->id_producto) }}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventario.producto.destroy', $producto->id_producto) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('¿Está seguro de eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <small class="text-muted">Usa los controles de la tabla para navegar entre páginas</small>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<style>
    .small-box {
        border-radius: 0.25rem;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
    }

    .btn-group .btn {
        margin: 0 2px;
    }
</style>
@stop

@section('js')
@include('inventario::partials.datatables-inventario-init')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function () {
        initInventarioListTable({
            selector: '#productosTable',
            defaultOrder: [[2, 'asc']],
            filters: [
                @if ($categoriasFiltro->isNotEmpty())
                { select: '#filtroCategoria', column: 1 },
                @endif
            ],
            sortSelect: '#ordenarPor',
            sortMap: {
                nombre_asc: [2, 'asc'],
                nombre_desc: [2, 'desc'],
                categoria_asc: [1, 'asc'],
                categoria_desc: [1, 'desc'],
            },
        });
    });
</script>
@stop




