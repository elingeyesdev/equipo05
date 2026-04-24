@extends('layouts.app')

@section('title', 'Asignar Donaciones')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-hand-holding-usd"></i> Asignar Donaciones
                </h1>
                <small class="text-muted">
                    Asignación #{{ $asignacion->asignacionid }} — 
                    Campaña: {{ optional($asignacion->campania)->titulo ?? 'N/A' }}
                </small>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('asignaciones.index') }}">Asignaciones</a></li>
                    <li class="breadcrumb-item active">Asignar Donaciones</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- ALERTAS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="icon fas fa-check"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Revisa los errores:</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ESTADÍSTICAS --}}
        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ number_format($asignacion->monto ?? 0, 2) }}</h3>
                        <p>Total Asignación (Bs)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ number_format($yaAsignado ?? 0, 2) }}</h3>
                        <p>Ya Asignado (Bs)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($faltante ?? 0, 2) }}</h3>
                        <p>Faltante (Bs)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
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
                            <i class="fas fa-hand-holding-heart"></i> Nueva Asignación de Donación
                        </h3>
                    </div>

                    <form action="{{ route('asignaciones.asignar.store', $asignacion->asignacionid) }}" 
                          method="POST" 
                          id="formAsignar">
                        @csrf

                        <div class="card-body">
                            {{-- SELECCIÓN DE DONACIÓN --}}
                            <div class="form-group">
                                <label for="donacionid">
                                    <i class="fas fa-gift"></i> 
                                    Donación con Saldo Disponible 
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="donacionid" 
                                        id="donacionid" 
                                        class="form-control select2" 
                                        style="width: 100%;" 
                                        required>
                                    <option value="">-- Seleccione una donación --</option>
                                    @foreach($saldos as $s)
                                        <option value="{{ $s->donacionid }}" 
                                                data-saldo="{{ $s->saldodisponible }}"
                                                {{ old('donacionid') == $s->donacionid ? 'selected' : '' }}>
                                            Donación #{{ $s->donacionid }} — 
                                            Saldo: Bs {{ number_format($s->saldodisponible, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('donacionid') 
                                    <small class="text-danger">{{ $message }}</small> 
                                @enderror
                            </div>

                            {{-- INFORMACIÓN DINÁMICA --}}
                            <div id="info-saldo" class="alert alert-info" style="display: none;">
                                <h5><i class="icon fas fa-info-circle"></i> Información de la Donación</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-0">
                                            <strong>Saldo Disponible:</strong> 
                                            <span id="saldo-badge" class="badge badge-success"></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-0">
                                            <strong>Máximo a Asignar:</strong> 
                                            <span id="maximo-badge" class="badge badge-warning"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- CAMPOS DE MONTO --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="montoasignado">
                                            <i class="fas fa-money-bill-wave"></i> 
                                            Monto a Asignar 
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0.01" 
                                                   name="montoasignado" 
                                                   id="montoasignado"
                                                   class="form-control @error('montoasignado') is-invalid @enderror"
                                                   placeholder="0.00"
                                                   value="{{ old('montoasignado') }}" 
                                                   required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">Bs</span>
                                            </div>
                                        </div>
                                        <small class="text-muted" id="ayudaMonto">
                                            El monto no debe superar el saldo de la donación ni el faltante.
                                        </small>
                                        @error('montoasignado') 
                                            <span class="invalid-feedback d-block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-wallet"></i> Saldo de la Donación
                                        </label>
                                        <input type="text" 
                                               id="saldoSeleccionado" 
                                               class="form-control" 
                                               value="—" 
                                               readonly>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>
                                            <i class="fas fa-exclamation-circle"></i> Faltante de la Asignación
                                        </label>
                                        <input type="text" 
                                               id="faltanteAsignacion" 
                                               class="form-control"
                                               value="Bs {{ number_format($faltante ?? 0, 2) }}" 
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            {{-- ALERTA DE VALIDACIÓN --}}
                            <div id="alerta-validacion" class="alert alert-warning" style="display: none;">
                                <i class="icon fas fa-exclamation-triangle"></i>
                                <span id="mensaje-validacion"></span>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success" id="btnAsignar">
                                <i class="fas fa-check"></i> Asignar Donación
                            </button>
                            <a href="{{ route('asignaciones.detalles', $asignacion->asignacionid) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PANEL DE INFORMACIÓN --}}
            <div class="col-lg-4">
                {{-- GUÍA --}}
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-question-circle"></i> Guía de Asignación
                        </h3>
                    </div>
                    <div class="card-body">
                        <h5><i class="fas fa-list-ol text-info"></i> Pasos a seguir:</h5>
                        <ol class="pl-3">
                            <li>Selecciona una donación con saldo disponible</li>
                            <li>Verifica el saldo y el faltante</li>
                            <li>Ingresa el monto a asignar</li>
                            <li>Confirma la asignación</li>
                        </ol>

                        <hr>

                        <h5><i class="fas fa-exclamation-triangle text-warning"></i> Importante:</h5>
                        <ul class="pl-3">
                            <li>El monto debe ser mayor a 0</li>
                            <li>No puede exceder el saldo disponible</li>
                            <li>No puede exceder el faltante de la asignación</li>
                            <li>Se actualizará el saldo automáticamente</li>
                        </ul>

                        <hr>

                        <div class="callout callout-success">
                            <h5><i class="fas fa-lightbulb"></i> Consejo</h5>
                            <p class="mb-0">
                                Puedes dividir una donación en múltiples asignaciones 
                                hasta agotar su saldo disponible.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- RESUMEN DE LA ASIGNACIÓN --}}
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-list"></i> Resumen de la Asignación
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-5">ID:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-info">
                                    #{{ $asignacion->asignacionid }}
                                </span>
                            </dd>

                            <dt class="col-sm-5">Campaña:</dt>
                            <dd class="col-sm-7">{{ optional($asignacion->campania)->titulo ?? 'N/A' }}</dd>

                            <dt class="col-sm-5">Descripción:</dt>
                            <dd class="col-sm-7">{{ $asignacion->descripcion }}</dd>

                            <dt class="col-sm-5">Total:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-primary">
                                    {{ number_format($asignacion->monto ?? 0, 2) }} Bs
                                </span>
                            </dd>

                            <dt class="col-sm-5">Ya Asignado:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-success">
                                    {{ number_format($yaAsignado ?? 0, 2) }} Bs
                                </span>
                            </dd>

                            <dt class="col-sm-5">Faltante:</dt>
                            <dd class="col-sm-7">
                                <span class="badge badge-warning">
                                    {{ number_format($faltante ?? 0, 2) }} Bs
                                </span>
                            </dd>

                            <dt class="col-sm-5">Progreso:</dt>
                            <dd class="col-sm-7">
                                @php
                                    $total = $asignacion->monto ?? 1;
                                    $porcentaje = $total > 0 ? (($yaAsignado ?? 0) / $total * 100) : 0;
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $porcentaje }}%">
                                        {{ number_format($porcentaje, 0) }}%
                                    </div>
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- DONACIONES DISPONIBLES --}}
                @if($saldos->count() > 0)
                <div class="card card-success collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-gift"></i> Donaciones Disponibles ({{ $saldos->count() }})
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($saldos->take(5) as $s)
                                <li class="list-group-item">
                                    <strong>#{{ $s->donacionid }}</strong>
                                    <span class="badge badge-success float-right">
                                        {{ number_format($s->saldodisponible, 2) }} Bs
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('donacionid');
    const saldoTxt = document.getElementById('saldoSeleccionado');
    const ayuda = document.getElementById('ayudaMonto');
    const montoInput = document.getElementById('montoasignado');
    const btnAsignar = document.getElementById('btnAsignar');
    const alertaValidacion = document.getElementById('alerta-validacion');
    const mensajeValidacion = document.getElementById('mensaje-validacion');
    const infoSaldo = document.getElementById('info-saldo');
    const saldoBadge = document.getElementById('saldo-badge');
    const maximoBadge = document.getElementById('maximo-badge');
    
    const faltante = {{ (float) ($faltante ?? 0) }};
    let saldoActual = 0;
    let maximoPermitido = 0;

    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        placeholder: 'Seleccione una donación'
    });

    function refrescar() {
        const opt = sel.options[sel.selectedIndex];
        saldoActual = parseFloat(opt?.getAttribute('data-saldo') || 0);
        
        if (sel.value && !isNaN(saldoActual)) {
            saldoTxt.value = 'Bs ' + saldoActual.toFixed(2);
            maximoPermitido = Math.max(0, Math.min(saldoActual, faltante));
            
            if (montoInput) {
                montoInput.max = maximoPermitido || '';
                ayuda.innerHTML = '<i class="fas fa-info-circle"></i> Máximo permitido: <strong>Bs ' + 
                                 (maximoPermitido ? maximoPermitido.toFixed(2) : '0.00') + '</strong>';
            }

            // Mostrar info
            saldoBadge.textContent = 'Bs ' + saldoActual.toFixed(2);
            maximoBadge.textContent = 'Bs ' + maximoPermitido.toFixed(2);
            $(infoSaldo).fadeIn();
        } else {
            saldoTxt.value = '—';
            $(infoSaldo).fadeOut();
            $(alertaValidacion).fadeOut();
            saldoActual = 0;
            maximoPermitido = 0;
        }

        validarMonto();
    }

    function validarMonto() {
        const monto = parseFloat(montoInput.value) || 0;
        
        if (!sel.value) {
            btnAsignar.disabled = true;
            return;
        }

        if (monto <= 0) {
            mensajeValidacion.innerHTML = '<strong>Error:</strong> El monto debe ser mayor a 0';
            $(alertaValidacion).fadeIn();
            btnAsignar.disabled = true;
        } else if (monto > saldoActual) {
            mensajeValidacion.innerHTML = '<strong>Error:</strong> El monto (' + monto.toFixed(2) + 
                                         ' Bs) excede el saldo disponible (' + saldoActual.toFixed(2) + ' Bs)';
            $(alertaValidacion).fadeIn();
            btnAsignar.disabled = true;
        } else if (monto > faltante) {
            mensajeValidacion.innerHTML = '<strong>Error:</strong> El monto (' + monto.toFixed(2) + 
                                         ' Bs) excede el faltante de la asignación (' + faltante.toFixed(2) + ' Bs)';
            $(alertaValidacion).fadeIn();
            btnAsignar.disabled = true;
        } else {
            $(alertaValidacion).fadeOut();
            btnAsignar.disabled = false;
        }
    }

    sel?.addEventListener('change', refrescar);
    montoInput?.addEventListener('input', validarMonto);

    // Confirmación al enviar
    document.getElementById('formAsignar')?.addEventListener('submit', function(e) {
        const monto = parseFloat(montoInput.value) || 0;
        const donacionId = sel.value;

        if (!donacionId || monto <= 0 || monto > maximoPermitido) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'Verifica que todos los datos sean correctos'
            });
            return false;
        }

        e.preventDefault();
        Swal.fire({
            title: '¿Confirmar asignación?',
            html: `Se asignarán <strong>${monto.toFixed(2)} Bs</strong> de la donación <strong>#${donacionId}</strong>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check"></i> Sí, asignar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    // Validación inicial si hay valor previo
    if (sel.value) {
        refrescar();
    }
});
</script>
@endpush

@endsection