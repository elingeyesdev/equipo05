@extends('adminlte::page')

@section('title', 'Espacios')

@section('content_header')
@include('inventario::partials.page-toolbar', [
    'title' => 'Gestión de Espacios',
    'createRoute' => route('inventario.espacio.create'),
    'createLabel' => 'Nuevo Espacio',
])
@stop

@section('content')
@include('inventario::partials.flash-messages')
{{-- Statistics Row --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $espacios->count() }}</h3>
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
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => [[
                'id' => 'filtroEstadoEspacio',
                'label' => 'Filtrar por estado',
                'options' => [
                    'disponible' => 'Disponible',
                    'lleno' => 'Lleno',
                ],
            ]],
            'sortOptions' => [
                'codigo_asc' => 'Código espacio (A-Z)',
                'codigo_desc' => 'Código espacio (Z-A)',
                'estante_asc' => 'Estante (A-Z)',
                'estante_desc' => 'Estante (Z-A)',
            ],
            'defaultSort' => 'codigo_asc',
        ])

        <table id="espaciosTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Estante</th>
                    <th>Código Espacio</th>
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
                                    onsubmit="return confirm('¿Está seguro de eliminar este espacio?');">
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
            selector: '#espaciosTable',
            defaultOrder: [[2, 'asc']],
            filters: [{
                select: '#filtroEstadoEspacio',
                column: 3,
                valueMap: {
                    disponible: 'DISPONIBLE',
                    lleno: 'LLENO',
                },
            }],
            sortSelect: '#ordenarPor',
            sortMap: {
                codigo_asc: [2, 'asc'],
                codigo_desc: [2, 'desc'],
                estante_asc: [1, 'asc'],
                estante_desc: [1, 'desc'],
            },
        });
    });
</script>
@stop




