@extends('layouts.app')

@section('title', 'Cuidados — Rescate')
@section('subtitle', 'Historial de cuidados agrupados por hoja de vida.')
@section('content_header_title', 'Cuidados registrados')
@section('content_header_subtitle', 'Vista agrupada')

@section('content_body')
<div class="container-fluid res-page-shell">
    @include('fusion.modulos.partials.rescate-module-nav')
    @include('fusion.modulos.partials.rescate-flash')

    <div class="card res-list-card res-accent-success">
        <div class="card-header">
            <h3 class="res-card-title mb-0"><i class="fas fa-heart text-success mr-2"></i>Registro de cuidados</h3>
            <a href="{{ route('rescate.animal-care-records.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i> Nuevo registro
            </a>
        </div>
        <div class="card-body">
                        @if($groupedCares->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i> {{ __('No se encontraron cuidados de animales.') }}
                            </div>
                        @else
                            @foreach($groupedCares as $animalFileId => $cares)
                                @php
                                    $firstCare = $cares->first();
                                    $animalFile = $firstCare->animalFile;
                                    $animal = $animalFile?->animal;
                                    
                                    if ($animalFileId === 'sin_animal') {
                                        $animalName = 'Cuidados sin animal asignado';
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
                                                
                                                {{-- Tabla de cuidados --}}
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th class="text-center">Tipo de Cuidado</th>
                                                                <th class="text-center">Descripción</th>
                                                                <th class="text-center">Fecha</th>
                                                                <th class="text-center">Detalle</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($cares as $index => $care)
                                                                <tr>
                                                                    <td class="text-center">{{ $care->careType?->nombre ?? '-' }}</td>
                                                                    <td class="text-center">
                                                                        @if($care->descripcion)
                                                                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $care->descripcion }}">
                                                                                {{ Str::limit($care->descripcion, 50) }}
                                                                            </span>
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">{{ $care->fecha ? \Carbon\Carbon::parse($care->fecha)->format('d/m/Y') : '-' }}</td>
                                                                    <td class="text-center">
                                                                        <div class="btn-group btn-group-sm" role="group">
                                                                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.cares.show', $care->id) }}" title="Ver detalle">
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
</div>
@endsection
