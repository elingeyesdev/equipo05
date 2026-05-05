@extends('layouts.app')

@section('subtitle', 'Ver Biomasa')
@section('content_header_title', 'Biomasas')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Detalle de Biomasa #{{ $biomasa->id }}" theme="info" icon="fas fa-leaf">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('incendios.biomasas.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <dl class="row mb-0">
                        <dt class="col-sm-3">Nombre</dt>
                        <dd class="col-sm-9">{{ $biomasa->nombre }}</dd>

                        <dt class="col-sm-3">Tipo</dt>
                        <dd class="col-sm-9">{{ $biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Area (m²)</dt>
                        <dd class="col-sm-9">{{ $biomasa->area_m2 }}</dd>

                        <dt class="col-sm-3">Densidad</dt>
                        <dd class="col-sm-9">{{ $biomasa->densidad }}</dd>

                        <dt class="col-sm-3">Humedad</dt>
                        <dd class="col-sm-9">{{ $biomasa->humedad }}</dd>

                        <dt class="col-sm-3">Ubicacion</dt>
                        <dd class="col-sm-9">{{ $biomasa->ubicacion }}</dd>

                        <dt class="col-sm-3">Descripcion</dt>
                        <dd class="col-sm-9">{{ $biomasa->descripcion }}</dd>
                    </dl>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
