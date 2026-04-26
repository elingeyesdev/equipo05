@extends('layouts.app')

@section('subtitle', 'Dashboard')
@section('content_header_title', 'Panel Principal')

@section('content_body')
    <div class="row">
        {{-- Mapa principal con focos y biomasas --}}
        <div class="col-lg-8">
            <x-adminlte-card title="Mapa de Monitoreo" theme="light" icon="fas fa-map-marked-alt">
                <div id="map"></div>
                <div class="mt-2 text-sm text-muted">
                    <i class="fas fa-fire text-danger"></i> Puntos de calor (FIRMS) &nbsp;&nbsp;
                    <i class="fas fa-leaf text-success"></i> Áreas de biomasa
                </div>
            </x-adminlte-card>
        </div>

        {{-- Sidebar con biomasas listadas --}}
        <div class="col-lg-4">
            <x-adminlte-card 
            title="Áreas de Biomasa ({{ $biomasasCount }})"
            theme="success"
            icon="fas fa-leaf">
            {{-- Filtros por tipo de biomasa --}}
                <div class="mb-3">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-success dropdown-toggle btn-block" type="button" 
                                id="dropdownFiltros" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-filter"></i> Filtrar por tipo de biomasa
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownFiltros" style="padding: 10px;">
                            @php
                                $tiposBiomasa = \Modules\Incendios\Models\TipoBiomasa::select('id', 'tipo_biomasa', 'color')->get();
                            @endphp
                            @foreach($tiposBiomasa as $tipo)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input filtro-biomasa" 
                                           id="filtro-{{ $tipo->id }}" 
                                           value="{{ $tipo->id }}" 
                                           data-tipo="{{ $tipo->tipo_biomasa }}"
                                           checked>
                                    <label class="custom-control-label" for="filtro-{{ $tipo->id }}">
                                        <i class="fas fa-circle" style="color: {{ $tipo->color }}; font-size: 10px;"></i>
                                        {{ $tipo->tipo_biomasa }}
                                    </label>
                                </div>
                            @endforeach
                            <div class="dropdown-divider"></div>
                            <button class="btn btn-sm btn-outline-secondary btn-block" onclick="seleccionarTodos()">
                                <i class="fas fa-check-double"></i> Seleccionar Todos
                            </button>
                            <button class="btn btn-sm btn-outline-secondary btn-block" onclick="deseleccionarTodos()">
                                <i class="fas fa-times"></i> Deseleccionar Todos
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="biomasas-list" style="height: 460px; overflow-y: auto;">
                    <p class="text-muted text-center">
                        <i class="fas fa-spinner fa-spin"></i> Cargando biomasas...
                    </p>
                </div>
            </x-adminlte-card>
        </div>
    </div>

    {{-- Cards de clima y estadísticas --}}
    <div class="row">
        {{-- Clima Actual Completo --}}
        <div class="col-md-6">
            <x-adminlte-card title="Clima Actual - San José de Chiquitos" theme="info" icon="fas fa-cloud-sun">
                <div class="row">
                    <div class="col-6">
                        <div class="info-box mb-3 bg-light">
                            <span class="info-box-icon bg-info elevation-1">
                                <i class="fas fa-thermometer-half"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Temperatura</span>
                                <span class="info-box-number">
                                    @if(isset($weather['data']['current_weather']['temperature']))
                                        {{ $weather['data']['current_weather']['temperature'] }}°C
                                    @else
                                        --°C
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="info-box mb-3 bg-light">
                            <span class="info-box-icon bg-primary elevation-1">
                                <i class="fas fa-tint"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Humedad</span>
                                <span class="info-box-number">
                                    @if(isset($weather['data']['hourly']['relative_humidity_2m'][0]))
                                        {{ $weather['data']['hourly']['relative_humidity_2m'][0] }}%
                                    @else
                                        --%
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="info-box mb-3 bg-light">
                            <span class="info-box-icon bg-success elevation-1">
                                <i class="fas fa-wind"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Viento</span>
                                <span class="info-box-number">
                                    @if(isset($weather['data']['current_weather']['windspeed']))
                                        {{ $weather['data']['current_weather']['windspeed'] }} km/h
                                    @else
                                        -- km/h
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="info-box mb-3 bg-light">
                            <span class="info-box-icon bg-cyan elevation-1">
                                <i class="fas fa-cloud-rain"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Precipitación</span>
                                <span class="info-box-number">
                                    @if(isset($weather['data']['hourly']['precipitation'][0]))
                                        {{ $weather['data']['hourly']['precipitation'][0] }} mm
                                    @else
                                        0 mm
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="info-box mb-0 bg-light">
                            <span class="info-box-icon bg-warning elevation-1">
                                <i class="fas fa-compass"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Descripción</span>
                                <span class="info-box-number" style="font-size: 16px;">
                                    @php
                                        $temp = $weather['data']['current_weather']['temperature'] ?? 0;
                                        $precipitation = $weather['data']['hourly']['precipitation'][0] ?? 0;
                                        $humidity = $weather['data']['hourly']['relative_humidity_2m'][0] ?? 0;
                                        
                                        if ($precipitation > 0) {
                                            $desc = '🌧️ Lluvia';
                                        } elseif ($temp > 30) {
                                            $desc = '☀️ Caluroso y seco';
                                        } elseif ($temp > 25) {
                                            $desc = '🌤️ Cálido';
                                        } elseif ($temp > 20) {
                                            $desc = '⛅ Templado';
                                        } else {
                                            $desc = '🌥️ Fresco';
                                        }
                                        
                                        if ($humidity > 80 && $precipitation == 0) {
                                            $desc .= ' y húmedo';
                                        }
                                    @endphp
                                    {{ $desc }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
        
        {{-- Estadísticas --}}
        <div class="col-md-6">
            <x-adminlte-card title="Estadísticas del Sistema" theme="success" icon="fas fa-chart-bar">
                <div class="row">
                    <div class="col-6">
                        <div class="info-box mb-3 bg-light">
                            <span class="info-box-icon bg-danger elevation-1">
                                <i class="fas fa-fire"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Puntos de Calor</span>
                                <span class="info-box-number text-danger">
                                    {{ $firesCount }}
                                </span>
                                <small class="text-muted">Últimas 48 horas</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-6">
                        <div class="info-box mb-3 bg-light">
                            <span class="info-box-icon bg-success elevation-1">
                                <i class="fas fa-leaf"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Áreas de Biomasa</span>
                                <span class="info-box-number text-success">
                                    {{ $biomasasCount }}
                                </span>
                                <small class="text-muted">Aprobadas</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <div class="alert alert-info mb-0">
                            <h5><i class="icon fas fa-info-circle"></i> Estado del Sistema</h5>
                            <p class="mb-1">
                                <i class="fas fa-check-circle text-success"></i> 
                                <strong>Monitoreo activo</strong> - Datos actualizados automáticamente
                            </p>
                            <p class="mb-0">
                                <i class="fas fa-clock text-warning"></i> 
                                Última actualización: {{ now()->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            </x-adminlte-card>
        </div>
    </div>
@stop

@push('css')
<style>
    #map {
        height: 460px;
        width: 100%;
        border-radius: 4px;
    }

    .row:first-child .col-lg-4 .card {
        height: 615px;
        display: flex;
        flex-direction: column;
    }

    .row:first-child .col-lg-4 .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    #biomasas-list {
        flex: 1;
        overflow-y: auto;
    }    .weather-card .label {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .weather-card .value {
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
    }
    
    /* Estilos para marcadores de fuego personalizados */
    .custom-fire-marker {
        background: transparent !important;
        border: none !important;
    }
    
    /* Animación de pulso para marcadores de fuego */
    @keyframes fire-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(220, 38, 38, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
        }
    }
    
    .custom-fire-marker:hover > div > div {
        animation: fire-pulse 1.5s infinite;
    }
    
    /* Estilo mejorado para popups de fuego */
    .custom-fire-popup .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        padding: 0;
    }
    
    .custom-fire-popup .leaflet-popup-content {
        margin: 15px 20px;
        font-size: 14px;
        line-height: 1.5;
    }
    
    .custom-fire-popup .leaflet-popup-tip {
        background: white;
    }
    
    /* Transición suave para marcadores */
    .leaflet-marker-icon {
        transition: transform 0.2s ease;
    }
    
    .leaflet-marker-icon:hover {
        transform: scale(1.1);
        z-index: 1000 !important;
    }
