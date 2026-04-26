@extends('layouts.app')

@section('subtitle', 'Crear Foco de Incendio')
@section('content_header_title', 'Focos de Incendio')
@section('content_header_subtitle', 'Crear Nuevo')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Crear Foco de Incendio" theme="success" icon="fas fa-fire">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('focos-incendios.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('focos-incendios.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @include('focos-incendio.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection

@section('css')
    @stack('css')
@endsection

@section('js')
    @stack('js')
@endsection
