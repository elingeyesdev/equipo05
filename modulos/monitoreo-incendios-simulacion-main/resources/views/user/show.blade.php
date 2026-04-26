@extends('layouts.app')

@section('subtitle', 'Ver Usuario')
@section('content_header_title', 'Usuarios')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Información del Usuario: {{ $user->name }}" theme="info" icon="fas fa-user">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                        <a href="{{ route('users.edit', $3) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
                    </x-slot>

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-callout theme="primary" title="Nombre">
                                {{ $user->name }}
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="info" title="Correo Electrónico">
                                {{ $user->email }}
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="success" title="Teléfono">
                                {{ $user->telefono ?? 'No especificado' }}
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="warning" title="Cédula de Identidad">
                                {{ $user->cedula_identidad ?? 'No especificado' }}
                            </x-adminlte-callout>
                        </div>
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