</style>
@endpush

@push('js')
<script>
    // Esperar a que todo esté cargado (Leaflet incluido)
    function initDashboard() {
        // Verificar que Leaflet esté disponible
        if (typeof L === 'undefined') {
            console.warn('Leaflet aún no está cargado, reintentando en 100ms...');
            setTimeout(initDashboard, 100);
            return;
        }
        
        console.log('✓ Leaflet cargado correctamente');
        console.log('✓ Inicializando dashboard...');
        
        // Initialize map centered on San José de Chiquitos, Bolivia
        const map = L.map('map').setView([-17.8857, -60.7556], 12);
        console.log('Mapa inicializado:', map);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
    }).addTo(map);

    // Función para crear icono de fuego personalizado según intensidad
    function createFireIcon(fire) {
        const isCluster = fire.is_cluster || false;
        const size = fire.cluster_size || 1;
        
        // Determinar color según confianza
        let color = '#ff6b35'; // naranja por defecto
        if (fire.confidence === 'h') color = '#dc2626'; // rojo para alta confianza
        else if (fire.confidence === 'l') color = '#fb923c'; // naranja claro para baja
        
        // Determinar tamaño del icono según cluster
        let iconSize = isCluster && size > 1 ? [35, 45] : [28, 38];
        
        // Crear HTML personalizado para el icono
        const iconHtml = `
            <div style="position: relative;">
                <div style="
                    background: ${color};
                    width: ${iconSize[0]}px;
                    height: ${iconSize[0]}px;
                    border-radius: 50% 50% 50% 0;
                    transform: rotate(-45deg);
                    border: 3px solid white;
                    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                ">
                    <i class="fas fa-fire" style="
                        color: white;
                        font-size: ${isCluster && size > 1 ? '18px' : '14px'};
                        transform: rotate(45deg);
                    "></i>
                </div>
                ${isCluster && size > 1 ? `
                    <div style="
                        position: absolute;
                        top: -8px;
                        right: -8px;
                        background: #1e40af;
                        color: white;
                        border-radius: 50%;
                        width: 22px;
                        height: 22px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 11px;
                        font-weight: bold;
                        border: 2px solid white;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                    ">${size}</div>
                ` : ''}
            </div>
        `;
        
        return L.divIcon({
            html: iconHtml,
            className: 'custom-fire-marker',
            iconSize: [iconSize[0], iconSize[1]],
            iconAnchor: [iconSize[0]/2, iconSize[1]],
            popupAnchor: [0, -iconSize[1] + 5]
        });
    }

    // Función para crear popup mejorado
    function createFirePopup(fire) {
        const isCluster = fire.is_cluster || false;
        const size = fire.cluster_size || 1;
        const confidence = fire.confidence || 'n';
        const confidenceText = confidence === 'h' ? 'Alta' : confidence === 'l' ? 'Baja' : 'Normal';
        const confidenceColor = confidence === 'h' ? '#dc2626' : confidence === 'l' ? '#fb923c' : '#f59e0b';
        
        // Calcular intensidad basada en FRP para el simulador
        const frp = fire.frp || 5;
        const intensity = Math.min(5, Math.max(1, Math.round(frp / 50)));
        
        // Crear URL para el simulador con parámetros del foco
        const simulatorUrl = `/simulaciones/simulator?fire_lat=${fire.lat}&fire_lng=${fire.lng}&fire_intensity=${intensity}&fire_frp=${frp}`;
        
        // Crear URL para predicción con parámetros del foco
        const predictionUrl = `/predictions/create?fire_lat=${fire.lat}&fire_lng=${fire.lng}&fire_intensity=${intensity}&fire_frp=${frp}`;
        
        return `
            <div style="min-width: 250px; font-family: system-ui, -apple-system, sans-serif;">
                <div style="
                    background: linear-gradient(135deg, #dc2626 0%, #f59e0b 100%);
                    color: white;
                    padding: 12px;
                    margin: -15px -20px 10px -20px;
                    border-radius: 3px 3px 0 0;
                ">
                    <h4 style="margin: 0; font-size: 16px; font-weight: 600;">
                        <i class="fas fa-fire"></i> 
                        ${isCluster && size > 1 ? 'Punto Caliente' : 'Foco de Calor'}
                    </h4>
                </div>
                
                <div style="padding: 5px 0;">
                    ${isCluster && size > 1 ? `
                        <div style="
                            background: #eff6ff;
                            border-left: 3px solid #3b82f6;
                            padding: 8px 12px;
                            margin-bottom: 10px;
                            border-radius: 3px;
                        ">
                            <strong style="color: #1e40af;">
                                <i class="fas fa-layer-group"></i> ${size} focos agrupados
                            </strong>
                        </div>
                    ` : ''}
                    
                    <table style="width: 100%; font-size: 13px;">
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280;">
                                <i class="fas fa-calendar-alt" style="width: 20px;"></i> Fecha:
                            </td>
                            <td style="padding: 5px 0; font-weight: 500;">
                                ${fire.date}
                            </td>
                        </tr>
                        ${fire.time ? `
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280;">
                                <i class="fas fa-clock" style="width: 20px;"></i> Hora:
                            </td>
                            <td style="padding: 5px 0; font-weight: 500;">
                                ${fire.time.substring(0,2)}:${fire.time.substring(2,4)}
                            </td>
                        </tr>
                        ` : ''}
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280;">
                                <i class="fas fa-shield-alt" style="width: 20px;"></i> Confianza:
                            </td>
                            <td style="padding: 5px 0;">
                                <span style="
                                    background: ${confidenceColor};
                                    color: white;
                                    padding: 2px 8px;
                                    border-radius: 10px;
                                    font-size: 11px;
                                    font-weight: 600;
                                ">${confidenceText}</span>
                            </td>
                        </tr>
                        ${fire.frp ? `
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280;">
                                <i class="fas fa-bolt" style="width: 20px;"></i> Potencia:
                            </td>
                            <td style="padding: 5px 0; font-weight: 500;">
                                ${fire.frp.toFixed(1)} MW
                            </td>
                        </tr>
                        ` : ''}
                        <tr>
                            <td style="padding: 5px 0; color: #6b7280;">
                                <i class="fas fa-map-marker-alt" style="width: 20px;"></i> Ubicación:
                            </td>
                            <td style="padding: 5px 0; font-family: monospace; font-size: 11px;">
                                ${fire.lat.toFixed(4)}, ${fire.lng.toFixed(4)}
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Botones para simular y predecir -->
                <div style="
                    margin-top: 12px;
                    padding-top: 12px;
                    border-top: 1px solid #e5e7eb;
                    display: flex;
                    gap: 8px;
                ">
                    <a href="${simulatorUrl}" 
                       style="
                           flex: 1;
                           display: block;
                           background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                           color: white;
                           text-align: center;
                           padding: 10px 12px;
                           border-radius: 6px;
                           text-decoration: none;
                           font-weight: 600;
                           font-size: 13px;
                           transition: all 0.2s ease;
                           box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                       "
                       onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                        <i class="fas fa-play-circle"></i> Simular
                    </a>
                    <a href="${predictionUrl}" 
                       style="
                           flex: 1;
                           display: block;
                           background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
                           color: white;
                           text-align: center;
                           padding: 10px 12px;
                           border-radius: 6px;
                           text-decoration: none;
                           font-weight: 600;
                           font-size: 13px;
                           transition: all 0.2s ease;
                           box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                       "
                       onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.2)';"
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.1)';">
                        <i class="fas fa-chart-line"></i> Predecir
                    </a>
                </div>
                
                <div style="
                    margin-top: 8px;
                    font-size: 11px;
                    color: #9ca3af;
                    text-align: center;
                ">
                    <i class="fas fa-satellite"></i> NASA FIRMS - VIIRS
                </div>
            </div>
        `;
    }

    // Load fire hotspots con visualización mejorada
    fetch('/api/fires')
        .then(res => res.json())
        .then(result => {
            if (result.ok && result.data && result.data.length > 0) {
                console.log(`🔥 Cargados ${result.count} puntos calientes`);
                
                result.data.forEach(fire => {
                    const marker = L.marker(
                        [fire.lat, fire.lng], 
                        { icon: createFireIcon(fire) }
                    ).addTo(map);
                    
                    marker.bindPopup(createFirePopup(fire), {
                        maxWidth: 300,
                        className: 'custom-fire-popup'
                    });
                });
            } else {
                console.log('ℹ️ No se encontraron focos de calor en los últimos 2 días');
            }
        })
        .catch(err => console.error('Error loading fires:', err));

    // Variables globales para filtrado
    let allBiomasas = null;
    let biomasaLayer = null;

    // Load biomasas
    console.log('Intentando cargar biomasas desde /dashboard/biomasas...');
    fetch('/dashboard/biomasas', {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => {
            console.log('Respuesta recibida:', res.status, res.statusText);
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(geojson => {
            console.log('Biomasas cargadas:', geojson);
            console.log('Features encontrados:', geojson.features ? geojson.features.length : 0);
            
            // Guardar todas las biomasas
            allBiomasas = geojson;
            
            // Renderizar inicialmente todas las biomasas
            renderBiomasas(geojson);
            
            // Agregar event listeners a los filtros
            document.querySelectorAll('.filtro-biomasa').forEach(checkbox => {
                checkbox.addEventListener('change', filtrarBiomasas);
            });
        })
        .catch(err => {
            console.error('Error loading biomasas:', err);
            const listContainer = document.getElementById('biomasas-list');
            listContainer.innerHTML = `<p class="text-danger text-center">
                <i class="fas fa-exclamation-triangle"></i> Error al cargar biomasas<br>
                <small>${err.message}</small>
            </p>`;
        });
    
    // Función para renderizar biomasas en el mapa
    function renderBiomasas(geojson) {
        // Remover capa anterior si existe
        if (biomasaLayer) {
            map.removeLayer(biomasaLayer);
        }
        
        biomasaLayer = L.geoJSON(geojson, {
            style: function(feature) {
                const color = feature.properties.color || '#28a745';
                return {
                    color: color,
                    weight: 2,
                    fillColor: color,
                    fillOpacity: 0.3
                };
            },
            onEachFeature: (feature, layer) => {
                const props = feature.properties;
                layer.bindPopup(`
                    <strong><i class="fas fa-leaf text-success"></i> Área de Biomasa</strong><br>
                    <strong>Ubicación:</strong> ${props.ubicacion}<br>
                    <strong>Área:</strong> ${props.area}<br>
                    <strong>Densidad:</strong> ${props.densidad}<br>
                    <strong>Tipo:</strong> ${props.tipo}<br>
                    <strong>Fecha:</strong> ${props.fecha}
                    ${props.descripcion ? `<br><strong>Descripción:</strong> ${props.descripcion}` : ''}
                `);
            }
        }).addTo(map);

        // Build sidebar list
        const listContainer = document.getElementById('biomasas-list');
        if (geojson.features.length === 0) {
            listContainer.innerHTML = '<p class="text-muted text-center">No hay biomasas con los filtros seleccionados</p>';
        } else {
            let html = '<ul class="list-unstyled">';
            geojson.features.forEach((feature, index) => {
                const props = feature.properties;
                const color = props.color || '#28a745';
                const densidadClass = props.densidad === 'alta' ? 'success' : 
                                     props.densidad === 'media' ? 'warning' : 'info';
                html += `
                    <li class="mb-2 border-bottom pb-2">
                        <strong style="color: ${color};"><i class="fas fa-leaf"></i> ${props.tipo}</strong>
                        <br><small class="text-muted">${props.fecha}</small>
                        <br><span class="badge badge-${densidadClass}">Densidad: ${props.densidad}</span>
                        <br><small>Área: ${props.area}</small>
                    </li>
                `;
            });
            html += '</ul>';
            listContainer.innerHTML = html;
        }

        // Fit map to show all features if any exist
        if (geojson.features.length > 0) {
            map.fitBounds(biomasaLayer.getBounds(), { padding: [20, 20] });
        }
    }
    
    // Función para filtrar biomasas según checkboxes
    function filtrarBiomasas() {
        if (!allBiomasas) return;
        
        // Obtener tipos seleccionados
        const tiposSeleccionados = Array.from(document.querySelectorAll('.filtro-biomasa:checked'))
            .map(cb => cb.getAttribute('data-tipo'));
        
        console.log('Tipos seleccionados:', tiposSeleccionados);
        
        // Filtrar features
        const filteredGeoJSON = {
            type: 'FeatureCollection',
            features: allBiomasas.features.filter(feature => 
                tiposSeleccionados.includes(feature.properties.tipo)
            )
        };
        
        console.log('Biomasas filtradas:', filteredGeoJSON.features.length);
        
        // Re-renderizar
        renderBiomasas(filteredGeoJSON);
    }
    
    // Función para seleccionar todos los filtros
    window.seleccionarTodos = function() {
        document.querySelectorAll('.filtro-biomasa').forEach(cb => {
            cb.checked = true;
        });
        filtrarBiomasas();
    };
    
    // Función para deseleccionar todos los filtros
    window.deseleccionarTodos = function() {
        document.querySelectorAll('.filtro-biomasa').forEach(cb => {
            cb.checked = false;
        });
        filtrarBiomasas();
    };
    } // Fin de initDashboard
    
    // Iniciar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboard);
    } else {
        // DOM ya está listo, iniciar inmediatamente
        initDashboard();
    }
</script>
@endpush
