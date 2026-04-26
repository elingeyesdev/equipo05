@extends('adminlte::page')

@section('template_title')
    {{ $biomasa->name ?? __('Show') . " " . __('Biomasa') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Biomasa</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('biomasas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Nombre</dt>
                            <dd class="col-sm-9">{{ $biomasa->nombre }}</dd>

                            <dt class="col-sm-3">Tipo</dt>
                            <dd class="col-sm-9">{{ $biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Área (m²)</dt>
                            <dd class="col-sm-9">{{ $biomasa->area_m2 }}</dd>

                            <dt class="col-sm-3">Densidad</dt>
                            <dd class="col-sm-9">{{ $biomasa->densidad }}</dd>

                            <dt class="col-sm-3">Humedad</dt>
                            <dd class="col-sm-9">{{ $biomasa->humedad }}</dd>

                            <dt class="col-sm-3">Ubicación</dt>
                            <dd class="col-sm-9">{{ $biomasa->ubicacion }}</dd>

                            <dt class="col-sm-3">Descripción</dt>
                            <dd class="col-sm-9">{{ $biomasa->descripcion }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
