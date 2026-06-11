@extends('layouts.app')

@section('title', 'Ayudas Solicitadas')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
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

  .ayudas-header {
    margin-bottom: 1.5rem;
  }

  .ayudas-header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
  }

  /* Filters Card */
  .filters-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .filters-card label {
    font-weight: 700;
    color: var(--text-main);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
  }

  .form-control-custom {
    border-radius: 8px;
    border: 1px solid var(--border-color);
    padding: 0.6rem 0.8rem;
    font-size: 0.95rem;
    color: var(--text-main);
    background-color: #ffffff;
    transition: all 0.2s;
  }

  .form-control-custom:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    outline: none;
  }

  .btn-clear-filters {
    background-color: #007bff;
    color: #ffffff;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    padding: 0.6rem 1.2rem;
    transition: all 0.2s;
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .btn-clear-filters:hover {
    background-color: #0056b3;
    color: #ffffff;
  }

  /* Section Cards */
  .section-card {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid var(--border-color);
    box-shadow: var(--card-shadow);
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .section-card-header {
    background-color: #343a40;
    color: #ffffff;
    padding: 1rem 1.25rem;
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: none;
  }

  .section-card-body {
    padding: 1rem;
    flex-grow: 1;
    overflow-y: auto;
  }

  /* Ayuda Card Item */
  .ayuda-item-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid var(--border-color);
    border-left: 5px solid #cbd5e1;
    cursor: pointer;
    transition: all 0.2s;
  }

  .ayuda-item-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    background-color: #f8fafc;
  }

  .ayuda-item-card.prio-alto { border-left-color: #dc3545; }
  .ayuda-item-card.prio-medio { border-left-color: #ffc107; }
  .ayuda-item-card.prio-bajo { border-left-color: #28a745; }

  .ayuda-item-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .ayuda-item-title {
    font-weight: 700;
    color: var(--text-main);
    font-size: 1rem;
  }

  .badge-prio {
    font-size: 0.7rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 20px;
    text-transform: uppercase;
  }

  .badge-prio-alto { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
  .badge-prio-medio { background-color: rgba(255, 193, 7, 0.1); color: #b7791f; }
  .badge-prio-bajo { background-color: rgba(40, 167, 69, 0.1); color: #28a745; }

  .ayuda-item-desc {
    color: var(--text-main);
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
    line-height: 1.4;
  }

  .ayuda-item-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: var(--text-muted);
  }

  /* Map Container */
  #map {
    height: 60vh;
    width: 100%;
    z-index: 1;
  }

  /* Legend overlay inside map */
  .map-legend {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ffffff;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    border: 1px solid var(--border-color);
    z-index: 1000;
    font-size: 0.85rem;
    min-width: 100px;
  }

  .map-legend-title {
    font-weight: 700;
    color: #ffc107;
    margin-bottom: 8px;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
  }

  .legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    color: var(--text-main);
  }

  .legend-item:last-child {
    margin-bottom: 0;
  }

  .legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
  }

  .dot-alta { background-color: #dc3545; }
  .dot-media { background-color: #ffc107; }
  .dot-baja { background-color: #28a745; }

  .empty-state-text {
    color: var(--text-muted);
    text-align: center;
    padding: 3rem 1rem;
    font-size: 0.95rem;
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

  {{-- Heading --}}
  <div class="ayudas-header animate-slide">
    <h1 class="font-weight-bold">Ayudas Solicitadas</h1>
  </div>

  {{-- Filters Card --}}
  <div class="filters-card animate-slide">
    <div class="row align-items-end">
      <div class="col-md-4 mb-3 mb-md-0">
        <label for="buscarNombre">Búsqueda</label>
        <input type="text" id="buscarNombre" class="form-control form-control-custom w-100" placeholder="Buscar por nombre">
      </div>

      <div class="col-md-3 mb-3 mb-md-0">
        <label for="prioridadFiltro">Prioridad</label>
        <select id="prioridadFiltro" class="form-control form-control-custom w-100">
          <option value="">Todas</option>
          <option value="alto">Alto</option>
          <option value="medio">Medio</option>
          <option value="bajo">Bajo</option>
        </select>
      </div>

      <div class="col-md-3 mb-3 mb-md-0">
        <label for="estadoFiltro">Estado</label>
        <select id="estadoFiltro" class="form-control form-control-custom w-100">
          <option value="">Todos</option>
          <option value="sin responder">Sin responder</option>
          <option value="en progreso">En progreso</option>
          <option value="respondido">Respondido</option>
          <option value="resuelto">Resuelto</option>
        </select>
      </div>

      <div class="col-md-2">
        <button class="btn btn-clear-filters" id="btnLimpiar">
          <i class="fas fa-times"></i> Limpiar filtros
        </button>
      </div>
    </div>
  </div>

  {{-- Split Screen Layout --}}
  <div class="row animate-slide">
    {{-- Left: list of requests --}}
    <div class="col-lg-5 mb-4">
      <div class="section-card">
        <div class="section-card-header">
          <i class="fas fa-list-ul"></i> Lista de Ayudas
        </div>
        <div class="section-card-body" id="listado" style="max-height: 60vh;">
          {{-- Dynamic list --}}
        </div>
      </div>
    </div>

    {{-- Right: map --}}
    <div class="col-lg-7 mb-4">
      <div class="section-card">
        <div class="section-card-header">
          <i class="fas fa-map-marker-alt"></i> Mapa de Ayudas
        </div>
        <div class="section-card-body p-0 position-relative">
          <div id="map"></div>

          {{-- Map Legend Overlay --}}
          <div class="map-legend">
            <div class="map-legend-title">Leyenda</div>
            <div class="legend-item">
              <span class="legend-dot dot-alta"></span>
              <span>Alta</span>
            </div>
            <div class="legend-item">
              <span class="legend-dot dot-media"></span>
              <span>Media</span>
            </div>
            <div class="legend-item">
              <span class="legend-dot dot-baja"></span>
              <span>Baja</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    // Cargar datos pasados desde el controlador
    const datos = {!! $solicitudesJson !!};

    const buscarNombre = document.getElementById('buscarNombre');
    const prioridadFiltro = document.getElementById('prioridadFiltro');
    const estadoFiltro = document.getElementById('estadoFiltro');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const listadoDiv = document.getElementById('listado');

    // --- Mapa Leaflet ---
    // Centrar en Santa Cruz, Bolivia (-17.80, -63.15)
    const map = L.map('map').setView([-17.806776, -63.15749], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markersLayer = L.layerGroup().addTo(map);

    function colorPorPrioridad(prio) {
      prio = (prio || '').toLowerCase();
      if (prio === 'alto') return '#dc3545';
      if (prio === 'medio') return '#ffc107';
      if (prio === 'bajo') return '#28a745';
      return '#6c757d';
    }

    function crearIcono(prioridad) {
      const color = colorPorPrioridad(prioridad);
      return L.divIcon({
        html: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="${color}" width="30" height="30">
                 <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                 <circle cx="12" cy="9" r="3" fill="#ffffff"/>
               </svg>`,
        className: '',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
      });
    }

    let marcadoresPorId = {};

    function renderMapa(lista) {
      markersLayer.clearLayers();
      marcadoresPorId = {};

      if (lista.length === 0) return;

      const bounds = [];

      lista.forEach(item => {
        if (item.latitud == null || item.longitud == null) return;

        const pos = [item.latitud, item.longitud];
        bounds.push(pos);

        const marker = L.marker(pos, { icon: crearIcono(item.prioridad) })
          .bindPopup(`
            <div style="font-family: inherit; font-size: .9rem;">
              <h6 class="font-weight-bold mb-1" style="color:#1e293b;">${item.voluntario}</h6>
              <p class="mb-1 text-muted" style="font-size:0.8rem;"><i class="fas fa-map-marker-alt"></i> ${item.direccion}</p>
              <span class="badge badge-secondary mb-2" style="font-size:0.7rem;">${item.tipo.toUpperCase()}</span><br>
              <strong>Prioridad:</strong> <span style="color:${colorPorPrioridad(item.prioridad)}; font-weight:bold;">${item.prioridad.toUpperCase()}</span><br>
              <strong>Estado:</strong> <span class="text-capitalize">${item.estado}</span><br>
              <strong>Detalle:</strong> ${item.detalle ?? 'Sin detalle'}<br>
              <small class="text-muted d-block mt-1"><i class="far fa-clock"></i> ${item.fecha}</small>
            </div>
          `)
          .addTo(markersLayer);

        marcadoresPorId[item.id] = marker;
      });

      if (bounds.length > 0) {
        map.flyToBounds(bounds, { padding: [50, 50], maxZoom: 14 });
      }
    }

    function renderListado(lista) {
      listadoDiv.innerHTML = '';

      if (lista.length === 0) {
        listadoDiv.innerHTML = '<p class="empty-state-text">No se encontraron resultados.</p>';
        return;
      }

      lista.forEach(item => {
        const card = document.createElement('div');
        const prio = (item.prioridad || 'bajo').toLowerCase();
        card.className = `ayuda-item-card prio-${prio}`;
        
        let badgeColor = 'secondary';
        const est = item.estado.toLowerCase();
        if (est === 'sin responder') badgeColor = 'danger';
        else if (est === 'en progreso') badgeColor = 'warning text-dark';
        else if (est === 'respondido') badgeColor = 'success';
        else if (est === 'resuelto') badgeColor = 'primary';

        card.innerHTML = `
          <div class="ayuda-item-header">
            <span class="ayuda-item-title">${item.voluntario || 'Solicitante Anónimo'}</span>
            <span class="badge-prio badge-prio-${prio}">
              ${item.prioridad.toUpperCase()}
            </span>
          </div>
          <p class="small text-muted mb-2"><i class="fas fa-map-marker-alt mr-1"></i>${item.direccion}</p>
          <p class="ayuda-item-desc">${item.detalle || 'Solicitud de apoyo en terreno.'}</p>
          <div class="ayuda-item-footer">
            <span class="badge badge-${badgeColor} px-2 py-1 text-uppercase" style="font-size: .7rem; border-radius: 4px;">
              ${item.estado}
            </span>
            <span><i class="far fa-clock mr-1"></i>${item.fecha}</span>
          </div>
        `;

        card.addEventListener('click', () => {
          const marker = marcadoresPorId[item.id];
          if (marker) {
            map.flyTo(marker.getLatLng(), 15, { duration: 0.5 });
            marker.openPopup();
          }
        });

        listadoDiv.appendChild(card);
      });
    }

    function aplicarFiltros() {
      const q = buscarNombre.value.trim().toLowerCase();
      const prio = prioridadFiltro.value.toLowerCase();
      const est = estadoFiltro.value.toLowerCase();

      const filtradas = datos.filter(item => {
        const nombreOk = q === '' || item.voluntario.toLowerCase().includes(q) || (item.detalle ?? '').toLowerCase().includes(q);
        const prioOk = prio === '' || item.prioridad === prio;
        const estOk = est === '' || item.estado === est;

        return nombreOk && prioOk && estOk;
      });

      renderListado(filtradas);
      renderMapa(filtradas);
    }

    buscarNombre.addEventListener('input', aplicarFiltros);
    prioridadFiltro.addEventListener('change', aplicarFiltros);
    estadoFiltro.addEventListener('change', aplicarFiltros);

    btnLimpiar.addEventListener('click', () => {
      buscarNombre.value = '';
      prioridadFiltro.value = '';
      estadoFiltro.value = '';
      aplicarFiltros();
    });

    // Cargar listado inicial
    aplicarFiltros();
  });
</script>
@endsection
