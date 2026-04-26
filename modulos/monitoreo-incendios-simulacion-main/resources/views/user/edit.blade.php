@extends('layouts.app')

@section('subtitle', 'Editar Usuario')
@section('content_header_title', 'Usuarios')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Editar Usuario: {{ $user->name }}" theme="warning" icon="fas fa-user-edit">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('users.update', $user->id) }}" role="form" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        @include('user.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
