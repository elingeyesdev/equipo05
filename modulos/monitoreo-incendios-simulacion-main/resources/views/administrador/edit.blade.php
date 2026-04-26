@extends('layouts.app')

@section('subtitle', 'Editar Administrador')
@section('content_header_title', 'Administradores')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Editar Administrador: {{ $administrador->user->name }}" theme="warning" icon="fas fa-edit">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('administradores.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('administradores.update', $administrador->id) }}" role="form" enctype="multipart/form-data">
                        {{ method_field('PATCH') }}
                        @csrf
                        @include('administrador.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
