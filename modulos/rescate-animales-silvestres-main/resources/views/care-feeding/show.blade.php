@extends('layouts.app')

@section('title', 'Detalle de alimentación — Rescate')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Alimentación')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-utensils"></i> Alimentación #{{ $careFeeding->id }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.care-feedings.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.care-feedings.edit', $careFeeding->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Cuidado:</strong>
                            #{{ $careFeeding->care_id }}
                            @if($careFeeding->care)
                                <span class="text-muted">({{ $careFeeding->care->animalFile?->animal?->nombre ?? 'animal' }})</span>
                            @endif
                        </div>
                        <div class="form-group mb-3">
                            <strong>Tipo de alimento:</strong>
                            {{ $careFeeding->feedingType?->nombre ?? ('#'.$careFeeding->feeding_type_id) }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Frecuencia:</strong>
                            {{ $careFeeding->feedingFrequency?->nombre ?? ('#'.$careFeeding->feeding_frequency_id) }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Porción:</strong>
                            @php($fp = $careFeeding->feedingPortion)
                            @if($fp)
                                {{ trim(trim(($fp->cantidad ?? '').' '.($fp->unidad ?? ''))) ?: ('#'.$careFeeding->feeding_portion_id) }}
                            @else
                                #{{ $careFeeding->feeding_portion_id }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
