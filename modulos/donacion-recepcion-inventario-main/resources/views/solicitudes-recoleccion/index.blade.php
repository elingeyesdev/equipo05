@extends('adminlte::page')

@section('title', 'Solicitudes de Recolección')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Solicitudes de Recolección</h1>
    </div>
    <div class="col-sm-6">
        <a href="{{ route('inventario.solicitudes-recoleccions.create') }}" class="btn btn-primary float-right">
            Nueva Solicitud
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
                <h3>{{ $solicitudesRecoleccions->total() }}</h3>
                <p>Total de Solicitudes</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list"></i>
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
        <h3 class="card-title">Listado de Solicitudes</h3>
    </div>
    <div class="card-body">
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => [[
                'id' => 'filtroEstadoSolicitud',
                'label' => 'Filtrar por estado',
                'options' => [
                    'pendiente' => 'Pendiente',
                    'en_proceso' => 'En proceso',
                    'completada' => 'Completada',
                    'cancelada' => 'Cancelada',
                ],
            ]],
            'sortOptions' => [
                'fecha_desc' => 'Fecha programada (más reciente)',
                'fecha_asc' => 'Fecha programada (más antigua)',
                'donante_asc' => 'Donante (A-Z)',
                'donante_desc' => 'Donante (Z-A)',
            ],
            'defaultSort' => 'fecha_desc',
        ])

        <table id="solicitudesTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Donante</th>
                    <th>Direccin</th>
                    <th>Fecha Programada</th>
                    <th>Estado</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitudesRecoleccions as $solicitud)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td>
                            @if($solicitud->donante)
                                <strong>{{ $solicitud->donante->nombre }}</strong>
                                <br><small class="text-muted">{{ $solicitud->donante->tipo ?? 'N/A' }}</small>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>{{ $solicitud->direccion_recoleccion }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($solicitud->fecha_programada)->format('Y-m-d H:i:s') }}">
                            {{ \Carbon\Carbon::parse($solicitud->fecha_programada)->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-center">
                            @php
                                $badgeClass = match ($solicitud->estado) {
                                    'completada' => 'success',
                                    'en_proceso' => 'primary',
                                    'cancelada' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $solicitud->estado ?? 'pendiente')) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm"
                                    href="{{ route('inventario.solicitudes-recoleccions.show', $solicitud->id_solicitud) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm"
                                    href="{{ route('inventario.solicitudes-recoleccions.edit', $solicitud->id_solicitud) }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventario.solicitudes-recoleccions.destroy', $solicitud->id_solicitud) }}"
                                    method="POST" style="display: inline;"
                                    onsubmit="return confirm('¿Está seguro de eliminar esta solicitud?');">
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
            selector: '#solicitudesTable',
            defaultOrder: [[3, 'desc']],
            filters: [{
                select: '#filtroEstadoSolicitud',
                column: 4,
                valueMap: {
                    pendiente: 'Pendiente',
                    en_proceso: 'En proceso',
                    completada: 'Completada',
                    cancelada: 'Cancelada',
                },
            }],
            sortSelect: '#ordenarPor',
            sortMap: {
                fecha_desc: [3, 'desc'],
                fecha_asc: [3, 'asc'],
                donante_asc: [1, 'asc'],
                donante_desc: [1, 'desc'],
            },
        });
    });
</script>
@stop




