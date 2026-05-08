@extends('layouts.app')

@section('title', 'Detalle — Tipo de alimento')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Tipo de alimento')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-drumstick-bite"></i> {{ $feedingType->nombre ?? 'Registro' }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.feeding-types.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.feeding-types.edit', $feedingType->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nombre:</strong>
                            {{ $feedingType->nombre }}
                        </div>

                        <div class="form-group mb-3"><strong>Descripción:</strong> {{ $feedingType->descripcion ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
