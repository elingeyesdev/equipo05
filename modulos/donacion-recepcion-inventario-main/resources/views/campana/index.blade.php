@extends('adminlte::page')

@section('title', 'Gestión de Campañas')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Gestión de Campañas</h1>
    </div>
    <div class="col-sm-6">
        @can('gestionar-campanas')
            <a href="{{ route('inventario.campana.create') }}" class="btn btn-primary float-right">
                Nueva Campaña
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
                <h3>{{ $campanas->total() }}</h3>
                <p>Total de Campañas</p>
            </div>
            <div class="icon">
                <i class="fas fa-bullhorn"></i>
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
        <h3 class="card-title">Listado de Campañas</h3>
    </div>
    <div class="card-body">
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => [[
                'id' => 'filtroEstadoCampana',
                'label' => 'Filtrar por estado',
                'options' => [
                    'proxima' => 'Próxima',
                    'activa' => 'Activa',
                    'finalizada' => 'Finalizada',
                ],
            ]],
            'sortOptions' => [
                'inicio_desc' => 'Fecha inicio (más reciente)',
                'inicio_asc' => 'Fecha inicio (más antigua)',
                'fin_desc' => 'Fecha fin (más reciente)',
                'nombre_asc' => 'Nombre (A-Z)',
                'nombre_desc' => 'Nombre (Z-A)',
            ],
            'defaultSort' => 'inicio_desc',
        ])

        <table id="campanasTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($campanas as $campana)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td><strong>{{ $campana->nombre }}</strong></td>
                        <td>{{ Str::limit($campana->descripcion, 50) }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($campana->fecha_inicio)->format('Y-m-d') }}">
                            {{ \Carbon\Carbon::parse($campana->fecha_inicio)->format('d/m/Y') }}
                        </td>
                        <td data-order="{{ \Carbon\Carbon::parse($campana->fecha_fin)->format('Y-m-d') }}">
                            {{ \Carbon\Carbon::parse($campana->fecha_fin)->format('d/m/Y') }}
                        </td>
                        <td>
                            @php
                                $now = \Carbon\Carbon::now();
                                $inicio = \Carbon\Carbon::parse($campana->fecha_inicio);
                                $fin = \Carbon\Carbon::parse($campana->fecha_fin);
                            @endphp
                            @if($now->lt($inicio))
                                <span class="badge badge-secondary">
                                    <i class="fas fa-clock"></i> Próxima
                                </span>
                            @elseif($now->between($inicio, $fin))
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> Activa
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-times-circle"></i> Finalizada
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="{{ route('inventario.campana.show', $campana->id_campana) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('gestionar-campanas')
                                    <a class="btn btn-warning btn-sm" href="{{ route('inventario.campana.edit', $campana->id_campana) }}"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('inventario.campana.destroy', $campana->id_campana) }}" method="POST"
                                        style="display: inline;"
                                        onsubmit="return confirm('¿Está seguro de eliminar esta campaña?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
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
            selector: '#campanasTable',
            defaultOrder: [[3, 'desc']],
            filters: [{
                select: '#filtroEstadoCampana',
                column: 5,
                valueMap: {
                    proxima: 'Próxima',
                    activa: 'Activa',
                    finalizada: 'Finalizada',
                },
            }],
            sortSelect: '#ordenarPor',
            sortMap: {
                inicio_desc: [3, 'desc'],
                inicio_asc: [3, 'asc'],
                fin_desc: [4, 'desc'],
                nombre_asc: [1, 'asc'],
                nombre_desc: [1, 'desc'],
            },
        });
    });
</script>
@stop




