@extends('adminlte::page')

@section('title', 'Gestión de Donantes')

@section('content_header')
@include('inventario::partials.page-toolbar', [
    'title' => 'Gestión de Donantes',
    'createRoute' => route('inventario.donante.create'),
    'createLabel' => 'Nuevo Donante',
])
@stop

@section('content')
@include('inventario::partials.flash-messages')
<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $donantes->count() }}</h3>
                <p>Total de Donantes</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

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
        <h3 class="card-title">Listado de Donantes</h3>
    </div>
    <div class="card-body">
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => [[
                'id' => 'filtroTipoDonante',
                'label' => 'Filtrar por tipo',
                'options' => ['persona' => 'Persona', 'empresa' => 'Empresa'],
            ]],
            'sortOptions' => [
                'fecha_desc' => 'Fecha registro (más reciente)',
                'fecha_asc' => 'Fecha registro (más antigua)',
                'nombre_asc' => 'Nombre (A-Z)',
                'nombre_desc' => 'Nombre (Z-A)',
            ],
            'defaultSort' => 'fecha_desc',
        ])

        <table id="donantesTable" class="table table-bordered table-striped table-hover w-100">
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
                        <td data-order="{{ \Carbon\Carbon::parse($donante->fecha_registro)->format('Y-m-d') }}">
                            {{ \Carbon\Carbon::parse($donante->fecha_registro)->format('d/m/Y') }}
                        </td>
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
                                    class="d-inline"
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
@stop

@section('js')
@include('inventario::partials.datatables-inventario-init')
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function () {
        initInventarioListTable({
            selector: '#donantesTable',
            defaultOrder: [[5, 'desc']],
            filters: [{
                select: '#filtroTipoDonante',
                column: 2,
                valueMap: { persona: 'Persona', empresa: 'Empresa' },
            }],
            sortSelect: '#ordenarPor',
            sortMap: {
                fecha_desc: [5, 'desc'],
                fecha_asc: [5, 'asc'],
                nombre_asc: [1, 'asc'],
                nombre_desc: [1, 'desc'],
            },
        });
    });
</script>
@stop
