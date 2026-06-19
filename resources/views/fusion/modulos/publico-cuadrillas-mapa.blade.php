<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Focos de Calor en Tiempo Real</title>
    <!-- Bootstrap 4 & AdminLTE stylesheet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet & Clustering CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        .header-section {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            margin-bottom: 1.5rem;
        }
        .header-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
            margin: 0;
        }
        .breadcrumb-custom {
            font-size: 0.9rem;
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            align-items: center;
        }
        .breadcrumb-custom a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb-custom a:hover {
            text-decoration: underline;
        }
        .breadcrumb-custom .separator {
            margin: 0 0.5rem;
            color: #6c757d;
        }
        .breadcrumb-custom .active-item {
            color: #6c757d;
        }
        .card-map {
            border: none;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background-color: #ffffff;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .card-header-custom {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-title-custom {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            color: #343a40;
        }
        .card-title-custom i {
            margin-right: 0.5rem;
            color: #495057;
        }
        .filter-banner {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
        }
        .filter-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            margin: 0;
            display: flex;
            align-items: center;
        }
        .filter-title i {
            margin-right: 0.5rem;
            color: #dc3545;
        }
        #mapa-tiempo-real {
            height: 600px;
            width: 100%;
        }
        .card-footer-custom {
            background-color: #ffffff;
            border-top: 1px solid #dee2e6;
            padding: 0.75rem 1.25rem;
        }
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
        .info-box {
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            border-radius: 4px;
            background-color: #ffffff;
            display: flex;
            margin-bottom: 1.5rem;
            min-height: 80px;
            border: 1px solid #dee2e6;
        }
        .info-box-icon {
            border-top-left-radius: 3px;
            border-bottom-left-radius: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 70px;
            font-size: 1.75rem;
            color: #ffffff;
        }
        .info-box-content {
            padding: 10px 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .info-box-text {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }
        .info-box-number {
            font-size: 1.4rem;
            font-weight: 700;
            color: #212529;
            margin: 0;
        }
        /* Custom markers styles */
        .equipo-marker-div, .reporte-marker-div {
            background: none;
            border: none;
        }
    </style>
</head>
<body>

<!-- Cabecera -->
<div class="header-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="header-title">Focos de Calor</h1>
            </div>
            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                <div class="breadcrumb-custom justify-content-md-end">
                    <a href="/">Inicio</a>
                    <span class="separator">/</span>
                    <span class="active-item">Focos de Calor</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenido Principal -->
<div class="container">
    
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
            <div class="card card-map">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fas fa-map-marked-alt"></i> Mapa de Focos de Calor y Equipos
                    </h3>
                    <div>
                        <a href="{{ route('publico.cuadrillas.reporte') }}" class="btn btn-danger btn-sm font-weight-bold">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Reportar Incidente
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm font-weight-bold ml-1">
                            <i class="fas fa-sign-in-alt mr-1"></i> Iniciar sesión
                        </a>
                    </div>
                </div>

                <!-- Filtros de Tiempo -->
                <div class="filter-banner">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <h6 class="filter-title">
                                <i class="fas fa-satellite"></i> Datos NASA FIRMS - Filtro Temporal:
                            </h6>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary font-weight-bold" id="btn-24h" onclick="cargarNASA(1)">
                                    <i class="fas fa-clock mr-1"></i> Últimas 24 Horas
                                </button>
                                <button type="button" class="btn btn-outline-primary font-weight-bold active" id="btn-2d" onclick="cargarNASA(2)">
                                    <i class="fas fa-calendar-day mr-1"></i> Últimos 2 Días
                                </button>
                                <button type="button" class="btn btn-outline-primary font-weight-bold" id="btn-7d" onclick="cargarNASA(7)">
                                    <i class="fas fa-calendar-week mr-1"></i> Últimos 7 Días
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenedor del Mapa -->
                <div id="mapa-tiempo-real"></div>

                <!-- Pie del Mapa -->
                <div class="card-footer-custom">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 1.5rem; font-size: 0.85rem;">
                                <span><i class="fas fa-satellite mr-1" style="color: #dc3545;"></i> NASA FIRMS</span>
                                <span><i class="fas fa-users mr-1" style="color: #007bff;"></i> Equipos</span>
                                <span><i class="fas fa-bullhorn mr-1" style="color: #ff9800;"></i> Reportes</span>
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
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Equipos Desplegados</span>
                    <span class="info-box-number" id="lbl-equipos-count">{{ $countEquiposDesplegados }}</span>
                </div>
            </div>
        </div>

        <!-- Reportes en Mapa -->
        <div class="col-lg-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon" style="background-color: #ff9800; color: white;"><i class="fas fa-bullhorn"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Reportes en Mapa</span>
                    <span class="info-box-number" id="lbl-reportes-count">{{ $countReportes }}</span>
                </div>
            </div>
        </div>

        <!-- Focos NASA FIRMS -->
        <div class="col-lg-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-satellite"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Focos NASA FIRMS</span>
                    <span class="info-box-number" id="lbl-nasa-count">0</span>
                </div>
            </div>
        </div>

        <!-- Último Reporte -->
        <div class="col-lg-3 col-sm-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Último Reporte</span>
                    <span class="info-box-number" style="font-size: 1.15rem; word-break: break-all;" id="lbl-ultimo-reporte">{{ $ultimoReporte }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

<script>
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

    const INCENDIOS_API = @json(rtrim(url('api/incendios'), '/'));
    const CHIQUITANIA_AREA = '-62.5,-18.5,-57.5,-14.5';

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
                <div class="legend-item"><i style="background:#007bff"></i>Equipo de Bomberos</div>
                <div class="legend-item"><i style="background:#ff9800"></i>Reporte de Incidente</div>
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

    async function cargarNASA(dias = 2) {
        $('.btn-group button').removeClass('active');
        if (dias === 1) $('#btn-24h').addClass('active');
        else if (dias === 2) $('#btn-2d').addClass('active');
        else if (dias === 7) $('#btn-7d').addClass('active');

        capaNasaFirms.clearLayers();
        $('#lbl-nasa-count').text('0');

        const url = `${INCENDIOS_API}/fires?cluster=true&radius=20&days=${dias}&area=${encodeURIComponent(CHIQUITANIA_AREA)}`;
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Error al cargar datos FIRMS');
            const json = await response.json();
            const fires = json.data || [];
            let totalFocos = 0;

            fires.forEach(fire => {
                const lat = parseFloat(fire.lat);
                const lng = parseFloat(fire.lng);
                if (isNaN(lat) || isNaN(lng)) return;
                totalFocos++;
                const marker = L.marker([lat, lng], { icon: obtenerIconoFirms(fire.confidence) });
                marker.bindPopup(`
                    <div style="font-size: 12px; line-height: 1.4;">
                        <strong style="color: #dc3545; font-size: 13px;"><i class="fas fa-satellite"></i> Foco Satelital</strong><br/>
                        <hr style="margin: 4px 0 8px 0;">
                        <b>Fecha:</b> ${fire.date || fire.acq_date || 'N/A'}<br/>
                        <b>FRP:</b> ${fire.frp || 'N/A'} MW<br/>
                        <b>Confianza:</b> ${fire.confidence || 'N/A'}<br/>
                        <b>Coordenadas:</b> ${lat.toFixed(6)}, ${lng.toFixed(6)}
                    </div>
                `);
                capaNasaFirms.addLayer(marker);
            });

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
                            ? `<span class="badge" style="background-color: ${eq.estado.color || '#28a745'}; color: white;">${eq.estado.nombre}</span>` 
                            : '<span class="badge badge-success">Activo</span>';

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
        // Solo ajustar automáticamente la primera vez que se cargan las tres capas
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
</script>
</body>
</html>
