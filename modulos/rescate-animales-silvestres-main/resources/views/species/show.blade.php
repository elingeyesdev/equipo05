@extends('layouts.app')

@section('title', 'Detalle — Especie')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Especie')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-paw"></i> {{ $species->nombre ?? 'Registro' }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.species.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.species.edit', $species->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nombre:</strong>
                            {{ $species->nombre }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
@endsection
