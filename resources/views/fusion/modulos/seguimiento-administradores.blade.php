@extends('layouts.app')

@section('title', 'Administradores')

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

  .admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 15px;
  }

  .admin-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
  }

  .btn-add-admin {
    background-color: #007bff;
    color: #ffffff;
    font-weight: 600;
    border: none;
    border-radius: 6px;
    padding: 0.6rem 1.2rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
  }

  .btn-add-admin:hover {
    background-color: #0056b3;
    color: #ffffff;
    text-decoration: none;
  }

  /* Filter Card */
  .filter-card {
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
  }

  .filter-form-grid {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1.5fr 1fr;
    gap: 15px;
    align-items: center;
  }

  @media (max-width: 768px) {
    .filter-form-grid {
      grid-template-columns: 1fr;
    }
  }

  .form-control-custom {
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
    color: var(--text-main);
    background-color: #ffffff;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    width: 100%;
  }

  .form-control-custom:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
  }

  .input-group-custom {
    display: flex;
    width: 100%;
  }

  .input-group-custom .form-control-custom {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }

  .btn-search-append {
    background-color: #e9ecef;
    border: 1px solid #cbd5e1;
    border-left: none;
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
    padding: 0 15px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }

  .btn-search-append:hover {
    background-color: #dee2e6;
    color: var(--text-main);
  }

  .btn-clear {
    background-color: #ffffff;
    border: 1px solid #007bff;
    color: #007bff;
    font-weight: 600;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
  }

  .btn-clear:hover {
    background-color: rgba(0, 123, 255, 0.05);
    color: #0056b3;
    text-decoration: none;
  }

  /* Admin Cards List */
  .admin-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
  }

  .admin-card {
    background-color: #ffffff;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    border-left: 4px solid #007bff;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--card-shadow);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .admin-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
  }

  .admin-card-left {
    display: flex;
    align-items: center;
    gap: 18px;
  }

  .admin-avatar {
    width: 52px;
    height: 52px;
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
    font-size: 1.25rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .admin-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .admin-info-header {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .admin-name {
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--text-main);
    margin: 0;
  }

  .admin-badge {
    background-color: #0ea5e9;
    color: #ffffff;
    font-size: 0.725rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.025em;
  }

  .admin-detail-item {
    font-size: 0.9rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0;
  }

  .admin-detail-item i {
    width: 16px;
    color: var(--text-muted);
  }

  .admin-card-right {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .status-badge {
    font-size: 0.825rem;
    font-weight: 700;
    padding: 5px 12px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .status-badge-active {
    background-color: #28a745;
    color: #ffffff;
  }

  .status-badge-inactive {
    background-color: #6c757d;
    color: #ffffff;
  }

  .btn-toggle-status {
    background-color: #ffffff;
    font-weight: 600;
    font-size: 0.85rem;
    border-radius: 6px;
    padding: 0.45rem 1rem;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
  }

  .btn-toggle-deactivate {
    border: 1px solid #dc3545;
    color: #dc3545;
  }

  .btn-toggle-deactivate:hover {
    background-color: #dc3545;
    color: #ffffff;
    text-decoration: none;
  }

  .btn-toggle-activate {
    border: 1px solid #28a745;
    color: #28a745;
  }

  .btn-toggle-activate:hover {
    background-color: #28a745;
    color: #ffffff;
    text-decoration: none;
  }

  .admin-actions {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .btn-edit-admin {
    width: 34px;
    height: 34px;
    border-radius: 6px;
    border: 1px solid #cbd5e1;
    background-color: #ffffff;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }

  .btn-edit-admin:hover {
    background-color: #f1f5f9;
    color: var(--text-main);
    border-color: #94a3b8;
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    background-color: #ffffff;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
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
<div class="container-fluid py-4">

  {{-- Header --}}
  <div class="admin-header animate-slide">
    <h1>Administradores</h1>
    <div>
      <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn-add-admin">
        <i class="fas fa-plus"></i> Agregar Administrador
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show animate-slide mb-4" role="alert" style="border-radius: 8px;">
      <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  {{-- Filters Card --}}
  <div class="filter-card animate-slide">
    <form action="{{ route('seguimiento.administradores') }}" method="GET" id="filterForm">
      <div class="filter-form-grid">
        
        {{-- Search by Name --}}
        <div>
          <div class="input-group-custom">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control-custom" placeholder="Buscar por nombre">
            <button class="btn-search-append" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </div>

        {{-- Search by CI --}}
        <div>
          <input type="text" name="ci" value="{{ request('ci') }}" class="form-control-custom" placeholder="Buscar por CI">
        </div>

        {{-- State Dropdown --}}
        <div>
          <select name="estado" class="form-control-custom" onchange="document.getElementById('filterForm').submit();">
            <option value="">Todos los estados</option>
            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
          </select>
        </div>

        {{-- Clear Button --}}
        <div>
          <button type="button" class="btn-clear" onclick="limpiarFiltros();">
            <i class="fas fa-times"></i> Limpiar
          </button>
        </div>

      </div>
    </form>
  </div>

  {{-- List of Administrators --}}
  <div class="admin-list animate-slide">
    @forelse($administradores as $admin)
      @php
        $iniciales = '';
        if ($admin->nombre) {
            $iniciales .= mb_substr($admin->nombre, 0, 1, 'UTF-8');
        }
        if ($admin->apellido) {
            $iniciales .= mb_substr($admin->apellido, 0, 1, 'UTF-8');
        }
        if (empty($iniciales)) {
            $iniciales = 'AD';
        }
      @endphp
      <div class="admin-card">
        
        {{-- Left Info Block --}}
        <div class="admin-card-left">
          <div class="admin-avatar">
            {{ strtoupper($iniciales) }}
          </div>
          
          <div class="admin-info">
            <div class="admin-info-header">
              <h2 class="admin-name">{{ $admin->nombre }} {{ $admin->apellido }}</h2>
              <span class="admin-badge">Administrador</span>
            </div>
            <p class="admin-detail-item">
              <i class="fas fa-envelope"></i> {{ $admin->email ?: 'Sin correo electrónico' }}
            </p>
            <p class="admin-detail-item">
              <i class="fas fa-id-card"></i> CI: {{ $admin->ci ?: 'Sin Cédula de Identidad' }}
            </p>
            @if(!empty($admin->telefono))
              <p class="admin-detail-item">
                <i class="fas fa-phone"></i> Tel: {{ $admin->telefono }}
              </p>
            @endif
          </div>
        </div>

        {{-- Right Actions Block --}}
        <div class="admin-card-right">
          
          {{-- Status Badge --}}
          @if($admin->activo)
            <span class="status-badge status-badge-active">Activo</span>
          @else
            <span class="status-badge status-badge-inactive">Inactivo</span>
          @endif

          {{-- Edit Button --}}
          <div class="admin-actions">
            <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $admin->id_usuario]) }}" class="btn-edit-admin" title="Editar Administrador">
              <i class="fas fa-pencil-alt"></i>
            </a>

            {{-- Toggle Active Form --}}
            <form action="{{ route('seguimiento.crud.update', ['seccion' => $seccion, 'id' => $admin->id_usuario]) }}" method="POST" class="d-inline">
              @csrf
              @method('PUT')
              <input type="hidden" name="toggle_active" value="1">
              @if($admin->activo)
                <button type="submit" class="btn-toggle-status btn-toggle-deactivate" onclick="return confirm('¿Seguro que deseas desactivar a este administrador?');">
                  <i class="fas fa-ban"></i> Desactivar
                </button>
              @else
                <button type="submit" class="btn-toggle-status btn-toggle-activate">
                  <i class="fas fa-check"></i> Activar
                </button>
              @endif
            </form>
          </div>

        </div>

      </div>
    @empty
      <div class="empty-state">
        <i class="fas fa-user-shield fa-3x mb-3 text-muted"></i>
        <p class="text-muted mb-0 font-weight-bold" style="font-size: 1.1rem;">No se encontraron administradores registrados con los filtros aplicados.</p>
      </div>
    @endforelse
  </div>

</div>
@endsection

@section('js')
<script>
  function limpiarFiltros() {
    const form = document.getElementById('filterForm');
    form.querySelector('input[name="q"]').value = '';
    form.querySelector('input[name="ci"]').value = '';
    form.querySelector('select[name="estado"]').value = '';
    form.submit();
  }

  // Auto-hide alert banner
  setTimeout(function() {
    $('.alert').alert('close');
  }, 4000);
</script>
@endsection
