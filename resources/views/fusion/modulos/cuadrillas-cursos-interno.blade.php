@extends('layouts.app')

@section('title', 'Cursos de Capacitación')

@section('content')
<div class="container-fluid mt-4">
    
    <!-- Cabecera de Página -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h2 class="m-0 font-weight-bold text-dark"><i class="fas fa-graduation-cap mr-2 text-warning"></i> Cursos</h2>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="{{ route('cuadrillas.crud.create', ['seccion' => 'cursos']) }}" class="btn btn-primary font-weight-bold shadow-sm mr-2">
                <i class="fas fa-plus mr-1"></i> Nuevo Curso
            </a>
            <button type="button" class="btn btn-secondary font-weight-bold shadow-sm" id="toggleViewBtn">
                <i class="fas fa-table mr-1"></i> Vista Tabla
            </button>
        </div>
    </div>

    {{-- Mensajes de éxito/error --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <i class="icon fas fa-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Vista de Cards --}}
    <div id="cardsView">
        <div class="row">
            <div class="col-md-12">
                @forelse($cursos as $curso)
                    <div class="callout callout-warning shadow-sm mb-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                            <div class="flex-grow-1">
                                <h5 class="font-weight-bold text-dark">
                                    <i class="fas fa-graduation-cap text-warning mr-1"></i>
                                    {{ $curso->nombre }}
                                </h5>
                            </div>
                            <div class="ml-sm-3 mt-2 mt-sm-0">
                                <span class="badge badge-warning text-white p-2">
                                    <i class="fas fa-users mr-1"></i> {{ $curso->cursos_asignados_count ?? 0 }} Asignados
                                </span>
                            </div>
                        </div>

                        <p class="mb-2 mt-2 text-secondary" style="font-size: 0.95rem;">
                            {{ $curso->descripcion ?? 'Sin descripción disponible.' }}
                        </p>

                        <small class="text-muted d-block mt-2">
                            <i class="far fa-calendar-alt mr-1"></i>
                            Creado el {{ $curso->creado ? $curso->creado->format('d/m/Y') : 'N/A' }}
                        </small>

                        <div class="mt-3 d-flex" style="gap: .5rem;">
                            <a href="{{ route('cuadrillas.crud.edit', ['seccion' => 'cursos', 'id' => $curso->id_curso]) }}" class="btn btn-warning text-white btn-sm font-weight-bold">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                            <form action="{{ route('cuadrillas.crud.destroy', ['seccion' => 'cursos', 'id' => $curso->id_curso]) }}" method="POST" class="form-delete d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                                    <i class="fas fa-trash mr-1"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="callout callout-info shadow-sm">
                        <h5 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-1"></i> No hay cursos registrados</h5>
                        <p class="mb-0 text-secondary">Haga clic en "Nuevo Curso" para crear el primer curso de capacitación.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Vista de Tabla --}}
    <div id="tableView" style="display: none;">
        <div class="card card-outline card-warning shadow-lg border-0">
            <div class="card-header bg-warning text-dark py-3">
                <h3 class="card-title font-weight-bold m-0">Listado de Cursos</h3>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table id="cursos-table" class="table table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Asignados</th>
                                <th>Fecha Creación</th>
                                <th width="150px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cursos as $curso)
                                <tr>
                                    <td><strong>{{ $curso->nombre }}</strong></td>
                                    <td>{{ Str::limit($curso->descripcion ?? 'Sin descripción', 100) }}</td>
                                    <td>
                                        <span class="badge badge-info p-2">
                                            <i class="fas fa-users mr-1"></i> {{ $curso->cursos_asignados_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td>{{ $curso->creado ? $curso->creado->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="d-flex" style="gap: .35rem;">
                                            <a href="{{ route('cuadrillas.crud.edit', ['seccion' => 'cursos', 'id' => $curso->id_curso]) }}" class="btn btn-sm btn-info" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('cuadrillas.crud.destroy', ['seccion' => 'cursos', 'id' => $curso->id_curso]) }}" method="POST" class="form-delete-table d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
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
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<style>
    .callout {
        border-radius: 0.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        background-color: #fff;
        border-left: 5px solid #e9ecef;
        margin-bottom: 1rem;
        padding: 1.25rem;
    }
    .callout.callout-warning {
        border-left-color: #ffc107;
    }
    .callout.callout-info {
        border-left-color: #17a2b8;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    let isTableView = false;
    let dataTableInitialized = false;

    // Toggle entre vista de cards y tabla
    $('#toggleViewBtn').on('click', function() {
        isTableView = !isTableView;

        if (isTableView) {
            $('#cardsView').hide();
            $('#tableView').show();
            $(this).html('<i class="fas fa-th-large mr-1"></i> Vista Cards');

            // Inicializar DataTable
            if (!dataTableInitialized) {
                $('#cursos-table').DataTable({
                    responsive: true,
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    order: [[3, 'desc']]
                });
                dataTableInitialized = true;
            }
        } else {
            $('#tableView').hide();
            $('#cardsView').show();
            $(this).html('<i class="fas fa-table mr-1"></i> Vista Tabla');
        }
    });

    // Confirmación de borrado
    $('.form-delete, .form-delete-table').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta acción eliminará el curso de forma permanente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm('¿Está seguro de que desea eliminar este curso?')) {
                form.submit();
            }
        }
    });
});
</script>
@endpush
