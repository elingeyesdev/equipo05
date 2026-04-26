@extends('layouts.app')

@section('subtitle', 'Ver Administrador')
@section('content_header_title', 'Administradores')
@section('content_header_subtitle', 'Detalle')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Información del Administrador: {{ $administrador->user->name }}" theme="info" icon="fas fa-user-shield">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('administradores.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                        <a href="{{ route('administradores.edit', $administrador->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>
                    </x-slot>

                    <div class="row">
                        <div class="col-md-6">
                            <x-adminlte-callout theme="primary" title="Nombre">
                                {{ $administrador->user->name }}
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="info" title="Email">
                                {{ $administrador->user->email }}
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="success" title="Departamento">
                                {{ $administrador->departamento }}
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="warning" title="Nivel de Acceso">
                                <span class="badge badge-warning" style="font-size: 16px;">{{ $administrador->nivel_acceso }}</span>
                                <br>
                                <small class="text-muted">
                                    @if($administrador->nivel_acceso == 5)
                                        Nivel Máximo - Acceso Total
                                    @elseif($administrador->nivel_acceso >= 3)
                                        Nivel Alto - Acceso Avanzado
                                    @else
                                        Nivel Básico - Acceso Limitado
                                    @endif
                                </small>
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="{{ $administrador->activo ? 'success' : 'secondary' }}" title="Estado">
                                @if($administrador->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </x-adminlte-callout>
                        </div>
                        <div class="col-md-6">
                            <x-adminlte-callout theme="light" title="Fechas">
                                <strong>Creado:</strong> {{ $administrador->created_at->format('d/m/Y H:i') }}<br>
                                <strong>Actualizado:</strong> {{ $administrador->updated_at->format('d/m/Y H:i') }}
                            </x-adminlte-callout>
                        </div>

                        @if($administrador->simulaciones->count() > 0)
                        <div class="col-md-12">
                            <hr>
                            <h5>Simulaciones creadas ({{ $administrador->simulaciones->count() }})</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Fecha</th>
                                            <th>Duración</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($administrador->simulaciones as $sim)
                                        <tr>
                                            <td>{{ $sim->nombre }}</td>
                                            <td>{{ $sim->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $sim->duracion }}h</td>
                                            <td>{{ $sim->estado }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
