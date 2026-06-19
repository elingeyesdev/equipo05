@php
    $mapId = $mapId ?? 'mapa-tiempo-real';
    $equiposUrl = $equiposUrl ?? route('publico.cuadrillas.equipos-api');
    $reportesUrl = $reportesUrl ?? route('publico.cuadrillas.reportes-api');
    $incendiosApi = rtrim(url('api/incendios'), '/');
    $chiquitaniaArea = '-62.5,-18.5,-57.5,-14.5';
@endphp

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
@endpush

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const CONFIG = {
        mapId: @json($mapId),
        equiposUrl: @json($equiposUrl),
        reportesUrl: @json($reportesUrl),
        incendiosApi: @json($incendiosApi),
        chiquitaniaArea: @json($chiquitaniaArea),
        refreshMs: 5 * 60 * 1000,
    };

    const el = document.getElementById(CONFIG.mapId);
    if (!el || typeof L === 'undefined') return;

    const mapa = L.map(CONFIG.mapId).setView([-17.8857, -60.7556], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(mapa);

    const capaFirms = L.markerClusterGroup({
        iconCreateFunction: function (cluster) {
            return L.divIcon({
                html: `<div style="background:#dc3545;color:#fff;border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-weight:700;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.25);">${cluster.getChildCount()}</div>`,
                className: 'cua-firms-cluster',
                iconSize: [34, 34],
            });
        },
    });
    const capaRegistrados = L.layerGroup();
    const capaEquipos = L.layerGroup();
    const capaReportes = L.layerGroup();

    mapa.addLayer(capaFirms);
    mapa.addLayer(capaRegistrados);
    mapa.addLayer(capaEquipos);
    mapa.addLayer(capaReportes);

    let diasActuales = 2;
    let ajustado = false;

    function iconoFirms(fire) {
        const conf = (fire.confidence || 'n').toString().toLowerCase();
        let color = '#f59e0b';
        let size = 10;
        if (conf === 'h' || parseFloat(conf) >= 80) { color = '#dc2626'; size = 12; }
        else if (conf === 'l' || parseFloat(conf) < 50) { color = '#06b6d4'; size = 8; }
        const isCluster = fire.is_cluster && (fire.cluster_size || 0) > 1;
        if (isCluster) size = 14;
        return L.divIcon({
            html: `<div style="background:${color};width:${size}px;height:${size}px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 4px rgba(0,0,0,.35);"></div>`,
            className: '',
            iconSize: [size, size],
            iconAnchor: [size / 2, size / 2],
        });
    }

    function popupFirms(fire) {
        const size = fire.cluster_size || 1;
        const isCluster = fire.is_cluster && size > 1;
        return `<div style="font-size:12px;line-height:1.45;">
            <strong style="color:#dc2626;"><i class="fas fa-satellite"></i> NASA FIRMS</strong>
            ${isCluster ? `<br><em>${size} detecciones agrupadas</em>` : ''}
            <hr style="margin:4px 0;">
            <b>Fecha:</b> ${fire.date || fire.acq_date || 'N/D'}<br>
            <b>Confianza:</b> ${fire.confidence || 'N/D'}<br>
            <b>FRP:</b> ${fire.frp || 'N/D'} MW<br>
            <b>Coords:</b> ${Number(fire.lat).toFixed(5)}, ${Number(fire.lng).toFixed(5)}
            <div class="text-muted mt-1" style="font-size:10px;">Misma fuente que módulo Incendios</div>
        </div>`;
    }

    function popupRegistrado(f) {
        return `<div style="font-size:12px;line-height:1.45;">
            <strong style="color:#2563eb;"><i class="fas fa-fire"></i> Foco registrado</strong>
            <hr style="margin:4px 0;">
            <b>Ubicación:</b> ${f.ubicacion || 'Sin descripción'}<br>
            <b>Intensidad:</b> ${f.intensidad ?? 'N/D'}<br>
            <b>Fecha:</b> ${f.fecha_humana || f.fecha || 'N/D'}
        </div>`;
    }

    function actualizarHora() {
        const lbl = document.getElementById('lbl-update-time');
        if (lbl) {
            const now = new Date();
            lbl.textContent = now.toLocaleString('es-BO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    }

    function ajustarVista() {
        if (ajustado) return;
        const markers = [];
        [capaFirms, capaRegistrados, capaEquipos, capaReportes].forEach(capa => capa.eachLayer(m => markers.push(m)));
        if (markers.length > 0) {
            mapa.fitBounds(L.featureGroup(markers).getBounds().pad(0.12));
            ajustado = true;
        }
    }

    async function cargarFirms(dias) {
        diasActuales = dias;
        document.querySelectorAll('[data-cua-days]').forEach(btn => {
            btn.classList.toggle('active', parseInt(btn.dataset.cuaDays, 10) === dias);
        });

        capaFirms.clearLayers();
        const lbl = document.getElementById('lbl-nasa-count');
        if (lbl) lbl.textContent = '…';

        try {
            const url = `${CONFIG.incendiosApi}/fires?cluster=true&radius=20&days=${dias}&area=${encodeURIComponent(CONFIG.chiquitaniaArea)}`;
            const res = await fetch(url);
            const json = await res.json();
            const fires = json.data || [];
            fires.forEach(fire => {
                if (fire.lat == null || fire.lng == null) return;
                const m = L.marker([fire.lat, fire.lng], { icon: iconoFirms(fire) });
                m.bindPopup(popupFirms(fire));
                capaFirms.addLayer(m);
            });
            if (lbl) lbl.textContent = String(fires.length);
        } catch (e) {
            console.error('FIRMS:', e);
            if (lbl) lbl.textContent = '—';
        }
        actualizarHora();
        ajustarVista();
    }

    async function cargarRegistrados(dias) {
        capaRegistrados.clearLayers();
        const hours = Math.min(168, Math.max(24, dias * 24));
        try {
            const res = await fetch(`${CONFIG.incendiosApi}/public/firms/active?hours=${hours}`);
            const json = await res.json();
            (json.data || []).forEach(f => {
                if (f.lat == null || f.lng == null) return;
                const m = L.marker([f.lat, f.lng], {
                    icon: L.divIcon({
                        html: '<div style="background:#2563eb;width:14px;height:14px;border:2px solid #fff;border-radius:50%;box-shadow:0 2px 6px rgba(37,99,235,.5);"></div>',
                        className: '',
                        iconSize: [14, 14],
                        iconAnchor: [7, 7],
                    }),
                });
                m.bindPopup(popupRegistrado(f));
                capaRegistrados.addLayer(m);
            });
            const lblReg = document.getElementById('lbl-registrados-count');
            if (lblReg) lblReg.textContent = String((json.data || []).length);
        } catch (e) {
            console.error('Focos registrados:', e);
        }
        ajustarVista();
    }

    function cargarEquipos() {
        capaEquipos.clearLayers();
        fetch(CONFIG.equiposUrl)
            .then(r => r.json())
            .then(data => {
                const list = Array.isArray(data) ? data : [];
                const lbl = document.getElementById('lbl-equipos-count');
                if (lbl) lbl.textContent = String(list.length);
                list.forEach(eq => {
                    if (!eq.ubicacion?.coordinates) return;
                    const [lng, lat] = eq.ubicacion.coordinates;
                    const m = L.marker([lat, lng], {
                        icon: L.divIcon({
                            html: '<div style="background:#007bff;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid #fff;box-shadow:0 2px 5px rgba(0,0,0,.25);"><i class="fas fa-users" style="font-size:12px;"></i></div>',
                            className: '',
                            iconSize: [32, 32],
                            iconAnchor: [16, 16],
                        }),
                    });
                    const estado = eq.estado?.nombre || 'Activo';
                    m.bindPopup(`<strong>${eq.nombre_equipo}</strong><br>Integrantes: ${eq.cantidad_integrantes || 0}<br>Estado: ${estado}`);
                    capaEquipos.addLayer(m);
                });
                ajustarVista();
            })
            .catch(() => {
                const lbl = document.getElementById('lbl-equipos-count');
                if (lbl) lbl.textContent = '—';
            });
    }

    function cargarReportes() {
        capaReportes.clearLayers();
        fetch(CONFIG.reportesUrl)
            .then(r => r.json())
            .then(data => {
                const list = Array.isArray(data) ? data : [];
                const lbl = document.getElementById('lbl-reportes-count');
                if (lbl) lbl.textContent = String(list.length);
                if (list.length > 0) {
                    const fechas = list.map(r => r.fecha_hora).filter(Boolean).sort();
                    const ultima = fechas.length ? new Date(fechas[fechas.length - 1]) : null;
                    const lblUlt = document.getElementById('lbl-ultimo-reporte');
                    if (lblUlt && ultima) {
                        lblUlt.textContent = ultima.toLocaleDateString('es-BO');
                    }
                }
                list.forEach(rep => {
                    if (!rep.ubicacion?.coordinates) return;
                    const [lng, lat] = rep.ubicacion.coordinates;
                    const m = L.marker([lat, lng], {
                        icon: L.divIcon({
                            html: '<div style="background:#ff9800;color:#fff;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid #fff;"><i class="fas fa-bullhorn" style="font-size:10px;"></i></div>',
                            className: '',
                            iconSize: [28, 28],
                            iconAnchor: [14, 14],
                        }),
                    });
                    const tipo = rep.tipos_incidente?.nombre || 'Incidente';
                    m.bindPopup(`<strong>${rep.nombre_lugar || 'Reporte'}</strong><br>${tipo}<br>${rep.nombre_reportante || 'Anónimo'}`);
                    capaReportes.addLayer(m);
                });
                ajustarVista();
            })
            .catch(() => {
                const lbl = document.getElementById('lbl-reportes-count');
                if (lbl) lbl.textContent = '—';
            });
    }

    window.cuaCargarMapaDias = function (dias) {
        cargarFirms(dias);
        cargarRegistrados(dias);
    };

    document.querySelectorAll('[data-cua-days]').forEach(btn => {
        btn.addEventListener('click', function () {
            cuaCargarMapaDias(parseInt(this.dataset.cuaDays, 10));
        });
    });

    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function () {
        const div = L.DomUtil.create('div', 'cua-map-legend');
        div.innerHTML = `
            <h6>Leyenda</h6>
            <div class="legend-item"><i class="dot" style="background:#dc2626"></i> NASA FIRMS</div>
            <div class="legend-item"><i class="dot" style="background:#2563eb"></i> Foco registrado</div>
            <div class="legend-item"><i class="dot" style="background:#007bff"></i> Equipo cuadrilla</div>
            <div class="legend-item"><i class="dot" style="background:#ff9800"></i> Reporte ciudadano</div>`;
        return div;
    };
    legend.addTo(mapa);

    cargarFirms(2);
    cargarRegistrados(2);
    cargarEquipos();
    cargarReportes();

    setTimeout(() => mapa.invalidateSize(), 300);
    setInterval(() => {
        cargarFirms(diasActuales);
        cargarRegistrados(diasActuales);
        cargarEquipos();
        cargarReportes();
    }, CONFIG.refreshMs);
});
</script>
@endpush
