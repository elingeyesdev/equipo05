@extends('layouts.app')

@section('title', 'Detalle de cuidado — Rescate')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Cuidados registrados')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-hand-holding-medical"></i> Cuidado #{{ $care->id }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.cares.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.cares.edit', $care->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Animal:</strong>
                            {{ $care->animalFile?->animal?->nombre ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Tipo de cuidado:</strong>
                            {{ $care->careType?->nombre ?? '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Descripción:</strong>
                            {{ $care->descripcion ?: '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Fecha:</strong>
                            {{ $care->fecha ? \Carbon\Carbon::parse($care->fecha)->format('d/m/Y') : '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Imagen:</strong>
                            @if(!empty($care?->imagen_url))
                                <div class="mt-2">
                                    <img src="{{ rescate_media_url($care->imagen_url, 'cuidado-'.$care->id) }}" alt="Imagen cuidado" style="max-height:240px;">
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
