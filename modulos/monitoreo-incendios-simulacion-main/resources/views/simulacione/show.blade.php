@extends('layouts.app')

@section('subtitle', 'Ver Simulación')
@section('content_header_title', 'Simulaciones')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Detalle de Simulación #{{ $simulacione->id }}" theme="info" icon="fas fa-project-diagram">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('incendios.simulaciones.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

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
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
