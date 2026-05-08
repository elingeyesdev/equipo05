<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Registro rápido de emergencias con fauna en riesgo. Indique ubicación y detalles para coordinar el rescate.">
    <title>Registro rápido — Rescate de fauna</title>
    <link rel="icon" type="image/png" href="{{ asset('Fotos/Patota.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('Fotos/Patota.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        .qr-map-wrap { height: 380px; border-radius: 10px; border: 1px solid #dee2e6; overflow: hidden; }
        .qr-hero { background: linear-gradient(135deg, #dc3545, #c82333); color: #fff; padding: 18px 20px; border-radius: 10px; margin-bottom: 18px; box-shadow: 0 10px 30px rgba(220, 53, 69, 0.22); }
        .ci-field { display: none; }
        .content-wrapper { margin-left: 0 !important; }
        .main-header { margin-left: 0 !important; }
        .qr-back-link { display: inline-flex; align-items: center; gap: .35rem; margin-bottom: 12px; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <div class="content-wrapper">
        <div class="content-header pb-0">
            <div class="container-fluid">
                <div class="row mb-2 align-items-center">
                    <div class="col-sm-12">
                        <a href="{{ route('rescate.landing') }}" class="qr-back-link text-secondary"><i class="fas fa-arrow-left"></i> Volver al inicio</a>
                        <h1 class="m-0 text-center">Registro rápido de emergencia</h1>
                    </div>
                </div>
            </div>
        </div>
        <section class="content pb-4">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="qr-hero text-center shadow-sm">
                            <h3 class="mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Reporte de animales en riesgo</h3>
                            <p class="mb-0 small">Marque la ubicación en el mapa y envíe una foto; los datos de contacto son opcionales.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <strong>No se pudo enviar.</strong>
                                <ul class="mb-0 mt-1 pl-3">
                                    @foreach ($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="card card-outline card-danger shadow-sm h-100">
                                    <div class="card-header"><h3 class="card-title mb-0">Ubicación del reporte</h3></div>
                                    <div class="card-body p-0 d-flex flex-column">
                                        <div id="map" class="qr-map-wrap"></div>
                                        <div class="p-3 border-top bg-light">
                                            <div class="alert alert-info mb-0 py-2 small" role="alert">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                Pulse el mapa o arrastre el marcador. Puede usar «Usar mi ubicación» si el navegador lo permite.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-outline card-warning shadow-sm h-100">
                                    <div class="card-header"><h3 class="card-title mb-0">Información del reporte</h3></div>
                                    <div class="card-body">
                                        <form id="reporteForm" action="{{ route('rescate.reporte-rapido.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                                            @csrf
                                            <div class="form-group">
                                                <label class="d-block">Tipo de emergencia</label>
                                                <div class="icheck-danger">
                                                    <input type="radio" id="incendio" name="tipo_emergencia" value="incendio" {{ old('tipo_emergencia', 'incendio') === 'incendio' ? 'checked' : '' }}>
                                                    <label for="incendio">Animales en incendio</label>
                                                </div>
                                                <div class="icheck-warning">
                                                    <input type="radio" id="otro" name="tipo_emergencia" value="otro" {{ old('tipo_emergencia') === 'otro' ? 'checked' : '' }}>
                                                    <label for="otro">Otra emergencia</label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="descripcion">Descripción</label>
                                                <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4" placeholder="Situación y tipo de animales (obligatorio si eligió «Otra»)">{{ old('descripcion') }}</textarea>
                                                @error('descripcion')<span class="invalid-feedback d-block" role="alert">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="nombre">Nombre</label>
                                                    <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre') }}" placeholder="Opcional" autocomplete="name">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="telefono">Teléfono</label>
                                                    <input type="tel" id="telefono" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="Opcional" autocomplete="tel">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="imagen">Fotografía <span class="text-danger">*</span></label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input @error('imagen') is-invalid @enderror" id="imagen" name="imagen" accept="image/jpeg,image/jpg,image/png" required>
                                                    <label class="custom-file-label" for="imagen">Seleccionar archivo JPG o PNG</label>
                                                </div>
                                                @error('imagen')<span class="invalid-feedback d-block" role="alert">{{ $message }}</span>@enderror
                                            </div>
                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="latitud">Latitud</label>
                                                    <input type="text" id="latitud" name="latitud" class="form-control bg-light" value="{{ old('latitud') }}" placeholder="Pulse el mapa" readonly required inputmode="decimal">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label for="longitud">Longitud</label>
                                                    <input type="text" id="longitud" name="longitud" class="form-control bg-light" value="{{ old('longitud') }}" placeholder="Pulse el mapa" readonly required inputmode="decimal">
                                                </div>
                                            </div>
                                            <div class="d-flex flex-wrap justify-content-between gap-2 mt-2">
                                                <button type="button" id="btnGeo" class="btn btn-outline-info"><i class="fas fa-location-arrow"></i> Usar mi ubicación</button>
                                                <button type="submit" class="btn btn-warning"><i class="fas fa-paper-plane"></i> Enviar reporte</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    var center = [-17.7833, -63.1821];
    var map = L.map('map', { zoomControl: true }).setView(center, 7);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var marker = L.marker(center, { draggable: true }).addTo(map);

    function setCoords(lat, lng) {
        var latEl = document.getElementById('latitud');
        var lngEl = document.getElementById('longitud');
        if (!latEl || !lngEl) return;
        latEl.value = Number(lat).toFixed(6);
        lngEl.value = Number(lng).toFixed(6);
    }

    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        map.panTo(e.latlng);
        setCoords(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', function () {
        var ll = marker.getLatLng();
        setCoords(ll.lat, ll.lng);
    });

    var lat0 = parseFloat(document.getElementById('latitud').value);
    var lng0 = parseFloat(document.getElementById('longitud').value);
    if (!isNaN(lat0) && !isNaN(lng0)) {
        marker.setLatLng([lat0, lng0]);
        map.setView([lat0, lng0], 14);
        setCoords(lat0, lng0);
    } else {
        setCoords(center[0], center[1]);
    }

    document.getElementById('btnGeo').addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Su navegador no permite geolocalización.');
            return;
        }
        navigator.geolocation.getCurrentPosition(function (pos) {
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 15);
            setCoords(lat, lng);
        }, function () {
            alert('No se pudo obtener la ubicación. Permita el acceso o marque el mapa manualmente.');
        }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 });
    });

    $('#imagen').on('change', function () {
        var fileName = this.files && this.files[0] ? this.files[0].name : 'Seleccionar archivo JPG o PNG';
        $(this).next('.custom-file-label').html(fileName);
    });

    document.getElementById('reporteForm').addEventListener('submit', function (e) {
        var lat = document.getElementById('latitud').value;
        var lng = document.getElementById('longitud').value;
        if (!lat || !lng) {
            e.preventDefault();
            alert('Marque la ubicación en el mapa antes de enviar.');
        }
    });

    setTimeout(function () { map.invalidateSize(); }, 300);
})();
</script>
</body>
</html>
