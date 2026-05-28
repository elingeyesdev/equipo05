@extends('adminlte::page')

@php
    $rolesFiltro = collect();
    foreach ($usuarios as $usuarioFiltro) {
        $rol = $usuarioFiltro->primary_role_name ?? 'Sin rol';
        $rolesFiltro->put($rol, $rol);
    }
    $rolesFiltro = $rolesFiltro->sortKeys();
@endphp

@section('template_title')
    Usuarios
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between; align-items: center;">

                            <span id="card_title">
                                {{ __('Usuarios') }}
                            </span>

                            <div class="float-right">
                                <a href="{{ route('inventario.usuario.create') }}" class="btn btn-primary btn-sm float-right"
                                    data-placement="left">
                                    {{ __('Nuevo Usuario') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-4">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body bg-white">
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
                        <div class="table-responsive">
                            <table id="usuariosTable" class="table table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>No</th>
                                        <th>Nombres</th>
                                        <th>Apellidos</th>
                                        <th>CI</th>
                                        <th>Licencia Conducir</th>
                                        <th>Género</th>
                                        <th>Correo</th>
                                        <th>Teléfono</th>
                                        <th>Dirección</th>
                                        <th>Estado</th>
                                        <th>Rol</th>
                                        <th>Fecha Registro</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($usuarios as $usuario)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $usuario->nombres }}</td>
                                            <td>{{ $usuario->apellidos }}</td>
                                            <td>{{ $usuario->ci }}</td>
                                            <td>{{ $usuario->licencia_conducir }}</td>
                                            <td>{{ $usuario->genero }}</td>
                                            <td>{{ $usuario->correo }}</td>
                                            <td>{{ $usuario->telefono }}</td>
                                            <td>{{ $usuario->direccion_domicilio }}</td>
                                            <td>
                                                @if($usuario->estado == 'Activo')
                                                    <span class="badge badge-success">Activo</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>{{ $usuario->primary_role_name ?? 'Sin rol' }}</td>
                                            <td data-order="{{ $usuario->fecha_registro ? \Carbon\Carbon::parse($usuario->fecha_registro)->format('Y-m-d') : '' }}">
                                                {{ $usuario->fecha_registro ? \Carbon\Carbon::parse($usuario->fecha_registro)->format('d/m/Y') : '-' }}
                                            </td>

                                            <td>
                                                <form action="{{ route('inventario.usuario.destroy', $usuario->id_usuario) }}"
                                                    method="POST" style="display: inline-block;">
                                                    <a class="btn btn-sm btn-info"
                                                        href="{{ route('inventario.usuario.show', $usuario->id_usuario) }}" title="Ver">
                                                        <i class="fa fa-fw fa-eye"></i>
                                                    </a>
                                                    <a class="btn btn-sm btn-success"
                                                        href="{{ route('inventario.usuario.edit', $usuario->id_usuario) }}" title="Editar">
                                                        <i class="fa fa-fw fa-edit"></i>
                                                    </a>
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar"
                                                        onclick="event.preventDefault(); confirm('¿Está seguro de eliminar?') ? this.closest('form').submit() : false;">
                                                        <i class="fa fa-fw fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <small class="text-muted">Usa los controles de la tabla para navegar entre páginas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@endsection

@section('js')
    @include('inventario::partials.datatables-inventario-init')
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(function () {
            const filters = [
                {
                    select: '#filtroEstado',
                    column: 9,
                    valueMap: {
                        Activo: 'Activo',
                        Inactivo: 'Inactivo',
                    },
                },
                {
                    select: '#filtroGenero',
                    column: 5,
                },
            ];

            @if ($rolesFiltro->isNotEmpty())
            filters.push({
                select: '#filtroRol',
                column: 10,
            });
            @endif

            initInventarioListTable({
                selector: '#usuariosTable',
                defaultOrder: [[11, 'desc']],
                filters: filters,
                sortSelect: '#ordenarPor',
                sortMap: {
                    fecha_desc: [11, 'desc'],
                    fecha_asc: [11, 'asc'],
                    nombre_asc: [1, 'asc'],
                    nombre_desc: [1, 'desc'],
                },
            });
        });
    </script>
@endsection




