@extends('layouts.app')

@section('title', 'Hojas de vida — Rescate')
@section('subtitle', 'Fichas de animales en custodia con filtros por especie, estado y centro.')
@section('content_header_title', 'Hojas de vida')
@section('content_header_subtitle', 'Animales en custodia')

@section('content_body')
<div class="container-fluid res-page-shell">
    @include('fusion.modulos.partials.rescate-module-nav')
    @include('fusion.modulos.partials.rescate-flash')

    <div class="card res-list-card res-accent-success">
        <div class="card-header">
            <h3 class="res-card-title mb-0"><i class="fas fa-paw text-success mr-2"></i>Animales en custodia</h3>
            <a href="{{ route('rescate.animal-records.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus mr-1"></i> Nueva hoja de vida
            </a>
        </div>

        <div class="card-body">
            <form method="GET" class="res-filter-bar">
                <div class="form-row">
                    <div class="col-md-4">
                        <label>{{ __('Nombre del animal') }}</label>
                        <input type="text" name="nombre" value="{{ request('nombre') }}" class="form-control form-control-sm" placeholder="{{ __('Buscar por nombre') }}">
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('Especie') }}</label>
                        <select name="especie_id" class="form-control form-control-sm">
                            <option value="">{{ __('Todas') }}</option>
                            @foreach(($species ?? []) as $s)
                                <option value="{{ $s->id }}" {{ (string) request('especie_id') === (string) $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>{{ __('Estado') }}</label>
                        <select name="estado_id" class="form-control form-control-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach(($statuses ?? []) as $st)
                                <option value="{{ $st->id }}" {{ (string) request('estado_id') === (string) $st->id ? 'selected' : '' }}>{{ $st->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>{{ __('Centro') }}</label>
                        <select name="centro_id" class="form-control form-control-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach(($centers ?? []) as $c)
                                <option value="{{ $c->id }}" {{ (string) request('centro_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-2 d-flex align-items-center">
                    <button type="submit" class="btn btn-primary btn-sm mr-3">{{ __('Buscar') }}</button>
                    <a href="{{ route('rescate.animal-files.index') }}" class="btn btn-link btn-sm p-0">{{ __('Mostrar todos') }}</a>
                </div>
            </form>

            @if($animalFiles->isEmpty())
                <div class="res-empty-state">
                    <i class="fas fa-paw fa-2x mb-2 d-block text-muted"></i>
                    {{ __('No se registró ninguna hoja de animal todavía.') }}
                </div>
            @else
                <div class="row res-card-grid">
                    @foreach ($animalFiles as $animalFile)
                        <div class="col-md-6 col-lg-4">
                            <div class="card res-entity-card">
                                @include('fusion.modulos.partials.rescate-entity-photo', [
                                    'path' => $animalFile->imagen_url,
                                    'seed' => rescate_media_seed($animalFile),
                                    'alt' => $animalFile->animal?->nombre ?? 'Animal',
                                    'badge' => $animalFile->species?->nombre,
                                ])
                                <div class="card-header">
                                    <h3 class="card-title mb-0" title="{{ $animalFile->animal?->nombre }}">
                                        <i class="fas fa-paw text-success mr-1"></i>
                                        {{ \Illuminate\Support\Str::limit($animalFile->animal?->nombre ?? __('Sin nombre'), 28) }}
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="res-meta-row">
                                        <span><i class="fas fa-dna text-muted mr-1"></i>{{ __('Especie') }}</span>
                                        <strong>{{ $animalFile->species?->nombre ?? '-' }}</strong>
                                    </div>
                                    <div class="res-meta-row">
                                        <span><i class="fas fa-heartbeat text-muted mr-1"></i>{{ __('Estado') }}</span>
                                        @if($animalFile->animalStatus)
                                            <span class="badge badge-info">{{ $animalFile->animalStatus->nombre }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                    @if($animalFile->center)
                                    <div class="res-meta-row">
                                        <span><i class="fas fa-hospital text-muted mr-1"></i>{{ __('Centro') }}</span>
                                        <span>{{ \Illuminate\Support\Str::limit($animalFile->center->nombre, 22) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="card-footer">
                                    @canRole('Administrador')
                                    <form action="{{ route('rescate.animal-files.destroy', $animalFile->id) }}" method="POST" class="mb-0 res-btn-row">
                                        <a class="btn btn-primary btn-sm" href="{{ route('rescate.animal-files.show', $animalFile->id) }}">
                                            <i class="fa fa-eye"></i> {{ __('Ver') }}
                                        </a>
                                        <a class="btn btn-success btn-sm" href="{{ route('rescate.animal-files.edit', $animalFile->id) }}">
                                            <i class="fa fa-edit"></i> {{ __('Editar') }}
                                        </a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm js-confirm-delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <div class="res-btn-row">
                                        <a class="btn btn-primary btn-sm" href="{{ route('rescate.animal-files.show', $animalFile->id) }}">
                                            <i class="fa fa-eye"></i> {{ __('Ver') }}
                                        </a>
                                        @canRole('Veterinario')
                                        <a class="btn btn-success btn-sm" href="{{ route('rescate.animal-files.edit', $animalFile->id) }}">
                                            <i class="fa fa-edit"></i> {{ __('Editar') }}
                                        </a>
                                        @endcanRole
                                    </div>
                                    @endcanRole
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($animalFiles->hasPages())
        <div class="card-footer">
            {!! $animalFiles->withQueryString()->links('pagination::bootstrap-4') !!}
        </div>
        @endif
    </div>
</div>
@endsection
