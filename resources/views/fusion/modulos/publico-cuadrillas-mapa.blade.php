<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mapa en Tiempo Real</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        body { background: #eef1f4; }
        .wrapper-box { max-width: 1200px; margin: 2rem auto; }
        #mapa-tiempo-real { height: 600px; border-radius: 6px; }
        .legend {
            background: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 8px rgba(0,0,0,.2);
            font-size: 12px;
            line-height: 1.4;
        }
        .legend i {
            width: 12px;
            height: 12px;
            display: inline-block;
            border-radius: 50%;
            margin-right: 6px;
        }
    </style>
</head>
<body>
<div class="wrapper-box">
    <div class="card card-danger card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Mapa en Tiempo Real - Cuadrillas</h3>
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-sign-in-alt mr-1"></i> Iniciar sesión
            </a>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-outline-primary btn-sm" onclick="cargarNasa(1)">Últimas 24h</button>
                <button class="btn btn-outline-primary btn-sm" onclick="cargarNasa(2)">Últimos 2 días</button>
                <button class="btn btn-outline-primary btn-sm" onclick="cargarNasa(7)">Últimos 7 días</button>
                <span class="ml-3 text-muted">Focos encontrados: <strong id="contador-focos">0</strong></span>
            </div>
            <div id="mapa-tiempo-real"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
    const mapa = L.map('mapa-tiempo-real').setView([-17.8, -63.1], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapa);

    const capaNasa = L.markerClusterGroup();
    mapa.addLayer(capaNasa);

    const API_KEY = '1ae0346a287432156ada4abb791d57cd';
    const API_BASE = 'https://firms.modaps.eosdis.nasa.gov/api/area/csv';
    const BOLIVIA = { minLat: -22.9, maxLat: -9.7, minLng: -69.6, maxLng: -57.5 };

    const legend = L.control({ position: 'bottomright' });
    legend.onAdd = function () {
        const div = L.DomUtil.create('div', 'legend');
        div.innerHTML = `
            <div><i style="background:#ff0000"></i>Alta confianza</div>
            <div><i style="background:#ffa500"></i>Media confianza</div>
            <div><i style="background:#00ced1"></i>Baja confianza</div>
        `;
        return div;
    };
    legend.addTo(mapa);

    function iconoPorConfianza(conf) {
        let color = '#00ced1';
        let size = 8;
        if (conf === 'h' || parseFloat(conf) >= 80) { color = '#ff0000'; size = 12; }
        else if (conf === 'n' || (parseFloat(conf) >= 50 && parseFloat(conf) < 80)) { color = '#ffa500'; size = 10; }
        return L.divIcon({
            html: `<div style="background:${color};width:${size}px;height:${size}px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 6px rgba(0,0,0,.5)"></div>`,
            className: '',
            iconSize: [size, size]
        });
    }

    function dentroDeBolivia(lat, lng) {
        return lat >= BOLIVIA.minLat && lat <= BOLIVIA.maxLat && lng >= BOLIVIA.minLng && lng <= BOLIVIA.maxLng;
    }

    async function cargarNasa(days = 2) {
        capaNasa.clearLayers();
        document.getElementById('contador-focos').textContent = '0';
        const url = `${API_BASE}/${API_KEY}/VIIRS_NOAA21_NRT/world/${days}`;
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('No se pudo consultar NASA FIRMS');
            const csv = await response.text();
            const lines = csv.trim().split('\n');
            if (lines.length < 2) return;

            const headers = lines[0].split(',');
            let total = 0;
            for (let i = 1; i < lines.length; i++) {
                const cols = lines[i].split(',');
                const row = {};
                headers.forEach((h, idx) => row[h.trim()] = (cols[idx] || '').trim());
                const lat = parseFloat(row.latitude);
                const lng = parseFloat(row.longitude);
                if (!dentroDeBolivia(lat, lng)) continue;
                total++;
                const marker = L.marker([lat, lng], { icon: iconoPorConfianza(row.confidence) });
                marker.bindPopup(`
                    <strong>NASA FIRMS</strong><br>
                    Fecha: ${row.acq_date || 'N/A'}<br>
                    Hora: ${row.acq_time || 'N/A'}<br>
                    FRP: ${row.frp || 'N/A'} MW
                `);
                capaNasa.addLayer(marker);
            }
            document.getElementById('contador-focos').textContent = total.toString();
            if (total > 0) mapa.fitBounds(capaNasa.getBounds().pad(0.1));
        } catch (e) {
            console.error(e);
            alert('No se pudo cargar el mapa en tiempo real en este momento.');
        }
    }

    cargarNasa(2);
</script>
</body>
</html>
