@extends('layouts.app')

@section('title', $registro ? 'Editar Necesidad' : 'Crear Necesidad')

@section('css')
<style>
  :root {
    --primary-color: #3b82f6;
    --primary-hover: #2563eb;
    --bg-light: #f8fafc;
    --border-color: #e2e8f0;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  }

  .form-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #ffffff;
    border-radius: 16px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--card-shadow);
  }

  .form-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
  }

  .form-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
    padding: 2rem;
  }

  .form-group label {
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
  }

  .form-control-custom {
    border-radius: 10px;
    border: 2px solid var(--border-color);
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s;
    color: var(--text-main);
    background-color: #ffffff;
  }

  .form-control-custom:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
    outline: none;
  }

  /* CSS Animations */
  @keyframes slideIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .animate-slide {
    animation: slideIn 0.35s ease-out forwards;
  }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">

  {{-- Header --}}
  <div class="form-header animate-slide">
    <div class="row align-items-center">
      <div class="col-md-9">
        <h1 class="font-weight-bold mb-2">
          <i class="fas fa-hand-holding-medical mr-2 text-primary"></i>
          {{ $registro ? 'Editar Necesidad' : 'Crear Necesidad' }}
        </h1>
        <p class="mb-0 text-white-50" style="font-size: 1.1rem;">
          {{ $registro ? 'Modifica los datos de la necesidad registrada.' : 'Registra una nueva carencia o recurso urgente para los brigadistas.' }}
        </p>
      </div>
      <div class="col-md-3 text-md-right mt-3 mt-md-0">
        <a href="{{ route('seguimiento.necesidades') }}" class="btn btn-light px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 10px; color: var(--text-main);">
          <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
      </div>
    </div>
  </div>

  {{-- Formulario --}}
  <div class="form-card animate-slide">
    <form action="{{ $registro ? route('seguimiento.crud.update', ['seccion' => $seccion, 'id' => $registro->id_necesidad]) : route('seguimiento.crud.store', ['seccion' => $seccion]) }}" method="POST">
      @csrf
      @if($registro)
        @method('PUT')
      @endif

      <div class="form-group mb-4">
        <label for="tipo">Tipo de necesidad <span class="text-danger">*</span></label>
        <select name="tipo" id="tipo" class="form-control form-control-custom" required>
          <option value="" disabled {{ !old('tipo', $registro->tipo ?? '') ? 'selected' : '' }}>Seleccione el tipo</option>
          <option value="Víveres" {{ old('tipo', $registro->tipo ?? '') === 'Víveres' ? 'selected' : '' }}>Víveres</option>
          <option value="Herramientas" {{ old('tipo', $registro->tipo ?? '') === 'Herramientas' ? 'selected' : '' }}>Herramientas</option>
          <option value="Logística" {{ old('tipo', $registro->tipo ?? '') === 'Logística' ? 'selected' : '' }}>Logística</option>
          <option value="Salud" {{ old('tipo', $registro->tipo ?? '') === 'Salud' ? 'selected' : '' }}>Salud</option>
          <option value="Equipo" {{ old('tipo', $registro->tipo ?? '') === 'Equipo' ? 'selected' : '' }}>Equipo</option>
        </select>
      </div>

      <div class="form-group mb-4">
        <label for="descripcion">Descripción <span class="text-danger">*</span></label>
        <textarea name="descripcion" id="descripcion" class="form-control form-control-custom" rows="5" placeholder="Describa la necesidad detectada" required>{{ old('descripcion', $registro->descripcion ?? '') }}</textarea>
      </div>

      <div class="d-flex align-items-center mt-5" style="gap: 12px;">
        <button type="submit" class="btn btn-primary px-5 py-3 font-weight-bold shadow-sm" style="border-radius: 10px;">
          Guardar
        </button>
        <a href="{{ route('seguimiento.necesidades') }}" class="btn btn-outline-secondary px-4 py-3 font-weight-bold" style="border-radius: 10px;">
          Cancelar
        </a>
      </div>
    </form>
  </div>

</div>
@endsection
