@extends('layouts.app')

@section('title', 'Veterinario — ' . ($veterinarian->person?->nombre ?? 'Detalle'))
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Veterinarios')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-user-md"></i> {{ $veterinarian->person?->nombre ?? 'Veterinario' }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.veterinarians.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.veterinarians.edit', $veterinarian->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Persona:</strong>
                            {{ $veterinarian->person?->nombre ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Especialidad:</strong>
                            {{ $veterinarian->especialidad ?: '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Motivo de postulación:</strong>
                            {{ $veterinarian->motivo_postulacion ?: '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>CV:</strong>
                            @if($veterinarian->cv_documentado)
                                <a href="{{ asset('storage/' . $veterinarian->cv_documentado) }}" target="_blank">Ver CV</a>
                            @else
                                —
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <strong>Aprobado:</strong>
                            {{ $veterinarian->aprobado === null ? '—' : ($veterinarian->aprobado ? 'Sí' : 'No') }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Motivo revisión:</strong>
                            {{ $veterinarian->motivo_revision ?: '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Estado de la postulación:</strong>
                            @if($veterinarian->aprobado === true)
                                Postulación aceptada.
                            @elseif($veterinarian->aprobado === false && $veterinarian->motivo_revision)
                                Postulación no aceptada. Motivo: {{ $veterinarian->motivo_revision }}
                            @elseif($veterinarian->aprobado === false)
                                Postulación no aceptada.
                            @elseif($veterinarian->aprobado === null)
                                Postulación en proceso de revisión.
                            @else
                                —
                            @endif
                        </div>

                        @if($veterinarian->aprobado === null)
                        @canManageRescatePeople
                        <div class="form-group mb-3">
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalAprobarVeterinarian">
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
    @if($veterinarian->aprobado === null)
    {{-- Modal para aprobar/rechazar solicitud de veterinario --}}
    <div class="modal fade" id="modalAprobarVeterinarian" tabindex="-1" role="dialog" aria-labelledby="modalAprobarVeterinarianLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAprobarVeterinarianLabel">
                        <i class="fa fa-user-check"></i> {{ __('Revisar Solicitud de Veterinario') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('rescate.veterinarians.approve', $veterinarian->id) }}" method="POST" id="formAprobarVeterinarian">
                    @method('PUT')
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">{{ __('¿Desea aprobar o rechazar esta solicitud de veterinario?') }}</p>
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
                        <input type="hidden" name="action" id="actionVeterinarian" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="btnRechazarVeterinarian">
                            <i class="fa fa-times-circle"></i> {{ __('Rechazar') }}
                        </button>
                        <button type="button" class="btn btn-success" id="btnAprobarVeterinarian">
                            <i class="fa fa-check-circle"></i> {{ __('Aprobar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.getElementById('formAprobarVeterinarian');
        var actionInput = document.getElementById('actionVeterinarian');
        var motivoInput = document.getElementById('motivo_revision');
        var btnRechazar = document.getElementById('btnRechazarVeterinarian');
        var btnAprobar = document.getElementById('btnAprobarVeterinarian');

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
