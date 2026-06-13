@extends('adminlte::page')

@section('title', 'Almacenes')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1><i class="fas fa-warehouse"></i> Gestión de Almacenes</h1>
    </div>
    <div class="col-sm-6">
        @can('gestionar-almacen')
            <a href="{{ route('inventario.almacene.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Nuevo Almacén
            </a>
        @endcan
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
                <h3>{{ $almacenes->count() }}</h3>
                <p>Total de Almacenes</p>
            </div>
            <div class="icon">
                <i class="fas fa-warehouse"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $almacenes->where('latitud', '!=', null)->count() }}</h3>
                <p>Con Ubicación GPS</p>
            </div>
            <div class="icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
        </div>
    </div>
</div>

{{-- Alert Messages --}}
@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

{{-- Main Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Listado de Almacenes</h3>
    </div>
    <div class="card-body">
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => [[
                'id' => 'filtroUbicacion',
                'label' => 'Ubicación GPS',
                'options' => [
                    'registrada' => 'Con ubicación',
                    'sin' => 'Sin ubicación',
                ],
            ]],
            'sortOptions' => [
                'nombre_asc' => 'Nombre (A-Z)',
                'nombre_desc' => 'Nombre (Z-A)',
            ],
            'defaultSort' => 'nombre_asc',
        ])

        <table id="almacenesTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th><i class="fas fa-tag"></i> Nombre</th>
                    <th><i class="fas fa-map-marker-alt"></i> Dirección</th>
                    <th><i class="fas fa-globe"></i> Ubicación GPS</th>
                    <th width="200px" class="text-center"><i class="fas fa-cogs"></i> Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($almacenes as $almacene)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td>
                            <strong>{{ $almacene->nombre }}</strong>
                        </td>
                        <td>{{ $almacene->direccion }}</td>
                        <td class="text-center">
                            @if($almacene->latitud && $almacene->longitud)
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> Registrada
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ number_format($almacene->latitud, 4) }}, {{ number_format($almacene->longitud, 4) }}
                                </small>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-times"></i> Sin ubicación
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group mb-2" role="group">
                                <a class="btn btn-success btn-sm"
                                    href="{{ route('inventario.estante.create', ['id_almacen' => $almacene->id_almacen]) }}"
                                    title="Crear Estante">
                                    <i class="fas fa-plus"></i> Crear Estante
                                </a>
                            </div>
                            <br>
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="{{ route('inventario.almacene.show', $almacene->id_almacen) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('gestionar-almacen')
                                    <a class="btn btn-warning btn-sm" href="{{ route('inventario.almacene.edit', $almacene->id_almacen) }}"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-form-{{ $almacene->id_almacen }}"
                                        action="{{ route('inventario.almacene.destroy', $almacene->id_almacen) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                            onclick="confirmDelete('{{ $almacene->id_almacen }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
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

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este almacén? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
            </div>
        </div>
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
    $(document).ready(function () {
        initInventarioListTable({
            selector: '#almacenesTable',
            defaultOrder: [[1, 'asc']],
            filters: [{
                select: '#filtroUbicacion',
                column: 3,
                valueMap: {
                    registrada: 'Registrada',
                    sin: 'Sin ubicación',
                },
            }],
            sortSelect: '#ordenarPor',
            sortMap: {
                nombre_asc: [1, 'asc'],
                nombre_desc: [1, 'desc'],
            },
        });
    });

    let deleteId;

    function confirmDelete(id) {
        deleteId = id;
        $('#deleteModal').modal('show');
    }

    $('#confirmDeleteBtn').click(function () {
        if (deleteId) {
            $('#delete-form-' + deleteId).submit();
        }
    });
</script>
@stop




