@extends('adminlte::page')

@section('template_title')
    {{ $simulacione->name ?? __('Show') . " " . __('Simulaciones') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Simulaciones</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('simulaciones.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Nombre</dt>
                            <dd class="col-sm-9">{{ $simulacione->nombre }}</dd>

                            <dt class="col-sm-3">Fecha</dt>
                            <dd class="col-sm-9">{{ optional($simulacione->fecha)->format('Y-m-d H:i') }}</dd>

                            <dt class="col-sm-3">Duración (min)</dt>
                            <dd class="col-sm-9">{{ $simulacione->duracion }}</dd>

                            <dt class="col-sm-3">Estado</dt>
                            <dd class="col-sm-9">{{ $simulacione->estado }}</dd>

                            <dt class="col-sm-3">Focos activos</dt>
                            <dd class="col-sm-9">{{ $simulacione->focos_activos }}</dd>

                            <dt class="col-sm-3">Voluntarios enviados</dt>
                            <dd class="col-sm-9">{{ $simulacione->num_voluntarios_enviados }}</dd>
                            
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
