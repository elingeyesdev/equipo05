@extends('layouts.app')

@section('title', 'Nueva hoja de vida — Rescate')
@section('subtitle', 'Asociar un hallazgo aprobado con animal y ficha.')
@section('content_header_title', 'Hojas de vida')
@section('content_header_subtitle', 'Registro transaccional')

@section('content_body')

    <div class="card res-list-card res-accent-success">
        <div class="card-header">
            <h3 class="res-card-title mb-0"><i class="fas fa-clipboard-check text-success mr-2"></i>{{ __('Registrar hoja de vida') }}</h3>
            <a href="{{ route('rescate.animal-files.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-list mr-1"></i> {{ __('Ver listado') }}
            </a>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="font-weight-bold mb-1">{{ __('No se pudo registrar la hoja. Revisa los errores:') }}</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('rescate.animal-records.store') }}" enctype="multipart/form-data" id="animal-record-form">
                @csrf
                <input type="hidden" name="reporte_id" id="reporte_id" value="{{ old('reporte_id') }}">

                <div class="res-wizard-steps">
                    <span class="res-wizard-step is-active" id="wizard-step-1">
                        <i class="fas fa-binoculars"></i> {{ __('1. Elegir hallazgo') }}
                    </span>
                    <span class="res-wizard-step" id="wizard-step-2">
                        <i class="fas fa-paw"></i> {{ __('2. Datos del animal') }}
                    </span>
                </div>

                <div id="step1">
                    <p class="text-muted mb-3">
                        {{ __('Selecciona un hallazgo aprobado que aún no tenga hoja de vida asociada. Al elegirlo podrás completar la ficha del animal.') }}
                    </p>

                    @if(($reportCards ?? collect())->isEmpty())
                        <div class="res-empty-state">
                            <i class="fas fa-binoculars fa-2x mb-2 d-block text-muted"></i>
                            {{ __('No hay hallazgos aprobados disponibles sin asignar.') }}
                            <div class="mt-2">
                                <a href="{{ route('rescate.reports.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Ir a hallazgos') }}</a>
                            </div>
                        </div>
                    @else
                        <div class="row res-card-grid" id="report_cards">
                            @foreach($reportCards as $report)
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
                                    <div class="card res-entity-card res-selectable-card report-card"
                                         role="button"
                                         tabindex="0"
                                         data-report-id="{{ $report->id }}"
                                         data-cond-id="{{ $report->condicion_inicial_id }}"
                                         data-cond-name="{{ $report->condicionInicial?->nombre }}"
                                         data-obs="{{ e($report->observaciones ?? '') }}"
                                         data-label="{{ __('Hallazgo N°:id', ['id' => $report->id]) }}">
                                        @include('fusion.modulos.partials.rescate-entity-photo', [
                                            'path' => $report->imagen_url,
                                            'seed' => rescate_report_media_seed($report),
                                            'alt' => 'Hallazgo #'.$report->id,
                                            'badge' => $report->incidentType?->nombre,
                                        ])
                                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                                            <h3 class="card-title mb-0 small font-weight-bold">
                                                {{ __('Hallazgo N°:id', ['id' => $report->id]) }}
                                            </h3>
                                            <span class="badge badge-{{ $urgClass }}">{{ __('Urgencia') }}: {{ is_null($urg) ? 'N/A' : $urg }}</span>
                                        </div>
                                        <div class="card-body py-2">
                                            <div class="res-meta-row">
                                                <span>{{ __('Condición') }}</span>
                                                <strong>{{ $report->condicionInicial?->nombre ?? '-' }}</strong>
                                            </div>
                                            <div class="res-meta-row">
                                                <span>{{ __('Reportante') }}</span>
                                                <strong>{{ $report->person?->nombre ?? '-' }}</strong>
                                            </div>
                                            @if($report->direccion)
                                                <div class="res-meta-row">
                                                    <span>{{ __('Ubicación') }}</span>
                                                    <strong>{{ \Illuminate\Support\Str::limit($report->direccion, 28) }}</strong>
                                                </div>
                                            @endif
                                            <div class="res-select-hint mt-2 text-center">
                                                <i class="fas fa-hand-pointer mr-1"></i>{{ __('Clic para seleccionar') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div id="step2" class="d-none">
                    <div class="res-selected-report-banner is-visible" id="selected-report-banner">
                        <div>
                            <strong id="selected-report-label">{{ __('Hallazgo seleccionado') }}</strong>
                            <div class="small text-muted" id="selected-report-meta"></div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_change_report">
                            <i class="fas fa-exchange-alt mr-1"></i>{{ __('Cambiar hallazgo') }}
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <div class="res-form-section">
                                <div class="res-form-section-title">{{ __('Datos del animal') }}</div>
                                @include('animal.form', [
                                    'animal' => $animal ?? null,
                                    'reports' => $reports ?? [],
                                    'showSubmit' => false,
                                    'showName' => true,
                                    'hideInitialState' => true,
                                    'skipReportField' => true,
                                ])
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="res-form-section">
                                <div class="res-form-section-title">{{ __('Ficha de custodia') }}</div>
                                <div class="form-group mb-2">
                                    <label for="estado_inicial_nombre" class="form-label">{{ __('Estado inicial del hallazgo') }}</label>
                                    <input type="text" class="form-control" id="estado_inicial_nombre" value="" readonly>
                                    <input type="hidden" name="estado_inicial_id" id="estado_inicial_id" value="">
                                </div>
                                <div class="form-group mb-2">
                                    <label for="estado_id" class="form-label">{{ __('Estado actual') }} <span class="text-danger">*</span></label>
                                    <select name="estado_id" id="estado_id" class="form-control @error('estado_id') is-invalid @enderror">
                                        @foreach(collect($animalStatuses ?? [])->sortBy('nombre') as $st)
                                            <option value="{{ $st->id }}" {{ (string)old('estado_id') === (string)$st->id ? 'selected' : ((empty(old('estado_id')) && (string)$defaultStatusId === (string)$st->id) ? 'selected' : '') }}>{{ $st->nombre }}</option>
                                        @endforeach
                                    </select>
                                    {!! $errors->first('estado_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
                                </div>
                                @include('animal-file.form', [
                                    'animalFile' => $animalFile ?? null,
                                    'species' => $species ?? [],
                                    'animalStatuses' => $animalStatuses ?? [],
                                    'showAnimalSelect' => false,
                                    'showSubmit' => false,
                                    'hideState' => true,
                                ])
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex flex-wrap" style="gap: 0.5rem;">
                        <a href="{{ route('rescate.animal-files.index') }}" class="btn btn-outline-secondary">{{ __('Cancelar') }}</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> {{ __('Guardar hoja de vida') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('animal-record-form');
    const reportInput = document.getElementById('reporte_id');
    const cards = document.querySelectorAll('.report-card');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const wizardStep1 = document.getElementById('wizard-step-1');
    const wizardStep2 = document.getElementById('wizard-step-2');
    const preview = document.getElementById('preview-animalfile-imagen');
    const estadoSelect = document.getElementById('estado_id');
    const descField = document.getElementById('descripcion');
    const selectedLabel = document.getElementById('selected-report-label');
    const selectedMeta = document.getElementById('selected-report-meta');
    const changeReportBtn = document.getElementById('btn_change_report');

    function showStep2() {
        step2.classList.remove('d-none');
        wizardStep1.classList.remove('is-active');
        wizardStep1.classList.add('is-done');
        wizardStep2.classList.add('is-active');
        step2.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function resetSelection() {
        reportInput.value = '';
        cards.forEach(function (card) { card.classList.remove('is-selected'); });
        step2.classList.add('d-none');
        wizardStep1.classList.add('is-active');
        wizardStep1.classList.remove('is-done');
        wizardStep2.classList.remove('is-active');
        step1.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function selectCard(card) {
        const id = card.getAttribute('data-report-id');
        if (!id) return;

        reportInput.value = id;
        cards.forEach(function (c) { c.classList.remove('is-selected'); });
        card.classList.add('is-selected');

        const condName = card.getAttribute('data-cond-name') || '';
        const condId = card.getAttribute('data-cond-id') || '';
        const obs = card.getAttribute('data-obs') || '';
        const label = card.getAttribute('data-label') || ('Hallazgo N°' + id);

        document.getElementById('estado_inicial_nombre').value = condName;
        document.getElementById('estado_inicial_id').value = condId;

        if (selectedLabel) selectedLabel.textContent = label;
        if (selectedMeta) selectedMeta.textContent = condName ? ('Condición: ' + condName) : '';

        if (estadoSelect && condName) {
            const opts = Array.from(estadoSelect.options || []);
            const found = opts.find(function (o) {
                return String(o.textContent || '').trim().toLowerCase() === condName.toLowerCase();
            });
            if (found) estadoSelect.value = found.value;
        }

        if (descField && (!descField.value || descField.value.trim() === '') && obs) {
            descField.value = obs;
        }

        const img = card.querySelector('img');
        if (img && preview) {
            preview.src = img.src;
            preview.style.display = '';
        }

        showStep2();
    }

    cards.forEach(function (card) {
        card.addEventListener('click', function () { selectCard(card); });
        card.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                selectCard(card);
            }
        });
    });

    changeReportBtn?.addEventListener('click', resetSelection);

    const oldReportId = reportInput.value;
    if (oldReportId) {
        const preselected = document.querySelector('.report-card[data-report-id="' + oldReportId + '"]');
        if (preselected) selectCard(preselected);
    }

    form?.addEventListener('submit', function (event) {
        if (!reportInput.value) {
            event.preventDefault();
            alert(@json(__('Selecciona un hallazgo antes de guardar.')));
            resetSelection();
        }
    });
});
</script>
@endsection
