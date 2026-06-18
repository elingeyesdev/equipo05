@extends('adminlte::page')

@php
    $rolesFiltro = collect();
    foreach ($usuarios as $usuarioFiltro) {
        $rol = $usuarioFiltro->primary_role_name ?? 'Sin rol';
        $rolesFiltro->put($rol, $rol);
    }
    $rolesFiltro = $rolesFiltro->sortKeys();
@endphp

@section('title', 'Usuarios — Inventario')

@section('content_header')
@include('inventario::partials.page-toolbar', [
    'title' => 'Usuarios del inventario',
    'createRoute' => route('inventario.usuario.create'),
    'createLabel' => 'Nuevo Usuario',
])
@stop

@section('content')
@include('inventario::partials.flash-messages')

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ $message }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="fas fa-users mr-1"></i> Personal operativo</h3>
    </div>
    <div class="card-body p-0 p-md-3">
        <div class="px-3 pt-3 pb-0">
            @include('inventario::partials.datatables-list-toolbar', [
                'filters' => array_values(array_filter([
                    [
                        'id' => 'filtroEstado',
                        'label' => 'Estado',
                        'options' => [
                            'Activo' => 'Activo',
                            'Inactivo' => 'Inactivo',
                        ],
                    ],
                    $rolesFiltro->isNotEmpty() ? [
                        'id' => 'filtroRol',
                        'label' => 'Rol',
                        'options' => $rolesFiltro->all(),
                    ] : null,
                    [
                        'id' => 'filtroGenero',
                        'label' => 'Género',
                        'options' => [
                            'Masculino' => 'Masculino',
                            'Femenino' => 'Femenino',
                        ],
                    ],
                ])),
                'sortOptions' => [
                    'fecha_desc' => 'Fecha registro (más reciente)',
                    'fecha_asc' => 'Fecha registro (más antigua)',
                    'nombre_asc' => 'Nombres (A-Z)',
                    'nombre_desc' => 'Nombres (Z-A)',
                ],
                'defaultSort' => 'fecha_desc',
            ])
        </div>

        <div class="table-responsive inventario-table-wrap px-3 pb-3">
            <table id="usuariosTable" class="table table-bordered table-striped table-hover w-100 inventario-usuarios-table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="col-num">#</th>
                        <th class="col-usuario">Usuario</th>
                        <th class="col-ci">CI</th>
                        <th class="col-contacto">Contacto</th>
                        <th class="col-rol">Rol</th>
                        <th class="col-estado">Estado</th>
                        <th class="col-fecha">Registro</th>
                        <th class="col-acciones text-center">Acciones</th>
                        {{-- Columnas ocultas para filtros DataTables --}}
                        <th>Género</th>
                        <th>Licencia</th>
                        <th>Dirección</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr>
                            <td class="text-center align-middle"><strong>{{ ++$i }}</strong></td>
                            <td class="align-middle">
                                <div class="cell-usuario">
                                    {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                </div>
                                @if($usuario->is_recolector)
                                    <span class="badge badge-info mt-1"><i class="fas fa-truck-loading"></i> Recolector</span>
                                @endif
                            </td>
                            <td class="align-middle text-nowrap">{{ $usuario->ci }}</td>
                            <td class="align-middle cell-contacto">
                                @if($usuario->correo)
                                    <a href="mailto:{{ $usuario->correo }}" class="d-block text-truncate cell-email" title="{{ $usuario->correo }}">
                                        <i class="fas fa-envelope text-muted mr-1"></i>{{ $usuario->correo }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                                @if($usuario->telefono)
                                    <small class="d-block text-muted mt-1">
                                        <i class="fas fa-phone mr-1"></i>{{ $usuario->telefono }}
                                    </small>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border text-dark">{{ $usuario->primary_role_name ?? 'Sin rol' }}</span>
                            </td>
                            <td class="align-middle text-center">
                                @if($usuario->estado == 'Activo')
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="align-middle text-nowrap" data-order="{{ $usuario->fecha_registro ? \Carbon\Carbon::parse($usuario->fecha_registro)->format('Y-m-d') : '' }}">
                                {{ $usuario->fecha_registro ? \Carbon\Carbon::parse($usuario->fecha_registro)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="align-middle text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn-info" href="{{ route('inventario.usuario.show', $usuario->id_usuario) }}" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a class="btn btn-warning" href="{{ route('inventario.usuario.edit', $usuario->id_usuario) }}" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('inventario.usuario.destroy', $usuario->id_usuario) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Está seguro de eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td>{{ $usuario->genero ?? '' }}</td>
                            <td>{{ $usuario->licencia_conducir ?? '' }}</td>
                            <td>{{ $usuario->direccion_domicilio ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        <small class="text-muted">Licencia, género y dirección completos se muestran en la ficha de cada usuario.</small>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap4.min.css">
<style>
    .inventario-table-wrap {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .inventario-usuarios-table {
        table-layout: fixed;
        width: 100% !important;
    }

    .inventario-usuarios-table th,
    .inventario-usuarios-table td {
        vertical-align: middle !important;
        word-wrap: break-word;
        overflow-wrap: anywhere;
    }

    .inventario-usuarios-table .col-num { width: 3rem; }
    .inventario-usuarios-table .col-usuario { width: 16%; min-width: 8rem; }
    .inventario-usuarios-table .col-ci { width: 7rem; }
    .inventario-usuarios-table .col-contacto { width: 22%; min-width: 9rem; }
    .inventario-usuarios-table .col-rol { width: 11%; min-width: 6.5rem; }
    .inventario-usuarios-table .col-estado { width: 5.5rem; }
    .inventario-usuarios-table .col-fecha { width: 6.5rem; }
    .inventario-usuarios-table .col-acciones { width: 7.5rem; }

    .inventario-usuarios-table .cell-usuario {
        font-weight: 600;
        line-height: 1.3;
    }

    .inventario-usuarios-table .cell-contacto .cell-email {
        max-width: 100%;
        font-size: 0.8125rem;
        color: inherit;
    }

    .inventario-usuarios-table .cell-contacto small {
        font-size: 0.75rem;
    }

    .inventario-usuarios-table .btn-group .btn {
        padding: 0.25rem 0.45rem;
    }

    /* DataTables: evitar que el buscador empuje el ancho */
    .dataTables_wrapper .row:first-child,
    .dataTables_wrapper .row:last-child {
        margin-left: 0;
        margin-right: 0;
    }

    .dataTables_wrapper .dataTables_filter input {
        max-width: 100%;
    }

    @media (max-width: 767.98px) {
        .inventario-usuarios-table .col-usuario,
        .inventario-usuarios-table .col-contacto {
            min-width: 7rem;
        }

        .inventario-dt-toolbar .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }

    table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before {
        background-color: #007bff;
    }
</style>
@endsection

@section('js')
@include('inventario::partials.datatables-inventario-init')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap4.min.js"></script>
<script>
    $(function () {
        initInventarioListTable({
            selector: '#usuariosTable',
            defaultOrder: [[6, 'desc']],
            responsive: {
                details: {
                    type: 'column',
                    target: 0
                }
            },
            columnDefs: [
                { targets: [8, 9, 10], visible: false, searchable: true },
                { targets: 0, responsivePriority: 1 },
                { targets: 1, responsivePriority: 2 },
                { targets: 7, responsivePriority: 1, orderable: false },
                { targets: 4, responsivePriority: 3 },
                { targets: 5, responsivePriority: 4 },
            ],
            filters: [
                {
                    select: '#filtroEstado',
                    column: 5,
                    valueMap: {
                        Activo: 'Activo',
                        Inactivo: 'Inactivo',
                    },
                },
                {
                    select: '#filtroGenero',
                    column: 8,
                },
                @if ($rolesFiltro->isNotEmpty())
                {
                    select: '#filtroRol',
                    column: 4,
                },
                @endif
            ],
            sortSelect: '#ordenarPor',
            sortMap: {
                fecha_desc: [6, 'desc'],
                fecha_asc: [6, 'asc'],
                nombre_asc: [1, 'asc'],
                nombre_desc: [1, 'desc'],
            },
        });
    });
</script>
@endsection
