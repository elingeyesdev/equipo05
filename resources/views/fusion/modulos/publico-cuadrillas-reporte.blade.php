<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte Rápido de Incidente</title>
    <!-- Bootstrap 4 & AdminLTE base styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

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
            margin-bottom: 2rem;
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
        .card-report {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background-color: #ffffff;
            overflow: hidden;
            margin-bottom: 3rem;
        }
        .card-header-danger {
            background-color: #dc3545;
            color: #ffffff;
            padding: 1rem 1.5rem;
            font-size: 1.15rem;
            font-weight: 500;
            border-bottom: none;
        }
        .alert-info-custom {
            background-color: #17a2b8;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .alert-info-custom h5 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        .alert-info-custom h5 i {
            margin-right: 0.5rem;
        }
        label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }
        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 0.6rem 0.75rem;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        #map-reporte-publico {
            height: 400px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            margin-bottom: 0.5rem;
        }
        .paw-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        .paw-title i {
            color: #ffc107;
            margin-right: 0.5rem;
        }
        .card-footer-custom {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
            display: flex;
            gap: 0.75rem;
        }
        .btn-send {
            background-color: #dc3545;
            color: #ffffff;
            border: none;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }
        .btn-send:hover {
            background-color: #bd2130;
            color: #ffffff;
        }
        .btn-cancel {
            background-color: #6c757d;
            color: #ffffff;
            border: none;
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
            color: #ffffff;
            text-decoration: none;
        }
        /* Style adjustments for map marker */
        .reporte-marker {
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
                <h1 class="header-title">Reporte Rápido de Incidente</h1>
            </div>
            <div class="col-md-4 text-md-right mt-2 mt-md-0">
                <div class="breadcrumb-custom justify-content-md-end">
                    <a href="{{ route('publico.cuadrillas.mapa') }}">Mapa</a>
                    <span class="separator">/</span>
                    <span class="active-item">Reporte Rápido</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenedor del Formulario -->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> Por favor corrija los errores en el formulario.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card card-report">
                <div class="card-header-danger">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Información del Reporte
                </div>
                
                <form action="{{ route('publico.cuadrillas.reporte.store') }}" method="POST" id="form-reporte-publico">
                    @csrf
                    <div class="card-body p-4">
                        
                        <!-- Alerta Atención -->
                        <div class="alert-info-custom">
                            <h5><i class="fas fa-info-circle"></i> Atención</h5>
                            Complete el formulario para reportar un incidente. Los campos marcados con <span class="text-warning">*</span> son obligatorios.
                        </div>

                        <!-- Fila 1: Nombre y Teléfono -->
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="nombre_reportante">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre_reportante') is-invalid @enderror" 
                                       id="nombre_reportante" name="nombre_reportante" 
                                       value="{{ old('nombre_reportante') }}" required maxlength="200" 
                                       placeholder="Ej: Juan Pérez">
                                @error('nombre_reportante')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="telefono_contacto">Teléfono de Contacto</label>
                                <input type="text" class="form-control @error('telefono_contacto') is-invalid @enderror" 
                                       id="telefono_contacto" name="telefono_contacto" 
                                       value="{{ old('telefono_contacto') }}" maxlength="20" 
                                       placeholder="Ej: 77123456">
                                @error('telefono_contacto')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Fila 2: Tipo y Gravedad -->
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="tipo_incidente_id">Tipo de Incidente</label>
                                <select class="form-control @error('tipo_incidente_id') is-invalid @enderror" 
                                        id="tipo_incidente_id" name="tipo_incidente_id">
                                    <option value="">Seleccione una opción</option>
                                    @foreach ($tiposIncidente as $tipo)
                                        <option value="{{ $tipo->id_tipo_incidente }}" 
                                            {{ old('tipo_incidente_id') == $tipo->id_tipo_incidente ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_incidente_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="gravedad_id">Nivel de Gravedad</label>
                                <select class="form-control @error('gravedad_id') is-invalid @enderror" 
                                        id="gravedad_id" name="gravedad_id">
                                    <option value="">Seleccione una opción</option>
                                    @foreach ($nivelesGravedad as $nivel)
                                        <option value="{{ $nivel->id_nivel_gravedad }}" 
                                            {{ old('gravedad_id') == $nivel->id_nivel_gravedad ? 'selected' : '' }}>
                                            {{ $nivel->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gravedad_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Fila 3: Nombre del Lugar -->
                        <div class="form-group">
                            <label for="nombre_lugar">Nombre del Lugar</label>
                            <input type="text" class="form-control @error('nombre_lugar') is-invalid @enderror" 
                                   id="nombre_lugar" name="nombre_lugar" value="{{ old('nombre_lugar') }}" 
                                   maxlength="200" placeholder="Ej: Cerca del parque central">
                            @error('nombre_lugar')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fila 4: Comentarios Adicionales -->
                        <div class="form-group">
                            <label for="comentario_adicional">Comentarios Adicionales</label>
                            <textarea class="form-control @error('comentario_adicional') is-invalid @enderror" 
                                      id="comentario_adicional" name="comentario_adicional" rows="3" 
                                      placeholder="Describa con detalle lo que está sucediendo...">{{ old('comentario_adicional') }}</textarea>
                            @error('comentario_adicional')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fila 5: Ubicación con Mapa -->
                        <div class="form-group">
                            <label>Ubicación del Incidente <span class="text-danger">*</span></label>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <input type="number" class="form-control @error('latitud') is-invalid @enderror" 
                                           id="latitud" name="latitud" value="{{ old('latitud') }}" step="0.000001" 
                                           placeholder="Latitud" readonly required>
                                    @error('latitud')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <input type="number" class="form-control @error('longitud') is-invalid @enderror" 
                                           id="longitud" name="longitud" value="{{ old('longitud') }}" step="0.000001" 
                                           placeholder="Longitud" readonly required>
                                    @error('longitud')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Mapa Leaflet contenedor -->
                            <div id="map-reporte-publico"></div>
                            
                            <small class="form-text text-muted">
                                <i class="fas fa-map-marker-alt text-danger"></i> Haga clic en el mapa para marcar la ubicación del incidente
                            </small>
                        </div>

                        <!-- Fila 6: Información de Animales -->
                        <hr class="my-4">
                        <h5 class="paw-title"><i class="fas fa-paw"></i> Información de Animales</h5>
                        <div class="form-group">
                            <label class="d-block">¿Hay algún animal herido presente?</label>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input" type="radio" id="animal_si" name="animal_presente" value="si">
                                <label for="animal_si" class="custom-control-label font-weight-normal">Sí</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input class="custom-control-input" type="radio" id="animal_no" name="animal_presente" value="no" checked>
                                <label for="animal_no" class="custom-control-label font-weight-normal">No</label>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer-custom">
                        <button type="submit" class="btn btn-send">
                            <i class="fas fa-paper-plane"></i> Enviar Reporte
                        </button>
                        <a href="{{ route('publico.cuadrillas.mapa') }}" class="btn btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal Animales Heridos -->
<div class="modal fade" id="animalModal" tabindex="-1" role="dialog" aria-labelledby="animalModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title font-weight-bold" id="animalModalLabel"><i class="fas fa-paw mr-2"></i> Detalles del Animal Herido</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                
                <!-- Imagen -->
                <div class="form-group">
                    <label>Fotografía del Animal</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="imagen_animal" name="imagen_animal" accept="image/*">
                        <label class="custom-file-label" for="imagen_animal">Seleccionar fotografía del animal...</label>
                    </div>
                    <div class="mt-3 text-center" id="imagen_preview_container" style="display: none;">
                        <img id="imagen_preview" src="#" alt="Vista previa de la imagen" class="img-fluid rounded border shadow-sm" style="max-height: 200px;">
                    </div>
                </div>

                <div class="row">
                    <!-- Estado inicial -->
                    <div class="col-md-6 form-group">
                        <label for="estado_animal">Estado inicial del animal</label>
                        <select class="form-control" id="estado_animal" name="estado_animal">
                            <option value="Herido leve">Herido leve</option>
                            <option value="Herido grave">Herido grave</option>
                            <option value="Inconsciente">Inconsciente</option>
                            <option value="Deshidratado">Deshidratado</option>
                            <option value="Quemaduras" selected>Quemaduras</option>
                            <option value="Desorientado / shock">Desorientado / shock</option>
                            <option value="Atascado / atrapado">Atascado / atrapado</option>
                            <option value="Difícil acceso">Difícil acceso</option>
                            <option value="Desconocido">Desconocido</option>
                        </select>
                    </div>
                    <!-- Tipo de incidente -->
                    <div class="col-md-6 form-group">
                        <label for="tipo_incidente_animal">Tipo de incidente</label>
                        <select class="form-control" id="tipo_incidente_animal" name="tipo_incidente_animal">
                            <option value="Incendio cercano - Alto" selected>Incendio cercano - Alto</option>
                        </select>
                    </div>
                </div>

                <!-- Tamaño -->
                <div class="form-group">
                    <label class="d-block">Tamaño del animal</label>
                    <div class="custom-control custom-radio custom-control-inline mr-3">
                        <input class="custom-control-input" type="radio" id="tamano_pequeno" name="tamano_animal" value="pequeno">
                        <label for="tamano_pequeno" class="custom-control-label font-weight-normal">Pequeño</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline mr-3">
                        <input class="custom-control-input" type="radio" id="tamano_mediano" name="tamano_animal" value="mediano" checked>
                        <label for="tamano_mediano" class="custom-control-label font-weight-normal">Mediano</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input class="custom-control-input" type="radio" id="tamano_grande" name="tamano_animal" value="grande">
                        <label for="tamano_grande" class="custom-control-label font-weight-normal">Grande</label>
                    </div>
                </div>

                <!-- Puede moverse -->
                <div class="form-group mb-0">
                    <label class="d-block">¿Puede moverse?</label>
                    <div class="custom-control custom-radio custom-control-inline mr-3">
                        <input class="custom-control-input" type="radio" id="moverse_si" name="puede_moverse" value="si">
                        <label for="moverse_si" class="custom-control-label font-weight-normal">Sí</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input class="custom-control-input" type="radio" id="moverse_no" name="puede_moverse" value="no" checked>
                        <label for="moverse_no" class="custom-control-label font-weight-normal">No</label>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-warning font-weight-bold" data-dismiss="modal">
                    <i class="fas fa-check"></i> Guardar y Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(document).ready(function() {
    // Inicializar mapa Leaflet centrado en Santa Cruz, Bolivia
    const map = L.map('map-reporte-publico').setView([-17.8, -63.1], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;

    // Acción de clic en el mapa para marcar ubicación
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Actualizar coordenadas en inputs
        $('#latitud').val(lat.toFixed(6));
        $('#longitud').val(lng.toFixed(6));

        // Obtener dirección aproximada con Nominatim API (Reverse Geocoding)
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    $('#nombre_lugar').val(data.display_name);
                }
            })
            .catch(error => console.error('Error en geocoding inverso:', error));

        // Quitar marcador previo
        if (marker) {
            map.removeLayer(marker);
        }

        // Crear y añadir nuevo marcador personalizado con icono de advertencia rojo
        const redIconHtml = '<div style="background-color: #dc3545; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.4);"><i class="fas fa-exclamation-triangle" style="font-size: 13px;"></i></div>';
        
        marker = L.marker([lat, lng], {
            icon: L.divIcon({
                html: redIconHtml,
                className: 'reporte-marker',
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            })
        }).addTo(map);
    });

    // Abrir el modal si el usuario selecciona "Sí" en animal herido
    $('input[name="animal_presente"]').change(function() {
        if (this.value === 'si') {
            $('#animalModal').modal('show');
        }
    });

    // Vista previa de imagen seleccionada del animal
    $('#imagen_animal').on('change', function() {
        // Actualizar label
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || "Seleccionar fotografía del animal...");

        // Mostrar preview
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#imagen_preview').attr('src', e.target.result);
                $('#imagen_preview_container').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagen_preview_container').hide();
        }
    });

    // Interceptar envío del formulario
    $('#form-reporte-publico').on('submit', function(e) {
        const animalPresente = $('input[name="animal_presente"]:checked').val();
        
        if (animalPresente === 'si') {
            e.preventDefault(); // Detener envío por defecto
            
            const form = $(this);
            const formData = new FormData(this);
            
            const submitBtn = form.find('button[type="submit"]');
            const originalBtnText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

            // 1. Guardar Reporte de Incendio principal vía AJAX
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    if (response.success && response.id) {
                        // 2. Si tiene éxito, guardar el reporte de rescate animal
                        sendAnimalReport(response.id, submitBtn, originalBtnText);
                    } else {
                        alert('Error al guardar el reporte: ' + (response.message || 'Error desconocido'));
                        submitBtn.prop('disabled', false).html(originalBtnText);
                    }
                },
                error: function(xhr) {
                    console.error('Error al enviar reporte principal:', xhr);
                    const msg = xhr.responseJSON?.message || 'Error en el servidor al enviar el reporte.';
                    alert('Error: ' + msg);
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        }
    });

    // Enviar reporte del animal herido vinculado al incendio
    function sendAnimalReport(incendioId, submitBtn, originalText) {
        const animalFormData = new FormData();
        
        // Mapeo estático de condiciones a IDs (Microservicio de Rescate)
        const conditionMap = {
            'Herido leve': 3,
            'Herido grave': 1,
            'Inconsciente': 1,
            'Deshidratado': 3,
            'Quemaduras': 2,
            'Desorientado / shock': 3,
            'Atascado / atrapado': 3,
            'Difícil acceso': 3,
            'Desconocido': 3
        };
        
        const incidentTypeMap = {
            'Incendio cercano - Alto': 1
        };

        const estado = $('#estado_animal').val();
        const tipo = $('#tipo_incidente_animal').val();
        const tamano = $('input[name="tamano_animal"]:checked').val();
        const moverse = $('input[name="puede_moverse"]:checked').val() === 'si';
        const imagen = $('#imagen_animal')[0].files[0];
        
        const lat = $('#latitud').val();
        const lng = $('#longitud').val();
        const obs = $('#comentario_adicional').val();
        
        // Cargar campos requeridos por la API del módulo de Rescate Animal
        animalFormData.append('incendio_id', incendioId);
        animalFormData.append('latitud', lat);
        animalFormData.append('longitud', lng);
        animalFormData.append('direccion', $('#nombre_lugar').val() || 'Sin dirección específica');
        animalFormData.append('observaciones', obs || 'Reportado vía Formulario Público de Cuadrillas');
        animalFormData.append('condicion_inicial_id', conditionMap[estado] || 2);
        animalFormData.append('tipo_incidente_id', incidentTypeMap[tipo] || 1);
        animalFormData.append('tamano', tamano);
        animalFormData.append('puede_moverse', moverse);
        animalFormData.append('traslado_inmediato', 0); // Falso por defecto
        
        if (imagen) {
            animalFormData.append('imagen', imagen);
        }

        // Llamar al endpoint público unificado del módulo de rescate
        $.ajax({
            url: '/api/rescate/reports', 
            method: 'POST',
            data: animalFormData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Redirigir al mapa con mensaje de éxito
                window.location.href = '{{ route("publico.cuadrillas.mapa") }}?success=Reporte+de+incidente+y+animal+guardados+con+exito';
            },
            error: function(xhr) {
                console.error('Error al guardar reporte de animal:', xhr);
                // Aunque fallara el animal, el reporte principal se guardó con éxito.
                alert('El reporte de incendio se guardó con éxito, pero falló el reporte del animal: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                window.location.href = '{{ route("publico.cuadrillas.mapa") }}';
            }
        });
    }
});
</script>
</body>
</html>
