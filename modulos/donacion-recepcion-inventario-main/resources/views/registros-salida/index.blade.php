@extends('adminlte::page')

@section('title', 'Registros de Salida')

@section('content_header')
    <h1>Registros de Salida</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-truck-loading"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Salidas</span>
                        <span class="info-box-number">
                            {{ $registrosSalidas->total() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Listado de Salidas</h3>
                        <div class="card-tools">
                            <a href="{{ route('inventario.registros-salida.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nueva Salida
                            </a>
                        </div>
                    </div>
                    
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success m-3">
                            <p>{{ $message }}</p>
                        </div>
                    @endif

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Paquete</th>
                                        <th>Fecha Salida</th>
                                        <th>Destino</th>
                                        <th>Observaciones</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($registrosSalidas as $registrosSalida)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>
                                                @if($registrosSalida->paquete)
                                                    <span class="badge badge-info">{{ $registrosSalida->paquete->codigo_paquete }}</span>
                                                @else
                                                    <span class="badge badge-secondary">Sin Paquete</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($registrosSalida->fecha_salida)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $registrosSalida->destino }}</td>
                                            <td>{{ Str::limit($registrosSalida->observaciones, 50) }}</td>
                                            <td>
                                                <form action="{{ route('inventario.registros-salida.destroy', $registrosSalida->id_salida) }}" method="POST">
                                                    <a class="btn btn-sm btn-primary" href="{{ route('inventario.registros-salida.show', $registrosSalida->id_salida) }}" title="Ver"><i class="fas fa-eye"></i></a>
                                                    {{-- <a class="btn btn-sm btn-success" href="{{ route('inventario.registros-salida.edit', $registrosSalida->id_salida) }}" title="Editar"><i class="fas fa-edit"></i></a> --}}
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este registro?')" title="Eliminar"><i class="fas fa-trash"></i></button>
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
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
@stop

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
            "order": [[2, 'desc']], // Order by fecha salida descending
            "language": {
                "search": "Buscar:",
                "zeroRecords": "No se encontraron resultados",
                "emptyTable": "No hay registros de salida",
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




