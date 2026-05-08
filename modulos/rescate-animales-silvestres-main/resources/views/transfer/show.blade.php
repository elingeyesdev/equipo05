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
                        <h3 class="card-title mb-0"><i class="fas fa-exchange-alt"></i> Traslado #{{ $transfer->id }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.transfers.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.transfers.edit', $transfer->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Persona:</strong>
                            {{ $transfer->person?->nombre ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Centro:</strong>
                            {{ $transfer->center?->nombre ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Observaciones:</strong>
                            {{ $transfer->observaciones ?: '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
