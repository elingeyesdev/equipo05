@extends('adminlte::page')

@section('template_title')
    Detalle del Donante
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Información del Donante</h3>
                        <div class="card-tools">
                            <a class="btn btn-sm btn-primary" href="{{ route('inventario.donante.index') }}">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-user"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Nombre</span>
                                        <span class="info-box-number">{{ $donante->nombre }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box">
                                    <span
                                        class="info-box-icon {{ $donante->tipo === 'persona' ? 'bg-primary' : 'bg-success' }}">
                                        <i class="fas {{ $donante->tipo === 'persona' ? 'fa-user' : 'fa-building' }}"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Tipo</span>
                                        <span class="info-box-number">{{ ucfirst($donante->tipo) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-envelope"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Email</span>
                                        <span class="info-box-number text-break">{{ $donante->email ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-phone"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Teléfono</span>
                                        <span class="info-box-number">{{ $donante->telefono ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-map-marker-alt"></i> Dirección</h5>
                                    <p>{{ $donante->direccion ?? 'No especificada' }}</p>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="callout callout-secondary">
                                    <h5><i class="far fa-calendar-alt"></i> Fecha de Registro</h5>
                                    <p>{{ \Carbon\Carbon::parse($donante->fecha_registro)->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('inventario.donante.edit', $donante->id_donante) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('inventario.donante.index') }}" class="btn btn-secondary float-right">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



