@extends('layouts.app')

@section('title', ($animal->nombre ?? 'Animal') . ' — Rescate')
@section('subtitle', 'Vista de solo lectura.')
@section('content_header_title', 'Animales')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-secondary shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-paw"></i> {{ $animal->nombre ?? 'Animal' }}</h3>
                        <div class="d-flex flex-wrap" style="gap:.35rem;">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('rescate.animals.index') }}"><i class="fas fa-arrow-left"></i> Volver</a>
                            <a class="btn btn-primary btn-sm" href="{{ route('rescate.animals.edit', $animal->id) }}"><i class="fas fa-edit"></i> Editar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <strong>Nombre:</strong>
                            {{ $animal->nombre }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Sexo:</strong>
                            {{ $animal->sexo }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Descripción:</strong>
                            {{ $animal->descripcion ?: '—' }}
                        </div>
                        <div class="form-group mb-3">
                            <strong>Número de reporte:</strong>
                            {{ $animal->reporte_id ? ('#'.$animal->reporte_id) : '—' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
