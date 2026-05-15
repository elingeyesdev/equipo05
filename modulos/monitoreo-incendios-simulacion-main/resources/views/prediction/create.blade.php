@extends('layouts.app')

@section('subtitle', 'Generar Predicción')
@section('content_header_title', 'Predicciones')
@section('content_header_subtitle', 'Generar Nueva Predicción')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Nueva Predicción de Propagación" theme="success" icon="fas fa-chart-line">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('incendios.predictions.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('incendios.predictions.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @include('prediction.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
