{{-- Utilidades Leaflet + OSRM para rutas logísticas --}}
<script>
window.LogisticaRouting = window.LogisticaRouting || (function () {
    const OSRM_URL = 'https://router.project-osrm.org/route/v1/driving';

    function iconoHtml(tipo) {
        const estilos = {
            origen: { bg: '#059669', icon: 'fa-warehouse' },
            paso: { bg: '#4f46e5', icon: 'fa-map-pin' },
            destino: { bg: '#dc3545', icon: 'fa-flag-checkered' },
        };
        const s = estilos[tipo] || estilos.paso;
        return L.divIcon({
            className: 'bg-transparent logistica-route-marker',
            html: `<div style="background:${s.bg};width:${tipo === 'destino' ? 30 : 26}px;height:${tipo === 'destino' ? 30 : 26}px;border-radius:50%;border:2.5px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 8px rgba(15,23,42,.35);"><i class="fas ${s.icon}" style="color:#fff;font-size:${tipo === 'destino' ? 13 : 11}px;"></i></div>`,
            iconSize: [tipo === 'destino' ? 30 : 26, tipo === 'destino' ? 30 : 26],
            iconAnchor: [tipo === 'destino' ? 15 : 13, tipo === 'destino' ? 15 : 13],
        });
    }

    function coordsOsrm(waypoints) {
        return waypoints.map(w => `${w.lng},${w.lat}`).join(';');
    }

    function latLngsDesdeGeojson(geometry) {
        if (!geometry || geometry.type !== 'LineString') return [];
        return geometry.coordinates.map(c => [c[1], c[0]]);
    }

    function dibujarPolylineProfesional(map, latLngs, opciones) {
        if (!latLngs || latLngs.length < 2) return null;

        const color = (opciones && opciones.color) || '#4f46e5';
        const capas = [];

        capas.push(L.polyline(latLngs, {
            color: '#0f172a',
            weight: 9,
            opacity: 0.18,
            lineCap: 'round',
            lineJoin: 'round',
        }).addTo(map));

        capas.push(L.polyline(latLngs, {
            color: color,
            weight: 5.5,
            opacity: 0.92,
            lineCap: 'round',
            lineJoin: 'round',
        }).addTo(map));

        capas.push(L.polyline(latLngs, {
            color: '#ffffff',
            weight: 1.5,
            opacity: 0.55,
            dashArray: '6, 14',
            lineCap: 'round',
            lineJoin: 'round',
        }).addTo(map));

        return capas;
    }

    async function obtenerRutaOsrm(waypoints) {
        if (waypoints.length < 2) return null;

        const url = `${OSRM_URL}/${coordsOsrm(waypoints)}?overview=full&geometries=geojson&steps=false`;
        const resp = await fetch(url);
        if (!resp.ok) throw new Error('OSRM HTTP ' + resp.status);

        const data = await resp.json();
        if (data.code !== 'Ok' || !data.routes || !data.routes[0]) {
            throw new Error('OSRM sin ruta');
        }

        return latLngsDesdeGeojson(data.routes[0].geometry);
    }

    function colocarMarcadores(map, waypoints) {
        const markers = [];
        waypoints.forEach((wp, i) => {
            const tipo = wp.tipo || (i === 0 ? 'origen' : (i === waypoints.length - 1 ? 'destino' : 'paso'));
            const marker = L.marker([wp.lat, wp.lng], { icon: iconoHtml(tipo), zIndexOffset: tipo === 'destino' ? 900 : 500 })
                .addTo(map);

            let popup = `<strong>${wp.zona || 'Punto ' + (i + 1)}</strong>`;
            if (wp.fecha) popup += `<br><small>${wp.fecha}</small>`;
            marker.bindPopup(popup);

            if (tipo === 'destino') {
                marker.bindTooltip('Destino final', { permanent: true, direction: 'top', offset: [0, -14], opacity: 0.95 });
            } else if (tipo === 'origen') {
                marker.bindTooltip('Almacén central', { permanent: true, direction: 'top', offset: [0, -14], opacity: 0.95 });
            }

            markers.push(marker);
        });
        return markers;
    }

    function animarCamion(map, latLngs) {
        if (!latLngs || latLngs.length < 2) return null;

        const truckIcon = L.divIcon({
            className: 'bg-transparent',
            html: '<div style="background:#4f46e5;width:34px;height:34px;border-radius:50%;border:2.5px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(79,70,229,.45);"><i class="fas fa-truck" style="color:#fff;font-size:15px;"></i></div>',
            iconSize: [34, 34],
            iconAnchor: [17, 17],
        });

        const puntos = latLngs.map(ll => L.latLng(Array.isArray(ll) ? ll[0] : ll.lat, Array.isArray(ll) ? ll[1] : ll.lng));
        const truck = L.marker(puntos[0], { icon: truckIcon, zIndexOffset: 2000 }).addTo(map);
        let idx = 0;
        const timer = setInterval(function () {
            idx = (idx + 1) % puntos.length;
            truck.setLatLng(puntos[idx]);
        }, 2200);

        return { marker: truck, timer: timer };
    }

    async function dibujarRuta(map, waypoints, opciones) {
        opciones = opciones || {};
        if (!waypoints || waypoints.length === 0) return { layers: [], bounds: null };

        colocarMarcadores(map, waypoints);

        let latLngs = waypoints.map(w => [w.lat, w.lng]);
        let routeLayers = null;

        if (waypoints.length >= 2) {
            try {
                const osrmCoords = await obtenerRutaOsrm(waypoints);
                if (osrmCoords && osrmCoords.length >= 2) {
                    latLngs = osrmCoords;
                }
            } catch (e) {
                console.warn('Ruta OSRM no disponible, usando línea directa.', e);
            }

            routeLayers = dibujarPolylineProfesional(map, latLngs, opciones);

            if (opciones.animarCamion !== false) {
                animarCamion(map, latLngs);
            }
        }

        const bounds = L.latLngBounds(latLngs);
        if (bounds.isValid()) {
            map.fitBounds(bounds.pad(0.14));
        }

        return { layers: routeLayers || [], bounds: bounds };
    }

    return {
        dibujarRuta: dibujarRuta,
        dibujarPolylineProfesional: dibujarPolylineProfesional,
        obtenerRutaOsrm: obtenerRutaOsrm,
        iconoHtml: iconoHtml,
    };
})();
</script>
