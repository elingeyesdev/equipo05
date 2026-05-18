@extends('adminlte::page')

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
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
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
                                            <td>{{ $usuario->fecha_registro ? \Carbon\Carbon::parse($usuario->fecha_registro)->format('d/m/Y') : '-' }}
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
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function () {
            $('table').DataTable({
                "paging": true,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "language": {
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron resultados",
                    "emptyTable": "No hay usuarios registrados",
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)",
                    "paginate": {
                        "first": "Primero",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });
        });
    </script>
@endsection




