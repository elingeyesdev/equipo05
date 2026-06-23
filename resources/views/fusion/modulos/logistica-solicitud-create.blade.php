@extends('layouts.app')

@section('content_header_title', 'Nueva solicitud')
@section('content_header_subtitle', 'Registro de ayuda para transporte de donaciones')

@section('content')
@include('fusion.modulos.partials.logistica-module-nav')
@include('fusion.modulos.partials.logistica-flash')

<div class="card logistica-list-card shadow-sm">
    <div class="card-header">
        <div class="logistica-btn-toolbar w-100">
            <a href="{{ route('logistica.solicitud') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('logistica.solicitud.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required placeholder="Ejemplo: Maria" value="{{ old('nombre') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Apellido</label>
                    <input type="text" name="apellido" class="form-control" placeholder="Ejemplo: Gutierrez" value="{{ old('apellido') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>CI</label>
                    <input type="text" name="ci" class="form-control" required placeholder="Ejemplo: 12345678" value="{{ old('ci') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Telefono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Ejemplo: 70012345" value="{{ old('telefono') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Comunidad</label>
                    <input type="text" name="comunidad" class="form-control" required placeholder="Ejemplo: Comunidad San Juan" value="{{ old('comunidad') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Provincia</label>
                    <input type="text" name="provincia" class="form-control" required placeholder="Ejemplo: Chiquitos" value="{{ old('provincia') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label>Direccion</label>
                    <input type="text" name="direccion" class="form-control" placeholder="Se completará al seleccionar en el mapa" value="{{ old('direccion') }}">
                </div>

                @include('fusion.modulos.partials.logistica-mapa-picker')

                <div class="col-md-4 mb-3">
                    <label>Tipo de emergencia</label>
                    <select name="tipo_emergencia" class="form-control" required>
                        <option value="">Seleccione…</option>
                        @foreach($tiposEmergencia as $tipo)
                            <option value="{{ $tipo->nombre }}" {{ old('tipo_emergencia') === $tipo->nombre ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>Cantidad Personas</label>
                    <input type="number" name="cantidad_personas" min="1" class="form-control" required placeholder="Ejemplo: 25" value="{{ old('cantidad_personas') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" required value="{{ old('fecha_inicio') }}">
                    <small class="text-muted">Fecha de inicio de la emergencia.</small>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Fecha Necesidad</label>
                    <input type="date" name="fecha_necesidad" class="form-control" value="{{ old('fecha_necesidad') }}">
                    <small class="text-muted">Fecha límite para recibir apoyo.</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label>Insumos Necesarios</label>
                    <textarea name="insumos_necesarios" rows="4" class="form-control" placeholder="Ejemplo: 30 frazadas, 20 botellas de agua, medicamentos básicos">{{ old('insumos_necesarios') }}</textarea>
                </div>
            </div>
            <div class="d-flex" style="gap:.5rem;">
                <a href="{{ route('logistica.solicitud') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar solicitud</button>
            </div>
        </form>
    </div>
</div>
@endsection
