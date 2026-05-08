@extends('layouts.app')

@section('title', 'Detalle — Tipo de incidente')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Tipo de incidente')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-exclamation-triangle"></i> {{ $incidentType->nombre ?? 'Registro' }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.incident-types.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.incident-types.edit', $incidentType->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nombre:</strong>
                            {{ $incidentType->nombre }}
                        </div>

                        <div class="form-group mb-3"><strong>Riesgo (índice):</strong> {{ $incidentType->riesgo ?? '—' }}</div>
                        <div class="form-group mb-3"><strong>Activo (1=sí):</strong> {{ $incidentType->activo ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
