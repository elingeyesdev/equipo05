@extends('layouts.app')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-hand-holding-usd"></i> Asignar Donación
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asignaciones.show', $asignacion->asignacionid) }}">Asignación #{{ $asignacion->asignacionid }}</a></li>
                    <li class="breadcrumb-item active">Asignar Donación</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- INFORMACIÓN DE LA ASIGNACIÓN --}}
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>#{{ $asignacion->asignacionid }}</h3>
                        <p>ID Asignación</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($asignacion->monto, 2) }}</h3>
                        <p>Monto Total (Bs)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $asignacion->campania->titulo ?? 'N/D' }}</h3>
                        <p>Campaña</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $donaciones->count() }}</h3>
                        <p>Donaciones Disponibles</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-gift"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                {{-- FORMULARIO DE ASIGNACIÓN --}}
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-hand-holding-heart"></i> Formulario de Asignación
                        </h3>
                    </div>

                    <form action="{{ route('asignaciones.asignarDonacionStore', $asignacion->asignacionid) }}" 
                          method="POST" id="formAsignar">
                        @csrf

                        <div class="card-body">
                            {{-- Selección de Donación --}}
                            <div class="form-group">
                                <label for="donacionid">
                                    <i class="fas fa-gift"></i> Selecciona Donación Disponible *
                                </label>
                                <select name="donacionid" id="donacionid" 
                                        class="form-control select2" 
                                        style="width: 100%;" 
                                        required>
                                    <option value="">-- Seleccionar Donación --</option>
                                    @foreach($donaciones as $d)
                                    <option value="{{ $d->donacionid }}" 
                                            data-monto="{{ $d->monto }}"
                                            data-donante="{{ $d->donante->nombre ?? 'Anónimo' }}">
                                        Donación #{{ $d->donacionid }} — 
                                        {{ number_format($d->monto, 2) }} Bs — 
                                        {{ $d->donante->nombre ?? 'Anónimo' }}
                                    </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Seleccione la donación que desea asignar a esta asignación
                                </small>
                            </div>

                            {{-- Información de la donación seleccionada --}}
                            <div id="info-donacion" class="alert alert-info" style="display: none;">
                                <h5><i class="icon fas fa-info-circle"></i> Información de la Donación</h5>
                                <p class="mb-1">
                                    <strong>Donante:</strong> <span id="donante-nombre"></span>
                                </p>
                                <p class="mb-0">
                                    <strong>Monto disponible:</strong> 
                                    <span id="monto-disponible" class="badge badge-success"></span>
                                </p>
                            </div>

                            {{-- Monto a Asignar --}}
                            <div class="form-group">
                                <label for="montoasignado">
                                    <i class="fas fa-money-bill-wave"></i> Monto a Asignar (Bs) *
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           step="0.01" 
                                           class="form-control" 
                                           name="montoasignado" 
                                           id="montoasignado"
                                           placeholder="0.00"
                                           required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">Bs</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    El monto debe ser menor o igual al monto disponible de la donación
                                </small>
                            </div>

                            {{-- Alerta de validación --}}
                            <div id="alerta-monto" class="alert alert-warning" style="display: none;">
                                <i class="icon fas fa-exclamation-triangle"></i>
                                <span id="mensaje-alerta"></span>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success" id="btnAsignar">
                                <i class="fas fa-check"></i> Asignar Donación
                            </button>
                            <a href="{{ route('asignaciones.show', $asignacion->asignacionid) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PANEL DE AYUDA --}}
            <div class="col-lg-4">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-question-circle"></i> Información
                        </h3>
                    </div>
                    <div class="card-body">
                        <h5><i class="fas fa-info-circle text-info"></i> ¿Cómo asignar?</h5>
                        <ol class="pl-3">
                            <li>Selecciona una donación disponible</li>
                            <li>Ingresa el monto a asignar</li>
                            <li>Verifica que el monto no exceda el disponible</li>
                            <li>Confirma la asignación</li>
                        </ol>

                        <hr>

                        <h5><i class="fas fa-exclamation-triangle text-warning"></i> Importante</h5>
                        <ul class="pl-3">
                            <li>El monto debe ser positivo</li>
                            <li>No puede exceder el monto disponible</li>
                            <li>Una vez asignada, la acción no se puede revertir directamente</li>
                        </ul>

                        <hr>

                        <div class="callout callout-success">
                            <h5><i class="fas fa-lightbulb"></i> Tip</h5>
                            <p class="mb-0">Puedes asignar parcialmente una donación a múltiples asignaciones.</p>
                        </div>
                    </div>
                </div>

                {{-- DETALLES DE LA ASIGNACIÓN --}}
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Detalles de la Asignación
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-5">Campaña:</dt>
                            <dd class="col-sm-7">{{ $asignacion->campania->titulo ?? 'N/D' }}</dd>

                            <dt class="col-sm-5">Descripción:</dt>
                            <dd class="col-sm-7">{{ $asignacion->descripcion }}</dd>

                            <dt class="col-sm-5">Monto Total:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-success">
                                    {{ number_format($asignacion->monto, 2) }} Bs
                                </span>
                            </dd>

                            <dt class="col-sm-5">Fecha:</dt>
                            <dd class="col-sm-7">
                                {{ \Carbon\Carbon::parse($asignacion->fechaasignacion)->format('d/m/Y H:i') }}
                            </dd>

                            <dt class="col-sm-5">Usuario:</dt>
                            <dd class="col-sm-7">{{ $asignacion->usuario->email ?? 'N/D' }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccione una donación'
    });

    let montoDisponible = 0;

    // Cuando se selecciona una donación
    $('#donacionid').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const monto = parseFloat(selectedOption.data('monto')) || 0;
        const donante = selectedOption.data('donante') || 'Anónimo';

        if ($(this).val()) {
            montoDisponible = monto;
            $('#donante-nombre').text(donante);
            $('#monto-disponible').text(monto.toFixed(2) + ' Bs');
            $('#info-donacion').fadeIn();
            $('#montoasignado').attr('max', monto);
        } else {
            $('#info-donacion').fadeOut();
            $('#alerta-monto').fadeOut();
            montoDisponible = 0;
        }
    });

    // Validar monto al escribir
    $('#montoasignado').on('input', function() {
        const montoAsignado = parseFloat($(this).val()) || 0;
        
        if (montoAsignado > montoDisponible) {
            $('#mensaje-alerta').text('El monto asignado (' + montoAsignado.toFixed(2) + ' Bs) excede el monto disponible (' + montoDisponible.toFixed(2) + ' Bs)');
            $('#alerta-monto').fadeIn();
            $('#btnAsignar').prop('disabled', true);
        } else if (montoAsignado <= 0) {
            $('#mensaje-alerta').text('El monto debe ser mayor a 0');
            $('#alerta-monto').fadeIn();
            $('#btnAsignar').prop('disabled', true);
        } else {
            $('#alerta-monto').fadeOut();
            $('#btnAsignar').prop('disabled', false);
        }
    });

    // Validación antes de enviar
    $('#formAsignar').on('submit', function(e) {
        const donacionId = $('#donacionid').val();
        const montoAsignado = parseFloat($('#montoasignado').val()) || 0;

        if (!donacionId) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Debe seleccionar una donación'
            });
            return false;
        }

        if (montoAsignado <= 0 || montoAsignado > montoDisponible) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Monto inválido',
                text: 'El monto debe ser mayor a 0 y no exceder el monto disponible'
            });
            return false;
        }

        // Confirmación
        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar asignación?',
            html: `Se asignarán <strong>${montoAsignado.toFixed(2)} Bs</strong> de la donación #${donacionId}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, asignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
});
</script>
@endpush

@endsection