@extends('layouts.app')

@section('subtitle', 'Crear Usuario')
@section('content_header_title', 'Usuarios')
@section('content_header_subtitle', 'Crear Nuevo')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Crear Usuario" theme="success" icon="fas fa-user-plus">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('users.store') }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @include('user.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
