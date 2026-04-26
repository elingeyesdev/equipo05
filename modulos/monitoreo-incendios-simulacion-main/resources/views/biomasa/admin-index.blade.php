@extends('layouts.app')

@section('subtitle', 'Moderación de Biomasas')
@section('content_header_title', 'Gestión de Biomasas')
@section('content_header_subtitle', '- Moderación y Aprobación')

@section('content_body')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('incendios.biomasas.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Crear Nueva Biomasa
                </a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                @if ($message = Session::get('success'))
                    <x-adminlte-alert theme="success" dismissable>
                        {{ $message }}
                    </x-adminlte-alert>
                @endif

                <!-- Tabs para filtrar por estado -->
                <x-adminlte-card title="Biomasas Reportadas" theme="primary" icon="fas fa-leaf">
                    
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#pendientes">
                                <i class="fas fa-clock"></i> Pendientes 
                                <span class="badge badge-warning">{{ $biomasas->where('estado', 'pendiente')->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#aprobadas">
                                <i class="fas fa-check-circle"></i> Aprobadas 
                                <span class="badge badge-success">{{ $biomasas->where('estado', 'aprobada')->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#rechazadas">
                                <i class="fas fa-times-circle"></i> Rechazadas 
                                <span class="badge badge-danger">{{ $biomasas->where('estado', 'rechazada')->count() }}</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3">
                        <!-- PENDIENTES -->
                        <div id="pendientes" class="tab-pane fade show active">
                            @include('biomasa.partials.lista-biomasas', ['biomasasFiltradas' => $biomasas->where('estado', 'pendiente'), 'estado' => 'pendiente'])
                        </div>

                        <!-- APROBADAS -->
                        <div id="aprobadas" class="tab-pane fade">
                            @include('biomasa.partials.lista-biomasas', ['biomasasFiltradas' => $biomasas->where('estado', 'aprobada'), 'estado' => 'aprobada'])
                        </div>

                        <!-- RECHAZADAS -->
                        <div id="rechazadas" class="tab-pane fade">
                            @include('biomasa.partials.lista-biomasas', ['biomasasFiltradas' => $biomasas->where('estado', 'rechazada'), 'estado' => 'rechazada'])
                        </div>
                    </div>

                    {!! $biomasas->withQueryString()->links() !!}
                </x-adminlte-card>
            </div>
        </div>
    </div>

    <!-- Modal para rechazar -->
    <div class="modal fade" id="modalRechazar" tabindex="-1" role="dialog" aria-labelledby="modalRechazarLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="modalRechazarLabel">
                        <i class="fas fa-ban"></i> Motivo de Rechazo
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formRechazar" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="motivo_rechazo">Motivo del Rechazo <span class="text-danger">*</span></label>
                            <textarea 
                                name="motivo_rechazo" 
                                id="motivo_rechazo" 
                                class="form-control" 
                                rows="4" 
                                placeholder="Explique por qué se rechaza esta biomasa..." 
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-ban"></i> Rechazar Biomasa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function abrirModalRechazo(biomasaId) {
        console.log('Abriendo modal para biomasa ID:', biomasaId);
        const form = document.getElementById('formRechazar');
        form.action = `/biomasas/${biomasaId}/rechazar`;
        console.log('Form action establecida a:', form.action);
        $('#modalRechazar').modal('show');
    }
    
    // Limpiar formulario al cerrar modal
    $('#modalRechazar').on('hidden.bs.modal', function () {
        document.getElementById('formRechazar').reset();
    });
</script>
@stop
