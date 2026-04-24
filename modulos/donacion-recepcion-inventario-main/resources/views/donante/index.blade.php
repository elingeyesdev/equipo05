@extends('adminlte::page')

@section('title', 'Gestión de Donantes')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Gestión de Donantes</h1>
    </div>
    <div class="col-sm-6">
        <a href="{{ route('inventario.donante.create') }}" class="btn btn-primary float-right">
            <i class="fas fa-plus"></i> Nuevo Donante
        </a>
    </div>
</div>
@stop

@section('content')
{{-- Info Boxes --}}
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $donantes->total() }}</h3>
                <p>Total de Donantes</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
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

{{-- Main Card --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Listado de Donantes</h3>
    </div>
    <div class="card-body">
        <table id="donantesTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Fecha Registro</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($donantes as $donante)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td>{{ $donante->nombre }}</td>
                        <td>
                            @if($donante->tipo === 'persona')
                                <span class="badge badge-primary"><i class="fas fa-user"></i> Persona</span>
                            @else
                                <span class="badge badge-success"><i class="fas fa-building"></i> Empresa</span>
                            @endif
                        </td>
                        <td>{{ $donante->email ?? 'N/A' }}</td>
                        <td>{{ $donante->telefono ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($donante->fecha_registro)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="{{ route('inventario.donante.show', $donante->id_donante) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm" href="{{ route('inventario.donante.edit', $donante->id_donante) }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventario.donante.destroy', $donante->id_donante) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('¿Está seguro de eliminar este donante?');">
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
        $('#donantesTable').DataTable({
            "paging": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [[5, 'desc']], // Order by fecha registro
            "language": {
                "search": "Buscar:",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "No hay donantes registrados",
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



