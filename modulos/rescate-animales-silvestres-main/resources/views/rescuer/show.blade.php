@extends('layouts.app')

@section('title', 'Rescatista — ' . ($rescuer->person?->nombre ?? 'Detalle'))
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Rescatistas')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-hands-helping"></i> {{ $rescuer->person?->nombre ?? 'Rescatista' }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.rescuers.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.rescuers.edit', $rescuer->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Persona:</strong>
                            {{ $rescuer->person?->nombre ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Motivo de postulación:</strong>
                            {{ $rescuer->motivo_postulacion ?: '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>CV:</strong>
                            @if($rescuer->cv_documentado)
                                <a href="{{ asset('storage/' . $rescuer->cv_documentado) }}" target="_blank">Ver CV</a>
                            @else
                                —
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <strong>Aprobado:</strong>
                            {{ $rescuer->aprobado === null ? '—' : ($rescuer->aprobado ? 'Sí' : 'No') }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Motivo revisión:</strong>
                            {{ $rescuer->motivo_revision ?: '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Estado de la postulación:</strong>
                            @if($rescuer->aprobado === true)
                                Postulación aceptada.
                            @elseif($rescuer->aprobado === false && $rescuer->motivo_revision)
                                Postulación no aceptada. Motivo: {{ $rescuer->motivo_revision }}
                            @elseif($rescuer->aprobado === false)
                                Postulación no aceptada.
                            @elseif($rescuer->aprobado === null)
                                Postulación en proceso de revisión.
                            @else
                                —
                            @endif
                        </div>

                        @if($rescuer->aprobado === null)
                        @canManageRescatePeople
                        <div class="form-group mb-3">
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalAprobarRescuer">
                                <i class="fa fa-check-circle"></i> Aprobar/Rechazar solicitud
                            </button>
                        </div>
                        @endcanManageRescatePeople
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @canManageRescatePeople
    @if($rescuer->aprobado === null)
    {{-- Modal para aprobar/rechazar solicitud de rescatista --}}
    <div class="modal fade" id="modalAprobarRescuer" tabindex="-1" role="dialog" aria-labelledby="modalAprobarRescuerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAprobarRescuerLabel">
                        <i class="fa fa-user-check"></i> {{ __('Revisar Solicitud de Rescatista') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('rescate.rescuers.approve', $rescuer->id) }}" method="POST" id="formAprobarRescuer">
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">{{ __('¿Desea aprobar o rechazar esta solicitud de rescatista?') }}</p>
                        <div class="form-group">
                            <label for="motivo_revision">{{ __('Motivo de revisión') }} <span class="text-danger">*</span></label>
                            <textarea
                                class="form-control"
                                id="motivo_revision"
                                name="motivo_revision"
                                rows="3"
                                required
                                minlength="3"
                                placeholder="{{ __('Ingrese el motivo de la aprobación o rechazo...') }}"></textarea>
                            <small class="form-text text-muted">{{ __('Mínimo 3 caracteres') }}</small>
                        </div>
                        <input type="hidden" name="action" id="actionRescuer" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btnRechazarRescuer">
                            <i class="fa fa-times-circle"></i> {{ __('Rechazar') }}
                        </button>
                        <button type="button" class="btn btn-success" id="btnAprobarRescuer">
                            <i class="fa fa-check-circle"></i> {{ __('Aprobar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.getElementById('formAprobarRescuer');
        var actionInput = document.getElementById('actionRescuer');
        var motivoInput = document.getElementById('motivo_revision');
        var btnRechazar = document.getElementById('btnRechazarRescuer');
        var btnAprobar = document.getElementById('btnAprobarRescuer');

        function submitForm(action) {
            if (!motivoInput.value || motivoInput.value.trim().length < 3) {
                alert('{{ __('Por favor, ingrese un motivo de revisión (mínimo 3 caracteres).') }}');
                motivoInput.focus();
                return false;
            }

            if (actionInput) {
                actionInput.value = action;
            }

            if (btnRechazar) btnRechazar.disabled = true;
            if (btnAprobar) btnAprobar.disabled = true;

            if (form) {
                form.submit();
            }
            return true;
        }

        if (btnRechazar) {
            btnRechazar.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                submitForm('reject');
            });
        }

        if (btnAprobar) {
            btnAprobar.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                submitForm('approve');
            });
        }
    });
    </script>
    @endif
    @endcanManageRescatePeople

    @include('partials.page-pad')
@endsection
