@extends('layouts.app')

@section('subtitle', 'Crear Simulación')
@section('content_header_title', 'Simulaciones')
@section('content_header_subtitle', 'Crear Nuevo')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Crear Simulación" theme="success" icon="fas fa-project-diagram">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('incendios.simulaciones.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('incendios.simulaciones.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @include('simulacione.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
