@extends('adminlte::page')

@section('title', 'Puntos de Recolección')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Puntos de Recolección</h1>
    </div>
    <div class="col-sm-6">
        <a href="{{ route('inventario.puntos-recoleccion.create') }}" class="btn btn-primary float-right">
            <i class="fas fa-map-marker-alt"></i> Nuevo Punto
        </a>
    </div>
</div>
@stop

@section('content')
{{-- Statistics Row --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $puntosRecoleccions->total() }}</h3>
                <p>Puntos Activos</p>
            </div>
            <div class="icon">
                <i class="fas fa-map-marked-alt"></i>
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
        <h3 class="card-title">Listado de Puntos de Recolección</h3>
    </div>
    <div class="card-body">
        <table id="puntosTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Contacto</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($puntosRecoleccions as $punto)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td><strong>{{ $punto->nombre }}</strong></td>
                        <td>
                            {{ $punto->direccion }}
                        </td>
                        <td>
                            @if($punto->contacto)
                                {{ $punto->contacto }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm"
                                    href="{{ route('inventario.puntos-recoleccion.show', $punto->id_punto) }}" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm"
                                    href="{{ route('inventario.puntos-recoleccion.edit', $punto->id_punto) }}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventario.puntos-recoleccion.destroy', $punto->id_punto) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('¿Está seguro de eliminar este punto de recolección?');">
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
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#puntosTable').DataTable({
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
                "emptyTable": "No hay puntos de recolección registrados",
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
@stop




