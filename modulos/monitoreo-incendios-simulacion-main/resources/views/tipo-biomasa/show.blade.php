@extends('layouts.app')

@section('subtitle', 'Ver Tipo de Biomasa')
@section('content_header_title', 'Tipos de Biomasa')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Información del Tipo de Biomasa: {{ $tipoBiomasa->tipo_biomasa }}" theme="info" icon="fas fa-leaf">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('tipo-biomasas.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                        <a href="{{ route('tipo-biomasas.edit', $3) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
                    </x-slot>

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-callout theme="success" title="Tipo de Biomasa">
                                {{ $tipoBiomasa->tipo_biomasa }}
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="info" title="Color Representativo">
                                <span class="badge badge-lg" style="background-color: {{ $tipoBiomasa->color ?? '#4CAF50' }}; color: white; font-size: 14px; padding: 8px 16px;">
                                    {{ $tipoBiomasa->color ?? '#4CAF50' }}
                                </span>
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-12">
                            <x-adminlte-callout theme="warning" title="Factor de Propagación del Fuego">
                                <span class="badge badge-warning" style="font-size: 16px;">{{ $tipoBiomasa->modificador_intensidad ?? 1.0 }}x</span>
                                <br>
                                <small class="text-muted">
                                    @if(($tipoBiomasa->modificador_intensidad ?? 1.0) < 0.8)
                                        Propagación lenta - Áreas rocosas o con poca vegetación
                                    @elseif(($tipoBiomasa->modificador_intensidad ?? 1.0) < 1.2)
                                        Propagación estándar - Vegetación típica
                                    @else
                                        Propagación rápida - Bosque seco o vegetación densa
                                    @endif
                                </small>
                            </x-adminlte-callout>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
