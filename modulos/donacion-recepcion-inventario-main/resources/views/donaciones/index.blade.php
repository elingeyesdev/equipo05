@extends('adminlte::page')

@section('title', 'Gestión de Donaciones')

@section('content_header')
@include('inventario::partials.page-toolbar', [
    'title' => 'Gestión de Donaciones',
    'createRoute' => route('inventario.donaciones.create'),
    'createLabel' => 'Nueva Donación',
])
@stop

@section('content')
@include('inventario::partials.flash-messages')
{{-- Statistics Row --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $totalDonaciones }}</h3>
                <p>Total de Donaciones</p>
            </div>
            <div class="icon">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $donacionesDinero }}</h3>
                <p>Donaciones en Dinero</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $donacionesEspecie }}</h3>
                <p>Donaciones en Especie</p>
            </div>
            <div class="icon">
                <i class="fas fa-box-open"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Bs. {{ number_format($montoTotal, 2) }}</h3>
                <p>Monto Total Recaudado</p>
            </div>
            <div class="icon">
                <i class="fas fa-coins"></i>
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
        <h3 class="card-title">Listado de Donaciones</h3>
    </div>
    <div class="card-body">
        @include('inventario::partials.datatables-list-toolbar', [
            'filters' => [[
                'id' => 'filtroTipo',
                'label' => 'Filtrar por tipo',
                'options' => ['dinero' => 'Dinero', 'especie' => 'Especie'],
            ]],
            'sortOptions' => [
                'fecha_desc' => 'Fecha (más reciente)',
                'fecha_asc' => 'Fecha (más antigua)',
                'alfabeto_asc' => 'Donante (A-Z)',
                'alfabeto_desc' => 'Donante (Z-A)',
            ],
            'defaultSort' => 'fecha_desc',
        ])

        <table id="donacionesTable" class="table table-bordered table-striped table-hover">
            <thead class="thead-light">
                <tr>
                    <th width="60px">#</th>
                    <th>Donante</th>
                    <th>Tipo</th>
                    <th>Fecha y Hora</th>
                    <th width="200px" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($donaciones as $donacion)
                    <tr>
                        <td class="text-center"><strong>{{ ++$i }}</strong></td>
                        <td>
                            @if($donacion->donante)
                                {{ $donacion->donante->nombre }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($donacion->tipo === 'dinero')
                                <span class="badge badge-success badge-lg" data-tipo="dinero">
                                    <i class="fas fa-dollar-sign"></i> Dinero
                                </span>
                            @else
                                <span class="badge badge-warning badge-lg" data-tipo="especie">
                                    <i class="fas fa-box-open"></i> Especie
                                </span>
                            @endif
                        </td>
                        <td data-order="{{ \Carbon\Carbon::parse($donacion->fecha)->format('Y-m-d H:i:s') }}">
                            {{ \Carbon\Carbon::parse($donacion->fecha)->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="{{ route('inventario.donaciones.show', $donacion->id_donacion) }}"
                                    title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm"
                                    href="{{ route('inventario.donaciones.edit', $donacion->id_donacion) }}" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form id="delete-form-{{ $donacion->id_donacion }}"
                                    action="{{ route('inventario.donaciones.destroy', $donacion->id_donacion) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="deleted_reason"
                                        id="deleted-reason-{{ $donacion->id_donacion }}">
                                    <button type="button" class="btn btn-danger btn-sm delete-btn"
                                        data-id="{{ $donacion->id_donacion }}" data-type="donación" title="Eliminar">
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

    .badge-lg {
        font-size: 0.9rem;
        padding: 0.4rem 0.6rem;
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
            selector: '#donacionesTable',
            defaultOrder: [[3, 'desc']],
            filters: [{
                select: '#filtroTipo',
                column: 2,
                valueMap: { dinero: 'Dinero', especie: 'Especie' },
            }],
            sortSelect: '#ordenarPor',
            sortMap: {
                fecha_desc: [3, 'desc'],
                fecha_asc: [3, 'asc'],
                alfabeto_asc: [1, 'asc'],
                alfabeto_desc: [1, 'desc'],
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
                            placeholder="Ingrese el motivo por el cual desea eliminar esta ${type}..."
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




