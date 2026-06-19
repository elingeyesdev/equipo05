@extends('layouts.app')

@section('title', 'Historial de cambios — Rescate')
@section('subtitle', 'Trazabilidad de modificaciones por animal.')
@section('content_header_title', 'Historial de animales')
@section('content_header_subtitle', 'Línea de tiempo')

@section('content_body')
<div class="container-fluid res-page-shell">
    @include('fusion.modulos.partials.rescate-module-nav')
    @include('fusion.modulos.partials.rescate-flash')

    <div class="card res-list-card res-accent-info">
        <div class="card-header">
            <h3 class="res-card-title mb-0"><i class="fas fa-history text-info mr-2"></i>{{ __('Historial de Animales') }}</h3>
            <form method="get" class="form-inline mb-0">
                <label for="order" class="mr-2 small font-weight-bold">{{ __('Orden') }}</label>
                <select name="order" id="order" class="form-control form-control-sm" onchange="this.form.submit()">
                    @php $ord = request()->get('order'); @endphp
                    <option value="desc" {{ $ord!=='asc'?'selected':'' }}>{{ __('Más nuevo primero') }}</option>
                    <option value="asc" {{ $ord==='asc'?'selected':'' }}>{{ __('Más viejo primero') }}</option>
                </select>
            </form>
        </div>
        <div class="card-body">
                        <div class="row res-card-grid">
                            @foreach ($histories as $h)
                                @php
                                    $animalFile = $h->animalFile;
                                    $animal = $animalFile?->animal;
                                    $imagenUrl = $animalFile?->imagen_url ?? $animal?->report?->imagen_url ?? null;
                                    $nombre = $animal?->nombre ?? __('Sin nombre');
                                    $fechaCambio = $h->changed_at ? \Carbon\Carbon::parse($h->changed_at)->format('d/m/Y H:i') : '-';
                                    $desc = data_get($h->valores_nuevos, 'care.descripcion');
                                    $obsText = is_array($h->observaciones ?? null) ? ($h->observaciones['texto'] ?? null) : ($h->observaciones ?? null);
                                    $resumen = $desc ? \Illuminate\Support\Str::limit($desc, 60) : ($obsText ? \Illuminate\Support\Str::limit($obsText, 60) : '-');
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card res-entity-card">
                                        @include('fusion.modulos.partials.rescate-entity-photo', [
                                            'path' => $imagenUrl,
                                            'seed' => rescate_media_seed($animalFile),
                                            'alt' => $nombre,
                                            'badge' => $animalFile?->species?->nombre,
                                        ])
                                        <div class="card-header d-flex align-items-center">
                                            <h3 class="card-title mb-0" title="{{ $nombre }}">
                                                <i class="fas fa-history text-primary mr-2"></i>
                                                {{ \Illuminate\Support\Str::limit($nombre, 26) }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-unbordered mb-0">
                                                <li class="list-group-item">
                                                    <i class="fas fa-calendar-alt text-muted mr-2"></i>
                                                    <b>{{ __('Fecha de cambio:') }}</b>
                                                    <span class="float-right">{{ $fechaCambio }}</span>
                                                </li>
                                                <li class="list-group-item">
                                                    <i class="fas fa-info-circle text-muted mr-2"></i>
                                                    <b>{{ __('Resumen:') }}</b>
                                                    <span class="float-right text-right" style="max-width: 60%;">
                                                        {{ $resumen }}
                                                    </span>
                                                </li>
                                                @if($animalFile?->species)
                                                <li class="list-group-item">
                                                    <i class="fas fa-paw text-muted mr-2"></i>
                                                    <b>{{ __('Especie:') }}</b>
                                                    <span class="float-right">{{ $animalFile->species->nombre }}</span>
                                                </li>
                                                @endif
                                                @if($animalFile?->animalStatus)
                                                <li class="list-group-item">
                                                    <i class="fas fa-heartbeat text-muted mr-2"></i>
                                                    <b>{{ __('Estado de salud:') }}</b>
                                                    <span class="float-right">
                                                        <span class="badge badge-info">{{ $animalFile->animalStatus->nombre }}</span>
                                                    </span>
                                                </li>
                                                @endif
                                                @if($animalFile)
                                                <li class="list-group-item">
                                                    <i class="fas fa-info-circle text-muted mr-2"></i>
                                                    <b>{{ __('Estado del proceso:') }}</b>
                                                    <span class="float-right">
                                                        <span class="badge {{ $animalFile->getEstadoBadgeClass() }}">{{ $animalFile->getEstado() }}</span>
                                                    </span>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            <a class="btn btn-primary btn-sm w-100" href="{{ route('rescate.animal-histories.show', $h->id) }}">
                                                <i class="fa fa-fw fa-eye"></i> {{ __('Ver Historial') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($histories->isEmpty())
                            <div class="res-empty-state">
                                <i class="fas fa-history fa-2x mb-2 d-block text-muted"></i>
                                {{ __('No se encontraron registros en el historial de animales.') }}
                            </div>
                        @endif
        </div>
        @if($histories->hasPages())
        <div class="card-footer">
            {!! $histories->withQueryString()->links('pagination::bootstrap-4') !!}
        </div>
        @endif
    </div>
</div>
@endsection

