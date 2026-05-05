@extends('layouts.app')

@section('subtitle', 'Crear Biomasa')
@section('content_header_title', 'Biomasas')
@section('content_header_subtitle', 'Crear Nuevo')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                    </div>
                @endif

                <x-adminlte-card title="Crear Biomasa" theme="success" icon="fas fa-leaf">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('incendios.biomasas.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('incendios.biomasas.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @include('biomasa.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
