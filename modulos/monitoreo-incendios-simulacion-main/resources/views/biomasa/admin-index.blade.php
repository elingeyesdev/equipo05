@extends('layouts.app')

@section('subtitle', 'Biomasas')
@section('content_header_title', 'Biomasas')
@section('content_header_subtitle', 'Moderación y aprobación')

@section('content_body')
    @include('incendios::partials.module-nav')
    @include('incendios::partials.flash-messages')

    <div class="card inc-list-card shadow-sm">
        <div class="card-header">
            <div class="inc-btn-toolbar w-100">
                <a href="{{ route('incendios.biomasas.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus"></i> Nueva biomasa
                </a>
            </div>
        </div>

        <ul class="nav nav-tabs border-bottom-0 px-3 pt-2" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#pendientes">
                    Pendientes <span class="badge badge-warning ml-1">{{ $biomasas->where('estado', 'pendiente')->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#aprobadas">
                    Aprobadas <span class="badge badge-success ml-1">{{ $biomasas->where('estado', 'aprobada')->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#rechazadas">
                    Rechazadas <span class="badge badge-danger ml-1">{{ $biomasas->where('estado', 'rechazada')->count() }}</span>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="pendientes" class="tab-pane fade show active">
                @include('biomasa.partials.lista-biomasas', ['biomasasFiltradas' => $biomasas->where('estado', 'pendiente'), 'estado' => 'pendiente'])
            </div>
            <div id="aprobadas" class="tab-pane fade">
                @include('biomasa.partials.lista-biomasas', ['biomasasFiltradas' => $biomasas->where('estado', 'aprobada'), 'estado' => 'aprobada'])
            </div>
            <div id="rechazadas" class="tab-pane fade">
                @include('biomasa.partials.lista-biomasas', ['biomasasFiltradas' => $biomasas->where('estado', 'rechazada'), 'estado' => 'rechazada'])
            </div>
        </div>

        @if($biomasas->hasPages())
            <div class="card-footer bg-white d-flex justify-content-center py-3">
                {!! $biomasas->withQueryString()->links() !!}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalRechazar" tabindex="-1" role="dialog" aria-labelledby="modalRechazarLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRechazarLabel">
                        <i class="fas fa-ban text-danger"></i> Motivo de rechazo
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formRechazar" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label for="motivo_rechazo">Motivo <span class="text-danger">*</span></label>
                            <textarea name="motivo_rechazo" id="motivo_rechazo" class="form-control" rows="4" placeholder="Explique por qué se rechaza esta biomasa..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Rechazar biomasa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    function abrirModalRechazo(biomasaId) {
        const form = document.getElementById('formRechazar');
        form.action = '{{ route('incendios.biomasas.rechazar', ['id' => '__ID__']) }}'.replace('__ID__', biomasaId);
        $('#modalRechazar').modal('show');
    }

    $('#modalRechazar').on('hidden.bs.modal', function () {
        document.getElementById('formRechazar').reset();
    });
</script>
@endsection
