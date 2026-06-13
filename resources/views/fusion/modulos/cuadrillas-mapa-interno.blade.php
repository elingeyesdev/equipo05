@extends('layouts.app')

@section('title', 'Mapa en Tiempo Real')

@section('content')
<div class="container-fluid mt-4">
    
    @if(request()->has('success') || session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ request()->get('success') ?? session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            
            <!-- Tarjeta del Mapa -->
            <div class="card card-outline card-danger shadow-lg">
                <div class="card-header bg-danger text-white d-flex align-items-center justify-content-between">
                    <h3 class="card-title font-weight-bold m-0">
                        <i class="fas fa-map-marked-alt mr-2"></i> Mapa de Focos de Calor y Equipos
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('cuadrillas.reportes') }}" class="btn btn-warning btn-sm font-weight-bold text-dark">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Nuevo Reporte Rápido
                        </a>
                    </div>
                </div>

                <!-- Filtros de Tiempo -->
                <div class="card-body bg-light p-2 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <h6 class="m-0 font-weight-bold text-secondary">
                                <i class="fas fa-satellite text-danger mr-2"></i> Datos NASA FIRMS - Filtro Temporal:
                            </h6>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-danger font-weight-bold" id="btn-24h" onclick="cargarNASA(1)">
                                    <i class="fas fa-clock mr-1"></i> Últimas 24 Horas
                                </button>
                                <button type="button" class="btn btn-outline-danger font-weight-bold active" id="btn-2d" onclick="cargarNASA(2)">
                                    <i class="fas fa-calendar-day mr-1"></i> Últimos 2 Días
                                </button>
                                <button type="button" class="btn btn-outline-danger font-weight-bold" id="btn-7d" onclick="cargarNASA(7)">
                                    <i class="fas fa-calendar-week mr-1"></i> Últimos 7 Días
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenedor del Mapa -->
                <div class="card-body p-0">
                    <div id="mapa-tiempo-real" style="height: 550px; width: 100%;"></div>
                </div>

                <!-- Pie del Mapa -->
                <div class="card-footer bg-white py-2">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 1.5rem; font-size: 0.85rem;">
                                <span><i class="fas fa-satellite mr-1 text-danger"></i> NASA FIRMS (Satélite)</span>
                                <span><i class="fas fa-users mr-1 text-primary"></i> Equipos de Cuadrillas</span>
                                <span><i class="fas fa-bullhorn mr-1 text-warning"></i> Reportes Ciudadanos</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right mt-2 mt-md-0">
                            <small class="text-muted">
                                Última actualización: <span id="lbl-update-time">{{ now()->format('d/m/Y H:i') }}</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Cajas de Estadísticas Inferiores -->
    <div class="row">
        <!-- Equipos Desplegados -->
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-primary shadow">
                <div class="inner">
                    <h3 id="lbl-equipos-count">{{ $countEquiposDesplegados }}</h3>
                    <p>Equipos Desplegados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="small-box-footer py-2 text-white-50 text-center font-weight-bold" style="font-size: 0.85rem;">
                    Equipos en el mapa
                </div>
            </div>
        </div>

        <!-- Reportes en Mapa -->
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-warning text-dark shadow">
                <div class="inner">
                    <h3 id="lbl-reportes-count">{{ $countReportes }}</h3>
                    <p>Reportes en Mapa</p>
                </div>
                <div class="icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <a href="{{ route('cuadrillas.reportes') }}" class="small-box-footer text-dark" style="color: #212529 !important;">
                    Ver Reportes <i class="fas fa-arrow-circle-right text-dark"></i>
                </a>
            </div>
        </div>

        <!-- Focos NASA FIRMS -->
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-danger shadow">
                <div class="inner">
                    <h3 id="lbl-nasa-count">0</h3>
                    <p>Focos NASA FIRMS</p>
                </div>
                <div class="icon">
                    <i class="fas fa-satellite"></i>
                </div>
                <div class="small-box-footer py-2">
                    Datos del satélite VIIRS
                </div>
            </div>
        </div>

        <!-- Último Reporte -->
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-info shadow">
                <div class="inner">
                    <h3 id="lbl-ultimo-reporte" style="font-size: 1.8rem; line-height: 1.2; padding: 0.15rem 0;">{{ $ultimoReporte }}</h3>
                    <p>Último Reporte</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="small-box-footer py-2">
                    Fecha de recepción
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
<style>
    .legend {
        background: #ffffff;
        padding: 12px;
        border-radius: 6px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        font-size: 0.8rem;
        line-height: 1.5;
        color: #333;
        border: 1px solid #dee2e6;
        min-width: 170px;
    }
    .legend h6 {
        font-size: 0.85rem;
        font-weight: 700;
        margin: 0 0 8px 0;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 4px;
    }
    .legend .section-title {
        font-weight: 600;
        margin-top: 6px;
        margin-bottom: 4px;
        font-size: 0.75rem;
        color: #6c757d;
    }
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 4px;
    }
    .legend i {
        width: 11px;
        height: 11px;
        display: inline-block;
        border-radius: 50%;
        margin-right: 8px;
        flex-shrink: 0;
        border: 1px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .equipo-marker-div, .reporte-marker-div {
        background: none;
        border: none;
    }
</style>
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
$(document).ready(function() {
    // Inicializar mapa centrado en Bolivia
    const mapa = L.map('mapa-tiempo-real').setView([-17.8, -63.1], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapa);

    // Capas del mapa
    const capaNasaFirms = L.markerClusterGroup({
        iconCreateFunction: function(cluster) {
            return L.divIcon({
                html: `<div style="background-color: #dc3545; color: white; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.3);">${cluster.getChildCount()}</div>`,
                className: 'nasa-cluster',
                iconSize: [36, 36]
            });
        }
    });
    const capaEquipos = L.layerGroup();
    const capaReportes = L.layerGroup();

    mapa.addLayer(capaNasaFirms);
    mapa.addLayer(capaEquipos);
    mapa.addLayer(capaReportes);

    // Límites de Bolivia para filtrar focos
    const LIMITES_BOLIVIA = { minLat: -22.9, maxLat: -9.7, minLng: -69.6, maxLng: -57.5 };
    const NASA_API_KEY = '1ae0346a287432156ada4abb791d57cd';
    const NASA_API_BASE = 'https://firms.modaps.eosdis.nasa.gov/api/area/csv';

    // Agregar Leyenda
    function agregarLeyenda() {
        const legend = L.control({ position: 'bottomright' });
        legend.onAdd = function () {
            const div = L.DomUtil.create('div', 'legend');
            div.innerHTML = `
                <h6>Información</h6>
                <div class="section-title">NASA FIRMS:</div>
                <div class="legend-item"><i style="background:#dc3545"></i>Alta Confianza (≥80%)</div>
                <div class="legend-item"><i style="background:#ffa500"></i>Media Confianza (50-79%)</div>
                <div class="legend-item"><i style="background:#00ced1"></i>Baja Confianza (&lt;50%)</div>
                <div class="section-title">Otros:</div>
                <div class="legend-item"><i style="background:#007bff"></i>Equipo de Cuadrilla</div>
                <div class="legend-item"><i style="background:#ff9800"></i>Reporte Ciudadano</div>
            `;
            return div;
        };
        legend.addTo(mapa);
    }
    agregarLeyenda();

    // Retorna icono según la confianza de NASA FIRMS
    function obtenerIconoFirms(confianza) {
        let color = '#00ced1'; // Baja
        let size = 8;
        
        if (confianza === 'h' || parseFloat(confianza) >= 80) {
            color = '#dc3545'; // Alta
            size = 12;
        } else if (confianza === 'n' || (parseFloat(confianza) >= 50 && parseFloat(confianza) < 80)) {
            color = '#ffa500'; // Media
            size = 10;
        }
        
        return L.divIcon({
            html: `<div style="background-color: ${color}; width: ${size}px; height: ${size}px; border-radius: 50%; border: 1.5px solid #fff; box-shadow: 0 0 5px rgba(0,0,0,0.5)"></div>`,
            className: '',
            iconSize: [size, size],
            iconAnchor: [size/2, size/2]
        });
    }

    // Cargar Focos satelitales NASA FIRMS
    window.cargarNASA = async function(dias = 2) {
        // Actualizar estado activo de los botones del banner
        $('.btn-group button').removeClass('active');
        if (dias === 1) $('#btn-24h').addClass('active');
        else if (dias === 2) $('#btn-2d').addClass('active');
        else if (dias === 7) $('#btn-7d').addClass('active');

        capaNasaFirms.clearLayers();
        $('#lbl-nasa-count').text('0');

        const url = `${NASA_API_BASE}/${NASA_API_KEY}/VIIRS_NOAA21_NRT/world/${dias}`;
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error de conexión con NASA FIRMS');
            
            const csv = await response.text();
            const lines = csv.trim().split('\n');
            if (lines.length < 2) return;

            const headers = lines[0].split(',');
            let totalFocos = 0;

            for (let i = 1; i < lines.length; i++) {
                const cols = lines[i].split(',');
                if (cols.length !== headers.length) continue;
                
                const row = {};
                headers.forEach((h, idx) => row[h.trim()] = cols[idx].trim());
                
                const lat = parseFloat(row.latitude);
                const lng = parseFloat(row.longitude);

                // Filtrar dentro del cuadro geográfico de Bolivia
                if (lat >= LIMITES_BOLIVIA.minLat && lat <= LIMITES_BOLIVIA.maxLat && 
                    lng >= LIMITES_BOLIVIA.minLng && lng <= LIMITES_BOLIVIA.maxLng) {
                    
                    totalFocos++;
                    
                    const marker = L.marker([lat, lng], { icon: obtenerIconoFirms(row.confidence) });
                    const timeFormatted = row.acq_time ? row.acq_time.substring(0, 2) + ':' + row.acq_time.substring(2, 4) : 'N/A';
                    
                    marker.bindPopup(`
                        <div style="font-size: 12px; line-height: 1.4;">
                            <strong style="color: #dc3545; font-size: 13px;"><i class="fas fa-satellite"></i> Foco Satelital</strong><br/>
                            <hr style="margin: 4px 0 8px 0;">
                            <b>Fecha:</b> ${row.acq_date || 'N/A'}<br/>
                            <b>Hora:</b> ${timeFormatted} UTC<br/>
                            <b>FRP:</b> ${row.frp || 'N/A'} MW<br/>
                            <b>Confianza:</b> ${row.confidence || 'N/A'}<br/>
                            <b>Satélite:</b> ${row.satellite || 'VIIRS'}<br/>
                            <b>Coordenadas:</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
                        </div>
                    `);
                    capaNasaFirms.addLayer(marker);
                }
            }

            $('#lbl-nasa-count').text(totalFocos);
            ajustarLimitesMapa();

        } catch (error) {
            console.error('Error al cargar NASA FIRMS:', error);
            $('#lbl-nasa-count').text('Error');
        }
    }

    // Cargar Brigadas / Equipos desplegados
    function cargarEquipos() {
        capaEquipos.clearLayers();
        fetch('{{ route("publico.cuadrillas.equipos-api") }}')
            .then(res => res.json())
            .then(data => {
                $('#lbl-equipos-count').text(data.length);
                
                data.forEach(eq => {
                    if (eq.ubicacion && eq.ubicacion.coordinates) {
                        const [lng, lat] = eq.ubicacion.coordinates;

                        const blueIconHtml = '<div style="background-color: #007bff; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"><i class="fas fa-users" style="font-size: 13px;"></i></div>';
                        
                        const marker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                html: blueIconHtml,
                                className: 'equipo-marker-div',
                                iconSize: [36, 36],
                                iconAnchor: [18, 18]
                            })
                        }).addTo(capaEquipos);

                        const estadoBadge = eq.estado 
                            ? `<span class="badge shadow-sm" style="background-color: ${eq.estado.color || '#28a745'}; color: white;">${eq.estado.nombre}</span>` 
                            : '<span class="badge badge-success shadow-sm">Activo</span>';

                        marker.bindPopup(`
                            <div style="font-size: 12px; line-height: 1.4;">
                                <strong style="color: #007bff; font-size: 13px;"><i class="fas fa-users"></i> ${eq.nombre_equipo}</strong><br/>
                                <hr style="margin: 4px 0 8px 0;">
                                <b>Integrantes:</b> ${eq.cantidad_integrantes || 0}<br/>
                                <b>Estado:</b> ${estadoBadge}<br/>
                                <b>Ubicación:</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
                            </div>
                        `);
                    }
                });
                ajustarLimitesMapa();
            })
            .catch(err => {
                console.error('Error al cargar equipos:', err);
                $('#lbl-equipos-count').text('Error');
            });
    }

    // Cargar Reportes de incidentes ciudadanos
    function cargarReportes() {
        capaReportes.clearLayers();
        fetch('{{ route("publico.cuadrillas.reportes-api") }}')
            .then(res => res.json())
            .then(data => {
                $('#lbl-reportes-count').text(data.length);
                
                // Actualizar campo "Último Reporte"
                if (data.length > 0) {
                    const fechas = data.map(r => r.fecha_hora).filter(Boolean);
                    if (fechas.length > 0) {
                        const masReciente = new Date(fechas.sort().pop());
                        const dia = String(masReciente.getDate()).padStart(2, '0');
                        const mes = String(masReciente.getMonth() + 1).padStart(2, '0');
                        const anio = masReciente.getFullYear();
                        $('#lbl-ultimo-reporte').text(`${dia}/${mes}/${anio}`);
                    }
                }

                data.forEach(rep => {
                    if (rep.ubicacion && rep.ubicacion.coordinates) {
                        const [lng, lat] = rep.ubicacion.coordinates;

                        const orangeIconHtml = '<div style="background-color: #ff9800; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3.5px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"><i class="fas fa-bullhorn" style="font-size: 11px;"></i></div>';

                        const marker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                html: orangeIconHtml,
                                className: 'reporte-marker-div',
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            })
                        }).addTo(capaReportes);

                        const fechaHoraStr = rep.fecha_hora 
                            ? new Date(rep.fecha_hora).toLocaleString('es-BO') 
                            : 'N/A';
                        
                        const gravedadStr = rep.niveles_gravedad && rep.niveles_gravedad.nombre 
                            ? `<span class="badge badge-danger">${rep.niveles_gravedad.nombre}</span>` 
                            : '';

                        const tipoStr = rep.tipos_incidente && rep.tipos_incidente.nombre 
                            ? rep.tipos_incidente.nombre 
                            : 'Incendio / Incidente';

                        marker.bindPopup(`
                            <div style="font-size: 12px; line-height: 1.4; max-width: 250px;">
                                <strong style="color: #ff9800; font-size: 13px;"><i class="fas fa-bullhorn"></i> Reporte Ciudadano</strong><br/>
                                <hr style="margin: 4px 0 8px 0;">
                                <b>Lugar:</b> ${rep.nombre_lugar || 'Reporte de incidente'}<br/>
                                <b>Fecha/Hora:</b> ${fechaHoraStr}<br/>
                                <b>Tipo:</b> ${tipoStr}<br/>
                                ${gravedadStr ? `<b>Gravedad:</b> ${gravedadStr}<br/>` : ''}
                                <b>Reportado por:</b> ${rep.nombre_reportante || 'Anónimo'}<br/>
                                <b>Comentarios:</b> ${rep.comentario_adicional || 'Sin comentarios adicionales'}<br/>
                                <b>Coordenadas:</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
                            </div>
                        `);
                    }
                });
                ajustarLimitesMapa();
            })
            .catch(err => {
                console.error('Error al cargar reportes:', err);
                $('#lbl-reportes-count').text('Error');
            });
    }

    // Ajustar límites de cámara del mapa para mostrar marcadores colectivos
    let tieneAjustadoInicial = false;
    function ajustarLimitesMapa() {
        if (tieneAjustadoInicial) return;

        const marcadores = [];
        capaNasaFirms.eachLayer(m => marcadores.push(m));
        capaEquipos.eachLayer(m => marcadores.push(m));
        capaReportes.eachLayer(m => marcadores.push(m));

        if (marcadores.length > 0) {
            const grupo = new L.featureGroup(marcadores);
            mapa.fitBounds(grupo.getBounds().pad(0.1));
            tieneAjustadoInicial = true;
        }
    }

    // Inicializar cargas de datos
    cargarEquipos();
    cargarReportes();
    cargarNASA(2); // Cargar últimos 2 días por defecto
});
</script>
@endpush
