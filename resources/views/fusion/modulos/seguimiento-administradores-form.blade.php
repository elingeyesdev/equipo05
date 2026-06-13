@extends('layouts.app')

@section('title', $registro ? 'Editar Administrador' : 'Agregar Administrador')

@section('css')
<style>
  :root {
    --primary-color: #007bff;
    --primary-hover: #0056b3;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
  }

  .form-header-title {
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 1.5rem;
  }

  .form-card {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    border-top: 4px solid #007bff; /* Thin blue line at the top */
    box-shadow: var(--card-shadow);
    padding: 0;
    overflow: hidden;
  }

  .form-card-header {
    background-color: #ffffff;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    font-weight: 700;
    color: var(--text-main);
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .form-card-body {
    padding: 2rem 1.5rem;
  }

  .form-card-footer {
    background-color: #f8fafc;
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
  }

  .form-group-label {
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
  }

  .form-control-custom {
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    padding: 0.6rem 0.8rem;
    font-size: 0.95rem;
    color: var(--text-main);
    background-color: #ffffff;
    transition: all 0.2s;
    width: 100%;
  }

  .form-control-custom:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  .input-icon-group {
    display: flex;
    width: 100%;
  }

  .input-icon-prepend {
    background-color: #e9ecef;
    border: 1px solid #cbd5e1;
    border-right: none;
    border-top-left-radius: 6px;
    border-bottom-left-radius: 6px;
    width: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
  }

  .input-icon-group .form-control-custom {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }

  /* Split CI + Ext Group */
  .ci-split-group {
    display: flex;
    gap: 10px;
    width: 100%;
  }

  .ci-split-group .ci-number-input {
    flex-grow: 1;
  }

  .ci-split-group .ci-ext-select {
    width: 110px;
    flex-shrink: 0;
  }

  .help-text-muted {
    font-size: 0.775rem;
    color: var(--text-muted);
    margin-top: 4px;
    display: block;
  }

  .btn-submit-form {
    background-color: #007bff;
    color: #ffffff;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    padding: 0.6rem 1.5rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
    cursor: pointer;
  }

  .btn-submit-form:hover {
    background-color: #0056b3;
    color: #ffffff;
  }

  .btn-cancel-form {
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    color: var(--text-main);
    font-weight: 600;
    border-radius: 6px;
    padding: 0.6rem 1.5rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-cancel-form:hover {
    background-color: #f1f5f9;
    color: var(--text-main);
    text-decoration: none;
  }

  .form-row-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 1.5rem;
  }

  @media (max-width: 768px) {
    .form-row-grid {
      grid-template-columns: 1fr;
      gap: 15px;
    }
  }

  /* CSS Animations */
  @keyframes slideIn {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .animate-slide {
    animation: slideIn 0.3s ease-out forwards;
  }
</style>
@endsection

@section('content')
<div class="container py-4">

  {{-- Page Title --}}
  <h1 class="form-header-title animate-slide">
    {{ $registro ? 'Editar Administrador' : 'Agregar Administrador' }}
  </h1>

  {{-- Card Container --}}
  <div class="form-card animate-slide">
    
    {{-- Card Header --}}
    <div class="form-card-header">
      <i class="fas fa-user-shield text-primary"></i> Datos del Administrador
    </div>

    {{-- Form --}}
    <form action="{{ $registro ? route('seguimiento.crud.update', ['seccion' => $seccion, 'id' => $registro->id_usuario]) : route('seguimiento.crud.store', ['seccion' => $seccion]) }}" method="POST" id="adminForm">
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
      @endphp

      <div class="form-card-body">
        
        {{-- Row 1: Nombre & Apellido --}}
        <div class="form-row-grid">
          
          {{-- Nombre --}}
          <div>
            <label for="nombre" class="form-group-label">Nombre <span class="text-danger">*</span></label>
            <input 
              type="text" 
              name="nombre" 
              id="nombre" 
              class="form-control-custom" 
              placeholder="Ingrese el nombre" 
              value="{{ old('nombre', $registro->nombre ?? '') }}" 
              required
              maxlength="30"
            >
            <span class="help-text-muted" id="nombreCount">0/30 caracteres (mínimo 2)</span>
          </div>

          {{-- Apellido --}}
          <div>
            <label for="apellido" class="form-group-label">Apellido <span class="text-danger">*</span></label>
            <input 
              type="text" 
              name="apellido" 
              id="apellido" 
              class="form-control-custom" 
              placeholder="Ingrese el apellido" 
              value="{{ old('apellido', $registro->apellido ?? '') }}" 
              required
              maxlength="30"
            >
            <span class="help-text-muted" id="apellidoCount">0/30 caracteres (mínimo 2)</span>
          </div>

        </div>

        {{-- Row 2: Correo Electrónico & Cédula de Identidad --}}
        <div class="form-row-grid">
          
          {{-- Correo Electrónico --}}
          <div>
            <label for="email" class="form-group-label">Correo Electrónico <span class="text-danger">*</span></label>
            <div class="input-icon-group">
              <span class="input-icon-prepend">
                <i class="fas fa-envelope"></i>
              </span>
              <input 
                type="email" 
                name="email" 
                id="email" 
                class="form-control-custom" 
                placeholder="ejemplo@correo.com" 
                value="{{ old('email', $registro->email ?? '') }}" 
                required
              >
            </div>
            <span class="help-text-muted">Debe ser un correo válido</span>
          </div>

          {{-- Cédula de Identidad --}}
          <div>
            <label for="ci" class="form-group-label">Cédula de Identidad <span class="text-danger">*</span></label>
            <div class="ci-split-group">
              <div class="ci-number-input">
                <input 
                  type="text" 
                  name="ci" 
                  id="ci" 
                  class="form-control-custom" 
                  placeholder="Ingrese el CI" 
                  value="{{ old('ci', $ciNumero) }}" 
                  required
                  pattern="[0-9]{6,8}"
                  title="Debe ingresar de 6 a 8 dígitos numéricos"
                >
              </div>
              <div class="ci-ext-select">
                <select name="ext" id="ext" class="form-control-custom" required>
                  <option value="" disabled {{ !old('ext', $ciExt) ? 'selected' : '' }}>Ext.</option>
                  @foreach(['SC', 'LP', 'CB', 'OR', 'PT', 'TJ', 'HC', 'BE', 'PD'] as $extension)
                    <option value="{{ $extension }}" {{ old('ext', $ciExt) === $extension ? 'selected' : '' }}>{{ $extension }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <span class="help-text-muted">6-8 dígitos, solo números</span>
          </div>

        </div>

        {{-- Row 3: Teléfono --}}
        <div class="form-row-grid" style="grid-template-columns: 1fr;">
          
          {{-- Teléfono --}}
          <div style="max-width: 50%;" class="w-100-mobile">
            <label for="telefono" class="form-group-label">Teléfono</label>
            <div class="input-icon-group">
              <span class="input-icon-prepend">
                <i class="fas fa-phone"></i>
              </span>
              <input 
                type="text" 
                name="telefono" 
                id="telefono" 
                class="form-control-custom" 
                placeholder="Ej: 71234567" 
                value="{{ old('telefono', data_get($registro, 'telefono', '')) }}"
                pattern="[0-9]{7,8}"
                title="Debe ingresar de 7 a 8 dígitos numéricos"
              >
            </div>
            <span class="help-text-muted">7-8 dígitos (opcional)</span>
          </div>

        </div>

      </div>

      {{-- Footer Actions --}}
      <div class="form-card-footer">
        <a href="{{ route('seguimiento.administradores') }}" class="btn-cancel-form">
          <i class="fas fa-times"></i> Cancelar
        </a>
        <button type="submit" class="btn-submit-form">
          <i class="fas fa-user-plus"></i> {{ $registro ? 'Guardar Cambios' : 'Agregar Administrador' }}
        </button>
      </div>

    </form>

  </div>

</div>
@endsection

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const nombreInput = document.getElementById('nombre');
    const nombreCount = document.getElementById('nombreCount');
    const apellidoInput = document.getElementById('apellido');
    const apellidoCount = document.getElementById('apellidoCount');
    const ciInput = document.getElementById('ci');
    const telefonoInput = document.getElementById('telefono');

    // Function to update characters counters
    function updateCounter(input, countElement) {
      const len = input.value.length;
      countElement.textContent = `${len}/30 caracteres (mínimo 2)`;
      if (len >= 2 && len <= 30) {
        countElement.style.color = '#64748b'; // standard text-muted
      } else {
        countElement.style.color = '#dc3545'; // danger color
      }
    }

    nombreInput.addEventListener('input', () => updateCounter(nombreInput, nombreCount));
    apellidoInput.addEventListener('input', () => updateCounter(apellidoInput, apellidoCount));

    // Run counters initially
    updateCounter(nombreInput, nombreCount);
    updateCounter(apellidoInput, apellidoCount);

    // Limit inputs to only digits for CI and Phone
    [ciInput, telefonoInput].forEach(input => {
      input.addEventListener('input', (e) => {
        // Remove non-digit characters
        e.target.value = e.target.value.replace(/\D/g, '');
      });
    });
  });
</script>
<style>
  @media (max-width: 768px) {
    .w-100-mobile {
      max-width: 100% !important;
    }
  }
</style>
@endsection
