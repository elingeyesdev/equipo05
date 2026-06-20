@extends('layouts.app')

@section('title', 'Alimentación y cuidados — Rescate')
@section('subtitle', 'Registros de alimentación agrupados por animal.')
@section('content_header_title', 'Alimentación')
@section('content_header_subtitle', 'Vista agrupada')

@section('content_body')

    <div class="card res-list-card res-accent-success">
        <div class="card-header res-card-header--actions-only">
            <a href="{{ route('rescate.animal-feeding-records.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i> Nuevo registro
            </a>
        </div>
        <div class="card-body">
                        @if($groupedFeedings->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se encontraron registros de alimentación.') }}
                            </div>
                        @else
                            @foreach($groupedFeedings as $animalFileId => $feedings)
                                @php
                                    $firstFeeding = $feedings->first();
                                    $animalFile = $firstFeeding->care?->animalFile;
                                    $animal = $animalFile?->animal;
                                    
                                    if ($animalFileId === 'sin_animal') {
                                        $animalName = 'Alimentaciones sin animal asignado';
                                        $animalImage = rescate_media_url(null, 'fauna');
                                        $species = '-';
                                        $status = '-';
                                        $showAnimalInfo = false;
                                    } else {
                                        $animalName = $animal?->nombre ?? ('Animal ' . ($animalFile?->animal_id ?? '-'));
                                        $animalImage = $animalFile?->imagen_url 
                                            ? rescate_media_url($animalFile->imagen_url, rescate_media_seed($animalFile))
                                            : rescate_media_url(null, rescate_media_seed($animalFile));
                                        $species = $animalFile?->species?->nombre ?? '-';
                                        $status = $animalFile?->animalStatus?->nombre ?? '-';
                                        $showAnimalInfo = true;
                                    }
                                @endphp
                                <div class="card res-group-card mb-4">
                                    <div class="card-body">
                                        <div class="row align-items-start">
                                            {{-- Foto del animal a la izquierda --}}
                                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                                <img src="{{ $animalImage }}" alt="{{ $animalName }}" class="res-group-photo">
                                            </div>
                                            {{-- Información del animal a la derecha --}}
                                            <div class="col-md-9">
                                                <h5 class="mb-3">
                                                    <strong>{{ $animalName }}</strong>
                                                    @if($showAnimalInfo && $animalFile)
                                                        <span class="badge badge-info ml-2">Hoja N°{{ $animalFile->id }}</span>
                                                    @endif
                                                </h5>
                                                @if($showAnimalInfo)
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <small class="text-muted"><strong>Especie:</strong> {{ $species }}</small>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted"><strong>Estado:</strong> {{ $status }}</small>
                                                    </div>
                                                </div>
                                                @endif
                                                
                                                {{-- Tabla de alimentaciones --}}
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th class="text-center">{{ __('Tipo de Alimentación') }}</th>
                                                                <th class="text-center">{{ __('Frecuencia') }}</th>
                                                                <th class="text-center">{{ __('Porción') }}</th>
                                                                <th class="text-center">Detalle</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($feedings as $index => $careFeeding)
                                                                <tr>
                                                                    <td class="text-center">{{ $careFeeding->feedingType?->nombre ?? '-' }}</td>
                                                                    <td class="text-center">{{ $careFeeding->feedingFrequency?->nombre ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        @php($p = $careFeeding->feedingPortion)
                                                                        {{ $p ? ($p->cantidad.' '.$p->unidad) : '-' }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="btn-group btn-group-sm" role="group">
                                                                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.care-feedings.show', $careFeeding->id) }}" title="Ver detalle">
                                                                                <i class="fa fa-fw fa-eye"></i> Ver
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
        </div>
    </div>
@endsection
