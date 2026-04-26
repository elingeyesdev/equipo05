@extends('layouts.app')

@section('subtitle', 'Editar Tipo de Biomasa')
@section('content_header_title', 'Tipos de Biomasa')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Editar Tipo de Biomasa: {{ $tipoBiomasa->tipo_biomasa }}" theme="warning" icon="fas fa-edit">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('tipo-biomasas.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('tipo-biomasas.update', $tipoBiomasa->id) }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        @include('tipo-biomasa.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
