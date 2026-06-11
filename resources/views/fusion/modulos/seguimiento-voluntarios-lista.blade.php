@extends('layouts.app')

@section('title', $tituloSeccion)

@section('css')
<style>
  :root {
    --color-amarillo: #FFA726;
    --color-card: #ffffff;
    --color-texto-principal: #333333;
    --color-blanco: #f8f9fa;
    --color-azul: #007bff;
    --color-gris: #6c757d;
  }

  .inactivos-theme {
    --color-azul: #dc3545;
  }

  .listado-container {
    padding: 20px 0;
    width: 100%;
    box-sizing: border-box;
  }

  .listado-content {
    width: 100%;
    box-sizing: border-box;
  }

  .listado-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
  }

  .titulo-listado {
    color: var(--color-azul);
    font-size: 2.5rem;
    margin: 0;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .listado-paneles {
    display: flex;
    flex-direction: column;
    gap: 30px;
  }

  .panel-barrabusqueda {
    border-radius: 12px;
    padding: 25px;
    background: var(--color-card);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0,0,0,0.08);
  }

  .barra-busqueda {
    width: 100%;
    margin-bottom: 20px;
  }

  .input-busqueda {
    padding: 12px 18px;
    font-size: 16px;
    border-radius: 25px;
    border: 1px solid var(--color-azul);
    width: 100%;
    transition: border-color 0.3s ease;
  }

  .input-busqueda:focus {
    outline: none;
    border-color: var(--color-amarillo);
    box-shadow: 0 0 0 0.2rem rgba(255, 167, 38, 0.25);
  }

  .filtros-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    align-items: flex-end;
  }

  .filtros-grid label {
    font-weight: 600;
    color: var(--color-azul);
    display: block;
    margin-bottom: 5px;
  }

  .filtros-grid input,
  .filtros-grid select {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: border-color 0.3s ease;
    background-color: #fff;
  }

  .filtros-grid input:focus,
  .filtros-grid select:focus {
    outline: none;
    border-color: var(--color-azul);
    box-shadow: 0 0 0 0.2rem rgba(255, 167, 38, 0.15);
  }

  .filtro-limpiar {
    display: flex;
    align-items: flex-end;
  }

  .filtro-limpiar button {
    padding: 10px 16px;
    border: none;
    background-color: var(--color-gris);
    color: white;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
  }

  .filtro-limpiar button:hover {
    background-color: #a00000;
  }

  .panel-listadovol {
    background: transparent;
  }

  .lista {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .mensaje-vacio {
    color: var(--color-azul);
    font-style: italic;
    padding: 30px;
    text-align: center;
    background: var(--color-card);
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.08);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
  }

  /* Card de Voluntario */
  .card-voluntario {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 24px;
    background-color: var(--color-card);
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-left: 6px solid var(--color-azul);
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
    color: inherit;
    position: relative;
    border-top: 1px solid rgba(0,0,0,0.03);
    border-bottom: 1px solid rgba(0,0,0,0.03);
    border-right: 1px solid rgba(0,0,0,0.03);
  }

  .card-voluntario:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
    background-color: var(--color-blanco);
    text-decoration: none;
    color: inherit;
  }

  .avatar {
    width: 48px;
    height: 48px;
    background-color: var(--color-azul);
    color: white;
    font-weight: bold;
    font-size: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .info-voluntario {
    display: flex;
    flex-direction: column;
    flex: 1;
  }

  .nombre-estado {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 6px;
  }

  .nombre-estado h4 {
    margin: 0;
    font-size: 18px;
    color: var(--color-texto-principal);
    font-weight: 600;
  }

  .estado {
    font-size: 13px;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 600;
  }

  .estado.activo {
    background-color: rgba(46, 125, 50, 0.1);
    color: #2e7d32;
  }

  .estado.inactivo {
    background-color: rgba(198, 40, 40, 0.1);
    color: #c62828;
  }

  .info-voluntario p {
    margin: 0;
    font-size: 14px;
    color: #666;
  }

  @media (max-width: 768px) {
    .listado-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .titulo-listado {
      font-size: 2rem;
    }

    .filtros-grid {
      grid-template-columns: 1fr;
    }

    .card-voluntario {
      padding: 14px 18px;
    }

    .avatar {
      width: 40px;
      height: 40px;
      font-size: 18px;
    }
  }
</style>
@endsection

@section('content')
<div class="listado-container {{ $seccion === 'voluntarios-inactivos' ? 'inactivos-theme' : '' }}">
  <div class="listado-content">
    
    <header class="listado-header">
      <h1 class="titulo-listado">
        @if($seccion === 'voluntarios-inactivos')
          <i class="fas fa-user-slash text-danger"></i>
        @endif
        {{ $tituloSeccion }}
      </h1>
      <div class="header-acciones">
        @if($seccion === 'voluntarios-inactivos')
          <a href="{{ route('seguimiento.voluntarios') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Volver a Voluntarios Activos
          </a>
        @else
          <a href="{{ route('seguimiento.dashboard') }}" class="btn btn-primary btn-sm mr-2">
            <i class="fas fa-arrow-left mr-1"></i> Volver al dashboard
          </a>
          <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus mr-1"></i> Agregar
          </a>
        @endif
      </div>
    </header>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    {{-- Tarjeta de resumen para inactivos --}}
    @if($seccion === 'voluntarios-inactivos')
      <div class="card card-outline card-danger shadow-sm mb-4" style="border-left: 5px solid #dc3545; max-width: 100%;">
        <div class="card-body py-3">
          <h2 class="text-danger font-weight-bold mb-0" style="font-size: 2.5rem; line-height: 1;">{{ $voluntarios->count() }}</h2>
          <span class="text-muted" style="font-size: 0.9rem; font-weight: 500;">Total Inactivos</span>
        </div>
      </div>
    @endif

    <section class="listado-paneles">
      {{-- Panel de búsqueda y filtros --}}
      <div class="panel-barrabusqueda">
        <form action="{{ route('seguimiento.' . $seccion) }}" method="GET" id="filtrosForm">
          <div class="barra-busqueda">
            <input
              type="search"
              name="q"
              class="input-busqueda"
              placeholder="Buscar por nombre"
              value="{{ request('q') }}"
            />
          </div>

          <div class="filtros-grid">
            <div>
              <label>CI</label>
              <input
                type="text"
                name="ci"
                placeholder="Buscar por CI"
                value="{{ request('ci') }}"
              />
            </div>

            <div>
              <label>Tipo de Sangre</label>
              <select name="tipo_sangre">
                <option value="">Todos</option>
                <option value="O+" {{ request('tipo_sangre') === 'O+' ? 'selected' : '' }}>O+</option>
                <option value="O-" {{ request('tipo_sangre') === 'O-' ? 'selected' : '' }}>O-</option>
                <option value="A+" {{ request('tipo_sangre') === 'A+' ? 'selected' : '' }}>A+</option>
                <option value="A-" {{ request('tipo_sangre') === 'A-' ? 'selected' : '' }}>A-</option>
                <option value="B+" {{ request('tipo_sangre') === 'B+' ? 'selected' : '' }}>B+</option>
                <option value="B-" {{ request('tipo_sangre') === 'B-' ? 'selected' : '' }}>B-</option>
                <option value="AB+" {{ request('tipo_sangre') === 'AB+' ? 'selected' : '' }}>AB+</option>
                <option value="AB-" {{ request('tipo_sangre') === 'AB-' ? 'selected' : '' }}>AB-</option>
              </select>
            </div>

            @if($seccion !== 'voluntarios-inactivos')
              <div>
                <label>Disponibilidad</label>
                <select name="estado">
                  <option value="">Todos</option>
                  <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                  <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
              </div>
            @endif

            <div class="filtro-limpiar">
              <button type="button" onclick="limpiarFiltros()">
                <i class="fas fa-times"></i> Limpiar filtros
              </button>
            </div>
          </div>
        </form>
      </div>

      {{-- Panel de lista de voluntarios --}}
      <div class="panel-listadovol">
        <div class="lista">
          @forelse($voluntarios as $voluntario)
            @php
              $iniciales = mb_substr($voluntario->nombre ?? '', 0, 1, 'UTF-8') . mb_substr($voluntario->apellido ?? '', 0, 1, 'UTF-8');
              if (empty($iniciales)) {
                  $iniciales = 'V';
              }
              $estadoClass = $voluntario->activo ? 'activo' : 'inactivo';
              $estadoLabel = $voluntario->activo ? 'Activo' : 'Inactivo';
            @endphp
            <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $voluntario->id_usuario]) }}" class="card-voluntario">
              <div class="avatar">
                <span>{{ strtoupper($iniciales) }}</span>
              </div>
              <div class="info-voluntario">
                <div class="nombre-estado">
                  <h4>{{ $voluntario->nombre }} {{ $voluntario->apellido }}</h4>
                  <span class="estado {{ $estadoClass }}">
                    {{ $estadoLabel }}
                  </span>
                </div>
                <p>CI: {{ $voluntario->ci }} &nbsp; | &nbsp; Tipo de Sangre: {{ $voluntario->tipo_sangre ?? 'N/D' }}</p>
              </div>
            </a>
          @empty
            @if($seccion === 'voluntarios-inactivos')
              <div class="text-center py-5 bg-white rounded-lg shadow-sm border d-flex flex-column align-items-center justify-content-center" style="border-radius: 12px; border-left: 6px solid #dc3545 !important;">
                <i class="far fa-smile fa-4x text-success mb-3"></i>
                <p class="text-muted font-italic mb-0" style="font-size: 1.1rem; font-weight: 500;">¡Excelente! No hay voluntarios inactivos.</p>
              </div>
            @else
              <p class="mensaje-vacio">No se encontraron voluntarios.</p>
            @endif
          @endforelse
        </div>
      </div>
    </section>

  </div>
</div>
@endsection

@section('js')
<script>
  // Auto-submit al cambiar filtros
  document.querySelectorAll('#filtrosForm select, #filtrosForm input').forEach(element => {
    element.addEventListener('change', function() {
      document.getElementById('filtrosForm').submit();
    });
  });

  // Limpiar filtros
  function limpiarFiltros() {
    document.querySelectorAll('#filtrosForm input[type="text"], #filtrosForm input[type="search"]').forEach(input => {
      input.value = '';
    });
    document.querySelectorAll('#filtrosForm select').forEach(select => {
      select.value = '';
    });
    document.getElementById('filtrosForm').submit();
  }

  // Auto-ocultar alertas después de 5 segundos
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity = '0';
      setTimeout(function() {
        alert.remove();
      }, 500);
    });
  }, 5000);
</script>
@endsection
