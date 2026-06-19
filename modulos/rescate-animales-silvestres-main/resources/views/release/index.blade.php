@extends('layouts.app')

@section('title', 'Liberaciones — Rescate')
@section('subtitle', 'Animales liberados con filtros por especie y fechas.')
@section('content_header_title', 'Liberaciones')
@section('content_header_subtitle', 'Listado')

@section('content_body')
<div class="container-fluid res-page-shell">
    @include('fusion.modulos.partials.rescate-module-nav')
    @include('fusion.modulos.partials.rescate-flash')

    <div class="card res-list-card res-accent-success">
        <div class="card-header">
            <h3 class="res-card-title mb-0"><i class="fas fa-leaf text-success mr-2"></i>{{ __('Liberaciones') }}</h3>
            @canManageVeterinaryReleases
            <a href="{{ route('rescate.releases.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i> {{ __('Crear nueva') }}
            </a>
            @endcanManageVeterinaryReleases
        </div>
        <div class="card-body">
            <form method="GET" class="res-filter-bar js-auto-filter-form">
                            <div class="form-row">
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Nombre del animal') }}</label>
                                    <input type="text" name="nombre_animal" value="{{ request('nombre_animal') }}" class="form-control" placeholder="{{ __('Buscar por nombre') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="mb-1">{{ __('Especie') }}</label>
                                    <select name="especie_id" class="form-control">
                                        <option value="">{{ __('Todas') }}</option>
                                        @foreach(($species ?? []) as $s)
                                            <option value="{{ $s->id }}" {{ (string)request('especie_id') === (string)$s->id ? 'selected' : '' }}>
                                                {{ $s->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="mb-1">{{ __('Fecha desde') }}</label>
                                    <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="mb-1">{{ __('Fecha hasta') }}</label>
                                    <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="form-control">
                                </div>
                            </div>
                            <div class="mt-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary btn-sm mr-3">{{ __('Buscar') }}</button>
                                <a href="{{ route('rescate.releases.index') }}" class="btn btn-link p-0">{{ __('Mostrar todos') }}</a>
                            </div>
                        </form>


                        <div class="row res-card-grid">
                            @foreach ($releases as $release)
                                @php
                                    $animalFile = $release->animalFile;
                                    $animal = $animalFile?->animal;
                                    $imagenUrl = $release->imagen_url ?? $animalFile?->imagen_url;
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card res-entity-card">
                                        @include('fusion.modulos.partials.rescate-entity-photo', [
                                            'path' => $imagenUrl,
                                            'seed' => rescate_media_seed($animalFile),
                                            'alt' => $animal?->nombre ?? 'Animal liberado',
                                            'badge' => $animalFile?->species?->nombre,
                                        ])
                                        <div class="card-header d-flex align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $animal?->nombre }}">
                                                <i class="fas fa-dove text-primary mr-2"></i>
                                                {{ \Illuminate\Support\Str::limit($animal?->nombre ?? __('Sin nombre'), 26) }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-unbordered mb-0">
                                                <li class="list-group-item">
                                                    <i class="fas fa-paw text-muted mr-2"></i>
                                                    <b>{{ __('Especie:') }}</b>
                                                    <span class="float-right">{{ $animalFile?->species?->nombre ?? '-' }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="fas fa-heartbeat text-muted mr-2"></i>
                                                    <b>{{ __('Estado:') }}</b>
                                                    <span class="float-right">
                                                        @if($animalFile?->animalStatus)
                                                            <span class="badge badge-info">{{ $animalFile->animalStatus->nombre }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </span>
                                                </li>
                                                
                                                <li class="list-group-item">
                                                    <i class="fas fa-calendar-alt text-muted mr-2"></i>
                                                    <b>{{ __('Fecha de liberación:') }}</b>
                                                    <span class="float-right">{{ optional($release->created_at)->format('d/m/Y') }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            <a class="btn btn-primary btn-sm w-100" href="{{ route('rescate.releases.show', $release->id) }}">
                                                <i class="fa fa-fw fa-eye"></i> {{ __('Ver') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($releases->isEmpty())
                            <div class="res-empty-state">
                                <i class="fas fa-leaf fa-2x mb-2 d-block text-muted"></i>
                                {{ __('No se encontraron liberaciones con los filtros seleccionados.') }}
                            </div>
                        @endif
        </div>
        @if($releases->hasPages())
        <div class="card-footer">
            {!! $releases->withQueryString()->links('pagination::bootstrap-4') !!}
        </div>
        @endif
    </div>
</div>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.querySelector('form.js-auto-filter-form');
        if (form) {
            var applyBtn = form.querySelector('button[type="submit"]');
            applyBtn && applyBtn.addEventListener('click', function(){ /* submit explicit */ });
        }
    });
    </script>
@endsection
