@extends('adminlte::page')

@section('title', 'Espacios')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Gesti�n de Espacios</h1>
    </div>
    <div class="col-sm-6">
        <a href="{{ route('inventario.espacio.create') }}" class="btn btn-primary float-right">
            Nuevo Espacio
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
                <h3>{{ $espacios->total() }}</h3>
                <p>Total de Espacios</p>
            </div>
            <div class="icon">
                <i class="fas fa-th"></i>
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
        <h3 class="card-title">Listado de Espacios</h3>
    </div>
    <div class="card-body">
        <table id="espaciosTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Estante</th>
                    <th>C�digo Espacio</th>
                    <th class="text-center">Estado</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($espacios as $espacio)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td>
                            @if($espacio->estante)
                                <span class="badge badge-info">
                                    {{ $espacio->estante->codigo_estante }}
                                </span>
                                @if($espacio->estante->almacene)
                                    <br><small class="text-muted">{{ $espacio->estante->almacene->nombre }}</small>
                                @endif
                            @else
                                <span class="badge badge-secondary">Sin estante</span>
                            @endif
                        </td>
                        <td><strong>{{ $espacio->codigo_espacio }}</strong></td>
                        <td class="text-center">
                            @if($espacio->estado === 'lleno')
                                <span class="badge badge-danger">LLENO</span>
                            @else
                                <span class="badge badge-success">DISPONIBLE</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="{{ route('inventario.espacio.show', $espacio->id_espacio) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm" href="{{ route('inventario.espacio.edit', $espacio->id_espacio) }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventario.espacio.destroy', $espacio->id_espacio) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('�Est� seguro de eliminar este espacio?');">
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
        <small class="text-muted">Usa los controles de la tabla para navegar entre p�ginas</small>
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
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#espaciosTable').DataTable({
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
                "emptyTable": "No hay datos disponibles en la tabla",
                "lengthMenu": "Mostrar _MENU_ registros por p�gina",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "paginate": {
                    "first": "Primero",
                    "last": "�ltimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });
    });
</script>
@stop




