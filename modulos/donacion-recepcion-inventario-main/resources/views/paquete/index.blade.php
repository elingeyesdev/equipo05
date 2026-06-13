@extends('adminlte::page')

@section('title', 'Paquetes')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Gestión de Paquetes</h1>
    </div>
    <div class="col-sm-6">
        <a href="{{ route('inventario.paquete.create') }}" class="btn btn-primary float-right">
            Nuevo Paquete
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
                <h3>{{ $paquetes->count() }}</h3>
                <p>Total de Paquetes</p>
            </div>
            <div class="icon">
                <i class="fas fa-box"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><i class="fas fa-clock"></i></h3>
                <p>Solicitudes de Paquetes</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <a href="{{ route('inventario.paquete.pendientes') }}" class="small-box-footer">
                Ver solicitudes <i class="fas fa-arrow-circle-right"></i>
            </a>
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
        <h3 class="card-title">Listado de Paquetes</h3>
    </div>
    <div class="card-body">
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => [[
                'id' => 'filtroEstadoPaquete',
                'label' => 'Filtrar por estado',
                'options' => [
                    'pendiente' => 'Pendiente',
                    'en_proceso' => 'En proceso',
                    'despachado' => 'Despachado',
                    'cancelado' => 'Cancelado',
                ],
            ]],
            'sortOptions' => [
                'fecha_desc' => 'Fecha creación (más reciente)',
                'fecha_asc' => 'Fecha creación (más antigua)',
                'codigo_asc' => 'Código (A-Z)',
                'codigo_desc' => 'Código (Z-A)',
            ],
            'defaultSort' => 'fecha_desc',
        ])

        <table id="paquetesTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Código</th>
                    <th>Fecha de Creación</th>
                    <th>Estado</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($paquetes as $paquete)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td>
                            <strong>{{ $paquete->codigo_paquete ?? 'N/A' }}</strong>
                        </td>
                        <td data-order="{{ $paquete->fecha_creacion ? \Carbon\Carbon::parse($paquete->fecha_creacion)->format('Y-m-d H:i:s') : '' }}">
                            {{ $paquete->fecha_creacion ? \Carbon\Carbon::parse($paquete->fecha_creacion)->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="text-center">
                            @php
                                $badgeClass = match ($paquete->estado) {
                                    'despachado' => 'success',
                                    'en_proceso' => 'primary',
                                    'cancelado' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <span class="badge badge-{{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $paquete->estado ?? 'Pendiente')) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="{{ route('inventario.paquete.show', $paquete->id_paquete) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm" href="{{ route('inventario.paquete.edit', $paquete->id_paquete) }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form id="delete-form-{{ $paquete->id_paquete }}"
                                    action="{{ route('inventario.paquete.destroy', $paquete->id_paquete) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="deleted_reason"
                                        id="deleted-reason-{{ $paquete->id_paquete }}">
                                    <button type="button" class="btn btn-danger btn-sm delete-btn"
                                        data-id="{{ $paquete->id_paquete }}" data-type="paquete" title="Eliminar">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        initInventarioListTable({
            selector: '#paquetesTable',
            defaultOrder: [[2, 'desc']],
            filters: [{
                select: '#filtroEstadoPaquete',
                column: 3,
                valueMap: {
                    pendiente: 'Pendiente',
                    en_proceso: 'En proceso',
                    despachado: 'Despachado',
                    cancelado: 'Cancelado',
                },
            }],
            sortSelect: '#ordenarPor',
            sortMap: {
                fecha_desc: [2, 'desc'],
                fecha_asc: [2, 'asc'],
                codigo_asc: [1, 'asc'],
                codigo_desc: [1, 'desc'],
            },
        });

        // Handle delete button clicks
        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            const type = $(this).data('type');

            Swal.fire({
                title: `¿Eliminar ${type}?`,
                html: `
                    <div class="form-group text-left mt-3">
                        <label for="swal-reason" class="font-weight-bold">Motivo de eliminación *</label>
                        <textarea id="swal-reason" class="form-control mt-2" 
                            placeholder="Ingrese el motivo por el cual desea eliminar este ${type}..."
                            rows="4" style="resize: vertical;"></textarea>
                        <small class="form-text text-muted mt-1">
                            <i class="fas fa-info-circle"></i> Este motivo quedará registrado en el sistema para auditoría.
                        </small>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash mr-1"></i> Sí, eliminar',
                cancelButtonText: '<i class="fas fa-times mr-1"></i> Cancelar',
                width: '600px',
                customClass: {
                    popup: 'swal-delete-modal',
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false,
                preConfirm: () => {
                    const reason = document.getElementById('swal-reason').value;
                    if (!reason || reason.trim().length < 10) {
                        Swal.showValidationMessage('Por favor ingrese un motivo válido (mínimo 10 caracteres)');
                        return false;
                    }
                    if (reason.trim().length > 500) {
                        Swal.showValidationMessage('El motivo no puede exceder 500 caracteres');
                        return false;
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#deleted-reason-' + id).val(result.value);
                    $('#delete-form-' + id).submit();
                }
            });
        });
    });
</script>
@stop




