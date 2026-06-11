@extends('layouts.app')

@section('title', 'Capacitaciones')

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

  .cap-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    color: #ffffff;
    border-radius: 16px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--card-shadow);
  }

  .cap-header::after {
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

  .table-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
    overflow: hidden;
  }

  .table-custom {
    margin-bottom: 0;
  }

  .table-custom th {
    background-color: var(--bg-light);
    color: var(--text-main);
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.05em;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    border-top: none;
  }

  .table-custom td {
    padding: 1.25rem 1.5rem;
    vertical-align: middle;
    color: var(--text-main);
    border-bottom: 1px solid var(--border-color);
    font-size: 0.95rem;
  }

  .table-custom tr:last-child td {
    border-bottom: none;
  }

  .table-custom tbody tr {
    transition: background-color 0.2s;
  }

  .table-custom tbody tr:hover {
    background-color: #f1f5f9;
  }

  .action-btn-group {
    display: flex;
    gap: 8px;
  }

  .action-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    border: 1px solid var(--border-color);
    background-color: #ffffff;
  }

  .action-btn-edit {
    color: var(--primary-color);
  }

  .action-btn-edit:hover {
    background-color: var(--primary-color);
    color: #ffffff;
    border-color: var(--primary-color);
  }

  .action-btn-delete {
    color: #ef4444;
  }

  .action-btn-delete:hover {
    background-color: #ef4444;
    color: #ffffff;
    border-color: #ef4444;
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
  <div class="cap-header animate-slide">
    <div class="row align-items-center">
      <div class="col-md-7">
        <h1 class="font-weight-bold mb-2">
          <i class="fas fa-graduation-cap mr-2 text-primary"></i>Gestión de Capacitaciones
        </h1>
        <p class="mb-0 text-white-50" style="font-size: 1.1rem;">
          Registra y administra las capacitaciones y sus respectivos cursos y etapas de entrenamiento.
        </p>
      </div>
      <div class="col-md-5 text-md-right mt-3 mt-md-0">
        <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm mr-2" style="border-radius: 10px;">
          <i class="fas fa-plus mr-2"></i>Crear Nuevo
        </a>
        <a href="{{ route('seguimiento.dashboard') }}" class="btn btn-light px-4 py-2 font-weight-bold shadow-sm" style="border-radius: 10px; color: var(--text-main);">
          <i class="fas fa-arrow-left mr-2"></i>Volver
        </a>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show animate-slide" role="alert" style="border-radius: 12px; padding: 1rem 1.5rem;">
      <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  {{-- Tabla de Capacitaciones --}}
  <div class="table-card animate-slide">
    <div class="table-responsive">
      <table class="table table-custom">
        <thead>
          <tr>
            <th style="width: 80px;" class="text-center">No</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th style="width: 120px;" class="text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($capacitaciones as $idx => $cap)
            <tr>
              <td class="text-center font-weight-bold text-muted">{{ $idx + 1 }}</td>
              <td class="font-weight-bold" style="font-size: 1.02rem; color: var(--text-main);">{{ $cap->nombre }}</td>
              <td class="text-muted">{{ $cap->descripcion ?: 'Sin descripción disponible.' }}</td>
              <td class="text-center">
                <div class="action-btn-group justify-content-center">
                  <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $cap->id_capacitacion]) }}" class="action-btn action-btn-edit" title="Editar Capacitación">
                    <i class="fas fa-pencil-alt"></i>
                  </a>
                  <form action="{{ route('seguimiento.crud.destroy', ['seccion' => $seccion, 'id' => $cap->id_capacitacion]) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta capacitación y todos sus cursos asociados?');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="action-btn action-btn-delete" title="Eliminar Capacitación">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center py-5 text-muted">
                <i class="fas fa-chalkboard-teacher fa-3x mb-3 d-block" style="color: #cbd5e1;"></i>
                <span style="font-size: 1.1rem;">No se encontraron capacitaciones registradas.</span>
                <div class="mt-3">
                  <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-primary px-4 font-weight-bold" style="border-radius: 8px;">
                    Crear la primera capacitación
                  </a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
