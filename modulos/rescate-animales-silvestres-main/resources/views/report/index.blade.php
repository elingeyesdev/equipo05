@extends('layouts.app')

@section('title', 'Hallazgos — Rescate')
@section('subtitle', 'Reportes de campo, filtros y seguimiento.')
@section('content_header_title', 'Hallazgos / reportes')
@section('content_header_subtitle', 'Listado')

@section('content_body')

    <div class="card res-list-card res-accent-warning">
        <div class="card-header res-card-header--actions-only">
            <div class="res-card-header-actions d-flex flex-wrap gap-2">
                @canApproveRescateReports
                <a href="{{ route('rescate.reports.mapa-campo') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-map-marked-alt mr-1"></i> {{ __('Mapa de Campo') }}
                </a>
                @endcanApproveRescateReports
                <a href="{{ route('rescate.reports.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i> {{ __('Crear nuevo') }}
                </a>
            </div>
        </div>

        <div class="card-body">
            <form method="GET" class="res-filter-bar js-auto-filter-form">
                            <div class="form-row">
                                <div class="{{ \App\Support\AccessControl::canApproveRescateReports() ? 'col-md-3' : 'col-md-4' }}">
                                    <label class="mb-1">
                                        {{ __('Urgencia') }}
                                        <button type="button" class="btn btn-link btn-sm p-0 ml-1 align-baseline" data-toggle="tooltip" title="{{ __('Qué tan pronto se debe rescatar al animal. 1–2: Baja (situación estable), 3: Media (requiere seguimiento), 4–5: Alta (atención rápida).') }}">¿{{ __('Qué es urgencia') }}?</button>
                                    </label>
                                    <select name="urgencia_nivel" class="form-control">
                                        <option value="">{{ __('Todas') }}</option>
                                        <option value="alta" {{ request('urgencia_nivel')==='alta'?'selected':'' }}>{{ __('Alta') }}</option>
                                        <option value="media" {{ request('urgencia_nivel')==='media'?'selected':'' }}>{{ __('Media') }}</option>
                                        <option value="baja" {{ request('urgencia_nivel')==='baja'?'selected':'' }}>{{ __('Baja') }}</option>
                                    </select>
                                </div>
                                @canApproveRescateReports
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Reportante') }}</label>
                                    <select name="persona_id" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach(($reporters ?? []) as $p)
                                            <option value="{{ $p->id }}" {{ (string)$p->id === (string)request('persona_id') ? 'selected' : '' }}>
                                                {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endcanApproveRescateReports
                                <div class="{{ \App\Support\AccessControl::canApproveRescateReports() ? 'col-md-3' : 'col-md-4' }}">
                                    <label class="mb-1">{{ __('Tipo de incidente') }}</label>
                                    <select name="tipo_incidente_id" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        @foreach(($incidentTypes ?? []) as $it)
                                            <option value="{{ $it->id }}" {{ (string)$it->id === (string)request('tipo_incidente_id') ? 'selected' : '' }}>
                                                {{ $it->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="{{ \App\Support\AccessControl::canApproveRescateReports() ? 'col-md-3' : 'col-md-4' }}">
                                    <label class="mb-1">{{ __('Aprobado') }}</label>
                                    <select name="aprobado" class="form-control">
                                        <option value="">{{ __('Todos') }}</option>
                                        <option value="1" {{ request('aprobado')==='1'?'selected':'' }}>{{ __('Sí') }}</option>
                                        <option value="0" {{ request('aprobado')==='0'?'selected':'' }}>{{ __('No') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary btn-sm mr-3">{{ __('Buscar') }}</button>
                                <a href="{{ route('rescate.reports.index') }}" class="btn btn-link p-0">{{ __('Mostrar todos') }}</a>
                            </div>
                        </form>

                        <div class="row res-card-grid">
                            @foreach ($reports as $report)
                                @php
                                    $urg = $report->urgencia;
                                    if (is_numeric($urg)) {
                                        if ($urg >= 4) { $urgClass = 'danger'; }
                                        elseif ($urg == 3) { $urgClass = 'warning'; }
                                        else { $urgClass = 'info'; }
                                    } else {
                                        $urgClass = 'secondary';
                                    }
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card res-entity-card">
                                        @include('fusion.modulos.partials.rescate-entity-photo', [
                                            'path' => $report->imagen_url,
                                            'seed' => rescate_report_media_seed($report),
                                            'alt' => 'Hallazgo #'.$report->id,
                                            'badge' => $report->incidentType?->nombre,
                                        ])
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $report->condicionInicial?->nombre }}">
                                                <i class="fas fa-clipboard-list text-primary mr-2"></i>
                                                {{ \Illuminate\Support\Str::limit($report->condicionInicial?->nombre ?? __('Condición no especificada'), 26) }}
                                            </h3>
                                            <div class="card-tools d-flex align-items-center">
                                                <!--<i class="fas fa-exclamation-circle text-{{ $urgClass }} mr-1"></i>-->
                                                <span class="small text-muted mr-1">{{ __('Urgencia') }}:</span>
                                                <span class="badge badge-{{ $urgClass }}" title="{{ __('Urgencia') }}">
                                                    {{ is_null($urg) ? __('N/A') : $urg }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-unbordered mb-0">
                                                <li class="list-group-item">
                                                    <i class="fas fa-exclamation-triangle text-muted mr-2"></i>
                                                    <b>{{ __('Incidente:') }}</b>
                                                    <span class="float-right">{{ $report->incidentType?->nombre ?? '-' }}</span>
                                                </li>
                                                @if($report->direccion)
                                                <li class="list-group-item">
                                                    <i class="fas fa-map-marker-alt text-muted mr-2"></i>
                                                    <b>{{ __('Ubicación:') }}</b>
                                                    <span class="float-right text-right" style="max-width: 58%;">{{ \Illuminate\Support\Str::limit($report->direccion, 42) }}</span>
                                                </li>
                                                @endif
                                                <li class="list-group-item">
                                                    <i class="fas fa-{{ (int)$report->aprobado === 1 ? 'check-circle' : 'clock' }} text-muted mr-2"></i>
                                                    <b>{{ __('Aprobado:') }}</b>
                                                    <span class="float-right">
                                                        @if((int)$report->aprobado === 1)
                                                            <span class="badge badge-success">{{ __('Sí') }}</span>
                                                        @else
                                                            <span class="badge badge-warning">{{ __('No') }}</span>
                                                        @endif
                                                    </span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="fas fa-info-circle text-muted mr-2"></i>
                                                    <b>{{ __('Estado:') }}</b>
                                                    <span class="float-right">
                                                        <span class="badge {{ $report->getEstadoBadgeClass() }}">{{ $report->getEstado() }}</span>
                                                    </span>
                                                </li>
                                                @if($report->firstTransfer?->center)
                                                <li class="list-group-item">
                                                    <i class="fas fa-hospital text-muted mr-2"></i>
                                                    <b>{{ __('Traslado a:') }}</b>
                                                    <span class="float-right">{{ \Illuminate\Support\Str::limit($report->firstTransfer->center->nombre, 20) }}</span>
                                                </li>
                                                @endif
                                                <li class="list-group-item">
                                                    <i class="fas fa-calendar-alt text-muted mr-2"></i>
                                                    <b>{{ __('Fecha:') }}</b>
                                                    <span class="float-right">{{ optional($report->created_at)->format('d/m/Y') }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            @php
                                                $isOnlyCitizen = \App\Support\AccessControl::isOnlyRescateCitizen();
                                            @endphp
                                            @if($isOnlyCitizen)
                                            <div class="d-flex w-100">
                                                <a class="btn btn-primary btn-sm flex-fill" href="{{ route('rescate.reports.show', $report->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                                </a>
                                                @if((int)$report->aprobado === 1 && $report->latitud && $report->longitud && \App\Support\AccessControl::canApproveRescateReports())
                                                <a class="btn btn-info btn-sm flex-fill ml-2" href="{{ route('rescate.reports.mapa-campo', ['report' => $report->id]) }}" title="{{ __('Ver en mapa') }}">
                                                    <i class="fas fa-map-marked-alt"></i>
                                                </a>
                                                @endif
                                            </div>
                                            @else
                                            <form action="{{ route('rescate.reports.destroy', $report->id) }}" method="POST" class="mb-0 d-flex w-100">
                                                @csrf
                                                @method('DELETE')
                                                <a class="btn btn-primary btn-sm" href="{{ route('rescate.reports.show', $report->id) }}">
                                                    <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                                </a>
                                                @if((int)$report->aprobado === 1 && $report->latitud && $report->longitud && \App\Support\AccessControl::canApproveRescateReports())
                                                <a class="btn btn-info btn-sm" href="{{ route('rescate.reports.mapa-campo', ['report' => $report->id]) }}" title="{{ __('Ver en mapa') }}">
                                                    <i class="fas fa-map-marked-alt"></i>
                                                </a>
                                                @endif
                                                @canApproveRescateReports
                                                <button type="button" 
                                                        class="btn btn-success btn-sm {{ (int)$report->aprobado === 1 ? 'disabled' : '' }}" 
                                                        data-toggle="modal" 
                                                        data-target="#modalAprobarReport{{ $report->id }}"
                                                        {{ (int)$report->aprobado === 1 ? 'disabled' : '' }}
                                                        title="{{ (int)$report->aprobado === 1 ? __('Este hallazgo ya está aprobado') : __('Aprobar o rechazar este hallazgo') }}">
                                                    <i class="fa fa-fw fa-check"></i> {{ __('Aprobar') }}
                                                </button>
                                                @endcanApproveRescateReports
                                                @canDeleteRescateReports
                                                <button type="button" class="btn btn-danger btn-sm js-confirm-delete">
                                                    <i class="fa fa-fw fa-trash"></i> {{ __('Eliminar') }}
                                                </button>
                                                @endcanDeleteRescateReports
                                            </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($reports->isEmpty())
                            <div class="res-empty-state">
                                <i class="fas fa-binoculars fa-2x mb-2 d-block text-muted"></i>
                                {{ __('No se ha registrado ningún hallazgo todavía.') }}
                            </div>
                        @endif
        </div>
        @if($reports->hasPages())
        <div class="card-footer">
            {!! $reports->withQueryString()->links('pagination::bootstrap-4') !!}
        </div>
        @endif
    </div>

    {{-- Modales de aprobación para cada reporte --}}
    @foreach ($reports as $report)
        @canApproveRescateReports
        <div class="modal fade" id="modalAprobarReport{{ $report->id }}" tabindex="-1" role="dialog" aria-labelledby="modalAprobarReport{{ $report->id }}Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAprobarReport{{ $report->id }}Label">
                            <i class="fa fa-check-circle"></i> {{ __('Aprobar/Rechazar Hallazgo') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('rescate.reports.approve', $report->id) }}" method="POST" id="formAprobarReport{{ $report->id }}">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            <p class="mb-0">{{ __('¿Desea aprobar o rechazar este hallazgo?') }}</p>
                            <input type="hidden" name="action" id="actionReport{{ $report->id }}" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" id="btnRechazarReport{{ $report->id }}">
                                <i class="fa fa-times-circle"></i> {{ __('Rechazar') }}
                            </button>
                            <button type="button" class="btn btn-success" id="btnAprobarReport{{ $report->id }}">
                                <i class="fa fa-check-circle"></i> {{ __('Aprobar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endcanApproveRescateReports
    @endforeach
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.querySelector('form.js-auto-filter-form');
        if (form) {
            var applyBtn = form.querySelector('button[type="submit"]');
            applyBtn && applyBtn.addEventListener('click', function(){ /* submit explicit */ });
        }
        if (window.$ && typeof window.$.fn.tooltip === 'function') {
            window.$('[data-toggle="tooltip"]').tooltip();
        }
        
        // Prevenir que se abra el modal si el reporte ya está aprobado
        document.querySelectorAll('[data-target^="#modalAprobarReport"]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (this.disabled || this.classList.contains('disabled')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        });

        // Manejar aprobación/rechazo de reportes
        @foreach ($reports as $report)
            @canApproveRescateReports
            (function() {
                var form = document.getElementById('formAprobarReport{{ $report->id }}');
                var actionInput = document.getElementById('actionReport{{ $report->id }}');
                var btnRechazar = document.getElementById('btnRechazarReport{{ $report->id }}');
                var btnAprobar = document.getElementById('btnAprobarReport{{ $report->id }}');
                
                function submitForm(action) {
                    // Establecer el valor de action
                    if (actionInput) {
                        actionInput.value = action;
                    }
                    
                    // Deshabilitar botones para evitar doble envío
                    if (btnRechazar) btnRechazar.disabled = true;
                    if (btnAprobar) btnAprobar.disabled = true;
                    
                    // Enviar formulario
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
            })();
            @endcanApproveRescateReports
        @endforeach
    });
    </script>
@endsection
