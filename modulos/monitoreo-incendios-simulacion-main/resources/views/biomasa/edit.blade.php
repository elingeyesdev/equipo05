@extends('layouts.app')

@section('subtitle', 'Editar Biomasa')
@section('content_header_title', 'Biomasas')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Editar Biomasa: #{{ $biomasa->id }}" theme="warning" icon="fas fa-edit">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('incendios.biomasas.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('incendios.biomasas.update', $biomasa->id) }}" role="form" enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf
                        @include('biomasa.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
