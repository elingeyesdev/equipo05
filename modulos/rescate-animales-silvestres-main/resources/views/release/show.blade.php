@extends('layouts.app')

@section('title', 'Detalle de liberación — Rescate')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Liberaciones')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    
        <div class="row">
            <div class="col-md-12">
                @if($release->animalFile)
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-info d-flex align-items-center flex-wrap" style="gap:.35rem;">
                        <div class="flex-grow-1">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-paw mr-1"></i>
                                {{ __('Información del Animal') }}
                            </h3>
                        </div>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.releases.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.releases.edit', $release->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Nombre') }}:</strong>
                                    {{ $release->animalFile->animal?->nombre ?? '—' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Especie') }}:</strong>
                                    {{ $release->animalFile->species?->nombre ?? '—' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Estado') }}:</strong>
                                    {{ $release->animalFile->animalStatus?->nombre ?? '—' }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Imagen') }}:</strong>
                                    @if($release->animalFile->imagen_url)
                                        <div style="max-width: 100%; overflow: hidden; border-radius: 4px;">
                                            <img src="{{ rescate_media_url($release->animalFile->imagen_url, rescate_media_seed($release->animalFile)) }}" alt="img" style="max-width: 100%; max-height: 180px; height: auto; width: auto; object-fit: contain; border-radius: 4px;">
                                        </div>
                                    @else
                                        <span>—</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-success d-flex align-items-center flex-wrap" style="gap:.35rem;">
                        <div class="flex-grow-1">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-dove mr-1"></i>
                                {{ __('Información de la Liberación') }}
                            </h3>
                        </div>
                        @if(!$release->animalFile)
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.releases.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.releases.edit', $release->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                        @endif
                    </div>
                    <div class="card-body bg-white">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Dirección') }}:</strong>
                                    {{ $release->direccion ?: '—' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2 mb20">
                                    <strong>{{ __('Fecha de liberación') }}:</strong>
                                    {{ optional($release->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @if($release->detalle)
                        <div class="form-group mb-2 mb20">
                            <strong>{{ __('Detalle') }}:</strong>
                            {{ $release->detalle }}
                        </div>
                        @endif
                        @if(!is_null($release->latitud) && !is_null($release->longitud))
                        <div class="form-group mb-2 mb20">
                            <strong>{{ __('Ubicación de la liberación') }}:</strong>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div id="release_map" style="height: 400px; border-radius: 6px; overflow: hidden; width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.leaflet')
    @include('partials.page-pad')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var rawLat = @json($release->latitud);
        var rawLon = @json($release->longitud);
        var lat = parseFloat(rawLat);
        var lon = parseFloat(rawLon);
        var hasLat = rawLat !== null && rawLat !== '' && Number.isFinite(lat);
        var hasLon = rawLon !== null && rawLon !== '' && Number.isFinite(lon);
        if (hasLat && hasLon) {
            window.initStaticMap({
                mapId: 'release_map',
                lat: lat,
                lon: lon,
                zoom: 16,
                popup: @json($release->direccion ?? null),
            });
        }
    });
    </script>
@endsection
