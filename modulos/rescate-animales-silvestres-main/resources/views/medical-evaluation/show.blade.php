@extends('layouts.app')

@section('title', 'Detalle de evaluación médica — Rescate')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Evaluaciones médicas')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-notes-medical"></i> Evaluación #{{ $medicalEvaluation->id }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.medical-evaluations.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.medical-evaluations.edit', $medicalEvaluation->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Tratamiento:</strong>
                            {{ $medicalEvaluation->treatmentType?->nombre ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Descripción:</strong>
                            {{ $medicalEvaluation->descripcion ?: '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Fecha revisión:</strong>
                            {{ $medicalEvaluation->fecha ? \Carbon\Carbon::parse($medicalEvaluation->fecha)->format('d/m/Y') : '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Estado de salud anterior:</strong>
                            @php($prev = \Modules\Rescate\Models\AnimalHistory::where('animal_file_id', $medicalEvaluation->animal_file_id ?? null)
                                ->whereNotNull('valores_nuevos')
                                ->whereRaw("(valores_nuevos->'evaluacion_medica'->>'id')::text = ?", [(string)($medicalEvaluation->id ?? '')])
                                ->first())
                            {{ $prev?->valores_antiguos['estado']['nombre'] ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Veterinario:</strong>
                            {{ $medicalEvaluation->veterinarian?->person?->nombre ?? '—' }}
                            @if($medicalEvaluation->veterinarian?->especialidad)
                                <span class="text-muted"> ({{ $medicalEvaluation->veterinarian->especialidad }})</span>
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <strong>Animal:</strong>
                            {{ $medicalEvaluation->animalFile?->animal?->nombre ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Imagen de evaluación:</strong>
                            @if(!empty($medicalEvaluation?->imagen_url))
                                <div class="mt-2">
                                    <a href="{{ rescate_media_url($medicalEvaluation->imagen_url, 'evaluacion-'.$medicalEvaluation->id) }}" target="_blank" rel="noopener">
                                        <img src="{{ rescate_media_url($medicalEvaluation->imagen_url, 'evaluacion-'.$medicalEvaluation->id) }}" alt="Imagen evaluación" style="max-height:240px;">
                                    </a>
                                </div>
                            @else
                                —
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <strong>Imagen de llegada:</strong>
                            @php($foundImg = $medicalEvaluation->animalFile?->animal?->report?->imagen_url ?? null)
                            @if($foundImg)
                                <div class="mt-2">
                                    <a href="{{ rescate_media_url($foundImg, 'llegada') }}" target="_blank" rel="noopener">
                                        <img src="{{ rescate_media_url($foundImg, 'llegada') }}" alt="Imagen de llegada" style="max-height:240px;">
                                    </a>
                                </div>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
