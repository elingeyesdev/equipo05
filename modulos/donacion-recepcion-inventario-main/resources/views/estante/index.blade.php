@extends('adminlte::page')

@section('title', 'Estantes')

@section('content_header')
@include('inventario::partials.page-toolbar', [
    'title' => 'Gestión de Estantes',
    'createRoute' => route('inventario.estante.create'),
    'createLabel' => 'Nuevo Estante',
])
@stop

@section('content')
@include('inventario::partials.flash-messages')
{{-- Statistics Row --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $estantes->count() }}</h3>
                <p>Total de Estantes</p>
            </div>
            <div class="icon">
                <i class="fas fa-layer-group"></i>
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
        <h3 class="card-title">Listado de Estantes</h3>
    </div>
    <div class="card-body">
        @php
            $almacenesFiltro = collect();
            foreach ($estantes as $estanteFiltro) {
                $nombreAlm = $estanteFiltro->almacene?->nombre;
                if ($nombreAlm) {
                    $almacenesFiltro->put($nombreAlm, $nombreAlm);
                }
            }
            $almacenesFiltro = $almacenesFiltro->sortKeys();
        @endphp
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => $almacenesFiltro->isNotEmpty() ? [[
                'id' => 'filtroAlmacen',
                'label' => 'Filtrar por almacén',
                'options' => $almacenesFiltro->all(),
            ]] : [],
            'sortOptions' => [
                'codigo_asc' => 'Código estante (A-Z)',
                'codigo_desc' => 'Código estante (Z-A)',
                'almacen_asc' => 'Almacén (A-Z)',
                'almacen_desc' => 'Almacén (Z-A)',
            ],
            'defaultSort' => 'codigo_asc',
        ])

        <table id="estantesTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Almacén</th>
                    <th>Código Estante</th>
                    <th>Descripción</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estantes as $estante)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td>
                            @if($estante->almacene)
                                <span class="badge badge-info">
                                    {{ $estante->almacene->nombre }}
                                </span>
                            @else
                                <span class="badge badge-secondary">Sin almacn</span>
                            @endif
                        </td>
                        <td><strong>{{ $estante->codigo_estante }}</strong></td>
                        <td>{{ $estante->descripcion }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="{{ route('inventario.estante.show', $estante->id_estante) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm" href="{{ route('inventario.estante.edit', $estante->id_estante) }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventario.estante.destroy', $estante->id_estante) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('¿Está seguro de eliminar este estante?');">
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
            selector: '#estantesTable',
            defaultOrder: [[2, 'asc']],
            filters: [
                @if ($almacenesFiltro->isNotEmpty())
                { select: '#filtroAlmacen', column: 1 },
                @endif
            ],
            sortSelect: '#ordenarPor',
            sortMap: {
                codigo_asc: [2, 'asc'],
                codigo_desc: [2, 'desc'],
                almacen_asc: [1, 'asc'],
                almacen_desc: [1, 'desc'],
            },
        });
    });
</script>
@stop




