@extends('layouts.app')

@section('content_header_title', $registro ? 'Editar voluntario' : 'Agregar voluntario')
@section('content_header_subtitle', $seccion === 'voluntarios-inactivos' ? 'Reactivar o actualizar brigadista' : 'Registro de brigadista')

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="card seg-list-card seg-form-card seg-form-success shadow-sm">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="fas fa-user mr-1"></i> Datos del voluntario</h3>
        <a href="{{ route($seccion === 'voluntarios-inactivos' ? 'seguimiento.voluntarios-inactivos' : 'seguimiento.voluntarios') }}" class="btn btn-outline-secondary btn-sm ml-auto">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <form action="{{ $registro ? route('seguimiento.crud.update', ['seccion' => $seccion, 'id' => $registro->id_usuario]) : route('seguimiento.crud.store', ['seccion' => $seccion]) }}" method="POST">
        @csrf
        @if($registro) @method('PUT') @endif

        @php
            $ciNumero = '';
            $ciExt = '';
            $ciRegistro = data_get($registro, 'ci');
            if ($registro && !empty($ciRegistro)) {
                $parts = explode(' ', trim($ciRegistro));
                $ciNumero = $parts[0] ?? '';
                $ciExt = $parts[1] ?? '';
            }
            $activoDefault = $seccion === 'voluntarios-inactivos' ? '0' : '1';
            $activoVal = old('activo', data_get($registro, 'activo', $registro ? null : $activoDefault));
            $activoBool = filter_var($activoVal, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($activoBool === null && $activoVal !== null && $activoVal !== '') {
                $activoBool = in_array((string) $activoVal, ['1', 'true', 'activo'], true);
            }
            if ($activoBool === null) { $activoBool = $activoDefault === '1'; }
        @endphp

        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="seg-form-row">
                <div class="form-group mb-0">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', data_get($registro, 'nombre', '')) }}" required maxlength="150">
                </div>
                <div class="form-group mb-0">
                    <label for="apellido">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', data_get($registro, 'apellido', '')) }}" required maxlength="150">
                </div>
            </div>

            <div class="seg-form-row">
                <div class="form-group mb-0">
                    <label for="email">Correo <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', data_get($registro, 'email', '')) }}" required maxlength="150">
                </div>
                <div class="form-group mb-0">
                    <label for="ci">Cédula <span class="text-danger">*</span></label>
                    <div class="d-flex" style="gap:0.5rem;">
                        <input type="text" name="ci" id="ci" class="form-control" value="{{ old('ci', $ciNumero) }}" required pattern="[0-9]{6,8}" maxlength="8">
                        <select name="ext" id="ext" class="form-control" style="max-width:110px;">
                            <option value="">Ext.</option>
                            @foreach(['SC', 'LP', 'CB', 'OR', 'PT', 'TJ', 'HC', 'BE', 'PD'] as $extension)
                                <option value="{{ $extension }}" {{ old('ext', $ciExt) === $extension ? 'selected' : '' }}>{{ $extension }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="seg-form-row">
                <div class="form-group mb-0">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', data_get($registro, 'telefono', '')) }}" pattern="[0-9]{7,8}" maxlength="8">
                </div>
                <div class="form-group mb-0">
                    <label for="tipo_sangre">Tipo de sangre</label>
                    <select name="tipo_sangre" id="tipo_sangre" class="form-control">
                        <option value="">Seleccione…</option>
                        @foreach($tiposSangre as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo_sangre', data_get($registro, 'tipo_sangre')) === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group mb-0" style="max-width:280px;">
                <label for="activo">Estado</label>
                <select name="activo" id="activo" class="form-control">
                    <option value="1" {{ $activoBool ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ ! $activoBool ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="card-footer seg-btn-toolbar justify-content-end">
            <a href="{{ route($seccion === 'voluntarios-inactivos' ? 'seguimiento.voluntarios-inactivos' : 'seguimiento.voluntarios') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-save"></i> {{ $registro ? 'Guardar cambios' : 'Agregar voluntario' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    ['ci', 'telefono'].forEach(id => {
        const input = document.getElementById(id);
        if (input) input.addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, ''); });
    });
});
</script>
@endpush
