@extends('layouts.app')

@section('title', 'Detalle de traslado — Rescate')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Traslados')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-exchange-alt"></i>
                            Traslado #{{ $transfer->id }}
                            <span class="badge badge-{{ $transfer->isFirstTransfer() ? 'primary' : 'info' }} ml-2">
                                {{ $transfer->isFirstTransfer() ? __('Primer traslado') : __('Entre centros') }}
                            </span>
                        </h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.transfers.index', ['tab' => 'history']) }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.transfers.edit', $transfer->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <strong>{{ __('Persona') }}:</strong>
                                    {{ $transfer->person?->nombre ?? '—' }}
                                </div>
                                <div class="form-group mb-3">
                                    <strong>{{ __('Centro destino') }}:</strong>
                                    {{ $transfer->center?->nombre ?? '—' }}
                                </div>
                                <div class="form-group mb-3">
                                    <strong>{{ __('Fecha') }}:</strong>
                                    {{ optional($transfer->created_at)->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($transfer->report)
                                <div class="form-group mb-3">
                                    <strong>{{ __('Hallazgo') }}:</strong>
                                    <a href="{{ route('rescate.reports.show', $transfer->report->id) }}">#{{ $transfer->report->id }}</a>
                                    @if($transfer->report->direccion)
                                        <div class="small text-muted">{{ $transfer->report->direccion }}</div>
                                    @endif
                                </div>
                                @endif
                                @if($transfer->animal)
                                <div class="form-group mb-3">
                                    <strong>{{ __('Animal') }}:</strong>
                                    {{ $transfer->animal->nombre }}
                                    @php $species = $transfer->animal->animalFiles->first()?->species?->nombre; @endphp
                                    @if($species)
                                        <span class="text-muted">({{ $species }})</span>
                                    @endif
                                </div>
                                @endif
                                @if($transfer->latitud && $transfer->longitud)
                                <div class="form-group mb-3">
                                    <strong>{{ __('Coordenadas') }}:</strong>
                                    {{ $transfer->latitud }}, {{ $transfer->longitud }}
                                    <a class="btn btn-link btn-sm p-0 ml-1" href="{{ route('rescate.reports.mapa-campo', ['lat' => $transfer->latitud, 'lng' => $transfer->longitud]) }}">
                                        {{ __('Ver en mapa') }}
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <strong>{{ __('Observaciones') }}:</strong>
                            <p class="mb-0">{{ $transfer->observaciones ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
