@extends('layouts.app')

@section('title', 'Detalle — Frecuencia de alimentación')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Frecuencia de alimentación')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-clock"></i> {{ $feedingFrequency->nombre ?? 'Registro' }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.feeding-frequencies.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.feeding-frequencies.edit', $feedingFrequency->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nombre:</strong>
                            {{ $feedingFrequency->nombre }}
                        </div>

                        <div class="form-group mb-3"><strong>Descripción:</strong> {{ $feedingFrequency->descripcion ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
@endsection
