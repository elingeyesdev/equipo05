@extends('layouts.app')

@section('title', $registro ? 'Editar Voluntario' : 'Agregar Voluntario')

@section('css')
<style>
  :root {
    --border-color: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  }
  .form-header-title { font-size: 2.25rem; font-weight: 700; color: var(--text-main); margin-bottom: 1.5rem; }
  .form-card { background: #fff; border-radius: 8px; border: 1px solid var(--border-color); border-top: 4px solid #28a745; box-shadow: var(--card-shadow); overflow: hidden; }
  .form-card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main); display: flex; align-items: center; gap: 8px; }
  .form-card-body { padding: 2rem 1.5rem; }
  .form-card-footer { background: #f8fafc; padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px; }
  .form-group-label { font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; display: block; }
  .form-control-custom { border-radius: 6px; border: 1px solid #cbd5e1; padding: 0.6rem 0.8rem; width: 100%; }
  .form-row-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }
  .help-text-muted { color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem; display: block; }
  .input-icon-group { display: flex; width: 100%; }
  .input-icon-prepend { background: #e9ecef; border: 1px solid #cbd5e1; border-right: none; border-radius: 6px 0 0 6px; width: 44px; display: flex; align-items: center; justify-content: center; color: var(--text-muted); }
  .input-icon-group .form-control-custom { border-radius: 0 6px 6px 0; }
  .ci-split-group { display: flex; gap: 10px; }
  .ci-split-group .ci-number-input { flex-grow: 1; }
  .ci-split-group .ci-ext-select { width: 110px; flex-shrink: 0; }
  .btn-cancel-form, .btn-submit-form { border-radius: 6px; padding: 0.55rem 1.1rem; font-weight: 600; text-decoration: none; border: none; cursor: pointer; }
  .btn-cancel-form { background: #e2e8f0; color: #334155; }
  .btn-submit-form { background: #28a745; color: #fff; }
  @media (max-width: 768px) { .form-row-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
  <h1 class="form-header-title">{{ $registro ? 'Editar Voluntario' : 'Agregar Voluntario' }}</h1>

  <div class="form-card">
    <div class="form-card-header">
      <i class="fas fa-hands-helping text-success"></i> Datos del Voluntario
    </div>

    <form action="{{ $registro ? route('seguimiento.crud.update', ['seccion' => $seccion, 'id' => $registro->id_usuario]) : route('seguimiento.crud.store', ['seccion' => $seccion]) }}" method="POST">
      @csrf
      @if($registro)
        @method('PUT')
      @endif

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
        if ($activoBool === null) {
            $activoBool = $activoDefault === '1';
        }
      @endphp

      <div class="form-card-body">
        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="form-row-grid">
          <div>
            <label for="nombre" class="form-group-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre" class="form-control-custom" value="{{ old('nombre', data_get($registro, 'nombre', '')) }}" required maxlength="150">
          </div>
          <div>
            <label for="apellido" class="form-group-label">Apellido <span class="text-danger">*</span></label>
            <input type="text" name="apellido" id="apellido" class="form-control-custom" value="{{ old('apellido', data_get($registro, 'apellido', '')) }}" required maxlength="150">
          </div>
        </div>

        <div class="form-row-grid">
          <div>
            <label for="email" class="form-group-label">Correo electrónico <span class="text-danger">*</span></label>
            <div class="input-icon-group">
              <span class="input-icon-prepend"><i class="fas fa-envelope"></i></span>
              <input type="email" name="email" id="email" class="form-control-custom" value="{{ old('email', data_get($registro, 'email', '')) }}" required maxlength="150">
            </div>
          </div>
          <div>
            <label for="ci" class="form-group-label">Cédula de identidad <span class="text-danger">*</span></label>
            <div class="ci-split-group">
              <div class="ci-number-input">
                <input type="text" name="ci" id="ci" class="form-control-custom" value="{{ old('ci', $ciNumero) }}" required pattern="[0-9]{6,8}" maxlength="8">
              </div>
              <div class="ci-ext-select">
                <select name="ext" id="ext" class="form-control-custom">
                  <option value="">Ext.</option>
                  @foreach(['SC', 'LP', 'CB', 'OR', 'PT', 'TJ', 'HC', 'BE', 'PD'] as $extension)
                    <option value="{{ $extension }}" {{ old('ext', $ciExt) === $extension ? 'selected' : '' }}>{{ $extension }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <span class="help-text-muted">6-8 dígitos numéricos</span>
          </div>
        </div>

        <div class="form-row-grid">
          <div>
            <label for="telefono" class="form-group-label">Teléfono</label>
            <div class="input-icon-group">
              <span class="input-icon-prepend"><i class="fas fa-phone"></i></span>
              <input type="text" name="telefono" id="telefono" class="form-control-custom" value="{{ old('telefono', data_get($registro, 'telefono', '')) }}" pattern="[0-9]{7,8}" maxlength="8">
            </div>
          </div>
          <div>
            <label for="tipo_sangre" class="form-group-label">Tipo de sangre</label>
            <select name="tipo_sangre" id="tipo_sangre" class="form-control-custom">
              <option value="">Seleccione...</option>
              @foreach($tiposSangre as $tipo)
                <option value="{{ $tipo }}" {{ old('tipo_sangre', data_get($registro, 'tipo_sangre')) === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
              @endforeach
            </select>
            <span class="help-text-muted">Solo valores válidos: O+, A-, etc.</span>
          </div>
        </div>

        <div class="form-row-grid" style="grid-template-columns: 1fr;">
          <div style="max-width: 280px;">
            <label for="activo" class="form-group-label">Estado</label>
            <select name="activo" id="activo" class="form-control-custom">
              <option value="1" {{ $activoBool ? 'selected' : '' }}>Activo</option>
              <option value="0" {{ ! $activoBool ? 'selected' : '' }}>Inactivo</option>
            </select>
          </div>
        </div>
      </div>

      <div class="form-card-footer">
        <a href="{{ route($seccion === 'voluntarios-inactivos' ? 'seguimiento.voluntarios-inactivos' : 'seguimiento.voluntarios') }}" class="btn-cancel-form">
          <i class="fas fa-times"></i> Cancelar
        </a>
        <button type="submit" class="btn-submit-form">
          <i class="fas fa-save"></i> {{ $registro ? 'Guardar cambios' : 'Agregar voluntario' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
  ['ci', 'telefono'].forEach(id => {
    const input = document.getElementById(id);
    if (!input) return;
    input.addEventListener('input', e => { e.target.value = e.target.value.replace(/\D/g, ''); });
  });
});
</script>
@endsection
