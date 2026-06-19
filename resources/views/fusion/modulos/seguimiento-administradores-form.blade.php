@extends('layouts.app')

@section('content_header_title', $registro ? 'Editar administrador' : 'Agregar administrador')
@section('content_header_subtitle', 'Datos de la cuenta administrativa')

@section('content')
@include('fusion.modulos.partials.seguimiento-module-nav')
@include('fusion.modulos.partials.seguimiento-flash')

<div class="card seg-list-card seg-form-card seg-form-admin shadow-sm">
    <div class="card-header">
        <h3 class="card-title mb-0"><i class="fas fa-user-shield mr-1"></i> Formulario</h3>
        <a href="{{ route('seguimiento.administradores') }}" class="btn btn-outline-secondary btn-sm ml-auto">
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
        @endphp

        <div class="card-body">
            <div class="seg-form-row">
                <div class="form-group mb-0">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $registro->nombre ?? '') }}" required maxlength="30">
                    <small class="text-muted" id="nombreCount">0/30</small>
                </div>
                <div class="form-group mb-0">
                    <label for="apellido">Apellido <span class="text-danger">*</span></label>
                    <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', $registro->apellido ?? '') }}" required maxlength="30">
                    <small class="text-muted" id="apellidoCount">0/30</small>
                </div>
            </div>

            <div class="seg-form-row">
                <div class="form-group mb-0">
                    <label for="email">Correo <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $registro->email ?? '') }}" required>
                </div>
                <div class="form-group mb-0">
                    <label for="ci">Cédula de identidad <span class="text-danger">*</span></label>
                    <div class="d-flex" style="gap:0.5rem;">
                        <input type="text" name="ci" id="ci" class="form-control" value="{{ old('ci', $ciNumero) }}" required pattern="[0-9]{6,8}" maxlength="8" placeholder="Número">
                        <select name="ext" id="ext" class="form-control" style="max-width:110px;" required>
                            <option value="" disabled {{ !old('ext', $ciExt) ? 'selected' : '' }}>Ext.</option>
                            @foreach(['SC', 'LP', 'CB', 'OR', 'PT', 'TJ', 'HC', 'BE', 'PD'] as $extension)
                                <option value="{{ $extension }}" {{ old('ext', $ciExt) === $extension ? 'selected' : '' }}>{{ $extension }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group mb-0" style="max-width:50%;">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', data_get($registro, 'telefono', '')) }}" pattern="[0-9]{7,8}" maxlength="8" placeholder="71234567">
            </div>
        </div>

        <div class="card-footer seg-btn-toolbar justify-content-end">
            <a href="{{ route('seguimiento.administradores') }}" class="btn btn-outline-secondary btn-sm">Cancelar</a>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save"></i> {{ $registro ? 'Guardar cambios' : 'Agregar administrador' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    function bindCounter(input, el) {
        const update = () => { el.textContent = input.value.length + '/30'; };
        input.addEventListener('input', update);
        update();
    }
    bindCounter(document.getElementById('nombre'), document.getElementById('nombreCount'));
    bindCounter(document.getElementById('apellido'), document.getElementById('apellidoCount'));
    ['ci', 'telefono'].forEach(id => {
        const input = document.getElementById(id);
        if (input) input.addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, ''); });
    });
});
</script>
@endpush
