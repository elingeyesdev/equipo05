@extends('layouts.app')

@section('subtitle', 'Crear Administrador')
@section('content_header_title', 'Administradores')
@section('content_header_subtitle', 'Crear Nuevo')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Crear Administrador" theme="success" icon="fas fa-user-shield">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('administradores.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('administradores.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @include('administrador.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
