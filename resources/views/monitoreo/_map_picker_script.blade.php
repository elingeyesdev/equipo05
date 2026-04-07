<script>
(function () {
    const latInput = document.getElementById('latitud');
    const lngInput = document.getElementById('longitud');
    const readout = document.getElementById('coord-readout');
    if (!latInput || !lngInput || typeof L === 'undefined') return;

    let lat = parseFloat(latInput.value);
    let lng = parseFloat(lngInput.value);
    if (Number.isNaN(lat)) lat = -34.603722;
    if (Number.isNaN(lng)) lng = -58.381592;

    const map = L.map('map-picker').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const marker = L.marker([lat, lng], { draggable: true, autoPan: true }).addTo(map);

    function sync() {
        const p = marker.getLatLng();
        latInput.value = p.lat.toFixed(7);
        lngInput.value = p.lng.toFixed(7);
        if (readout) {
            readout.innerHTML = 'Coordenadas: <strong>' + latInput.value + '</strong>, <strong>' + lngInput.value + '</strong>';
        }
    }

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        sync();
    });
    marker.on('dragend', sync);
    sync();
})();
</script>
