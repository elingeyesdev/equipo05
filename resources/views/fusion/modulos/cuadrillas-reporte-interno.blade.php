@extends('layouts.app')

@section('content_header_title', 'Reporte rápido')
@section('content_header_subtitle', 'Registro de incidentes en campo')

@section('content')
@include('fusion.modulos.partials.cuadrillas-module-nav')
@include('fusion.modulos.partials.cuadrillas-flash')

<div class="row justify-content-center">
    <div class="col-lg-11 col-xl-10">

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i> Por favor corrija los errores en el formulario.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif

            <div class="card cua-list-card cua-accent-danger shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-exclamation-triangle mr-1 text-danger"></i> Formulario de reporte</h3>
                </div>
                
                <form action="{{ route('publico.cuadrillas.reporte.store') }}" method="POST" id="form-reporte-publico">
                    @csrf
                    <div class="card-body p-4">
                        
                        <!-- Alerta Atención -->
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <h5><i class="icon fas fa-info"></i> Atención</h5>
                            Complete el formulario para reportar un incidente. Los campos marcados con <span class="text-danger">*</span> son obligatorios.
                        </div>

                        <!-- Fila 1: Nombre y Teléfono -->
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="nombre_reportante">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre_reportante') is-invalid @enderror" 
                                       id="nombre_reportante" name="nombre_reportante" 
                                       value="{{ old('nombre_reportante', auth()->user()->nombre . ' ' . auth()->user()->apellido) }}" required maxlength="200" 
                                       placeholder="Ej: Juan Pérez">
                                @error('nombre_reportante')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="telefono_contacto">Teléfono de Contacto</label>
                                <input type="text" class="form-control @error('telefono_contacto') is-invalid @enderror" 
                                       id="telefono_contacto" name="telefono_contacto" 
                                       value="{{ old('telefono_contacto', auth()->user()->telefono) }}" maxlength="20" 
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
                            <div id="map-reporte-publico" style="height: 400px; border-radius: 4px; border: 1px solid #ced4da;"></div>
                            
                            <small class="form-text text-muted mt-2">
                                <i class="fas fa-map-marker-alt text-danger"></i> Haga clic en el mapa para marcar la ubicación del incidente
                            </small>
                        </div>

                        <!-- Fila 6: Información de Animales -->
                        <hr class="my-4">
                        <h5 class="text-dark font-weight-bold"><i class="fas fa-paw text-warning mr-2"></i> Información de Animales</h5>
                        <div class="form-group">
                            <label class="d-block font-weight-normal">¿Hay algún animal herido presente?</label>
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

                    <div class="card-footer bg-light d-flex justify-content-start gap-2">
                        <button type="submit" class="btn btn-danger px-4 mr-2">
                            <i class="fas fa-paper-plane mr-1"></i> Enviar Reporte
                        </button>
                        <a href="{{ route('cuadrillas.estadisticas') }}" class="btn btn-secondary px-4">
                            <i class="fas fa-times mr-1"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <div class="card cua-list-card cua-accent-danger shadow-sm mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-list mr-1 text-danger"></i> Reportes recientes en base de datos
                    </h3>
                    <a href="{{ route('cuadrillas.focos-calor') }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-map-marked-alt mr-1"></i> Ver en mapa
                    </a>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Reportante</th>
                                <th>Lugar</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($reportesRecientes ?? collect()) as $reporte)
                                <tr>
                                    <td>#{{ $reporte->id_reporte }}</td>
                                    <td>{{ $reporte->fecha_hora ? \Carbon\Carbon::parse($reporte->fecha_hora)->format('d/m/Y H:i') : '—' }}</td>
                                    <td>{{ $reporte->nombre_reportante ?? 'Anónimo' }}</td>
                                    <td>{{ $reporte->nombre_lugar ?? '—' }}</td>
                                    <td>{{ $reporte->tipo_incidente ?? '—' }}</td>
                                    <td>{{ $reporte->estado ?? 'Pendiente' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No hay reportes registrados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted small">
                    Incluye reportes enviados desde la app móvil y el formulario web público.
                </div>
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
                    <label class="font-weight-bold">Fotografía del Animal</label>
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
                        <label for="estado_animal" class="font-weight-bold">Estado inicial del animal</label>
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
                        <label for="tipo_incidente_animal" class="font-weight-bold">Tipo de incidente</label>
                        <select class="form-control" id="tipo_incidente_animal" name="tipo_incidente_animal">
                            <option value="1" selected>Incendio</option>
                            <option value="2">Atropello</option>
                            <option value="3">Cacería / arma de fuego</option>
                            <option value="4">Encontrado atrapado</option>
                            <option value="5">Animal desorientado</option>
                            <option value="6">Evento natural (inundación/tormenta)</option>
                            <option value="7">Otro</option>
                        </select>
                    </div>
                </div>

                <!-- Tamaño -->
                <div class="form-group">
                    <label class="d-block font-weight-bold">Tamaño del animal</label>
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
                    <label class="d-block font-weight-bold">¿Puede moverse?</label>
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
                <button type="button" class="btn btn-warning font-weight-bold text-dark" data-dismiss="modal">
                    <i class="fas fa-check"></i> Guardar y Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    .reporte-marker {
        background: none;
        border: none;
    }
</style>
@endpush

@push('js')
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
        animalFormData.append('observaciones', obs || 'Reportado vía Formulario Interno de Cuadrillas');
        animalFormData.append('condicion_inicial_id', conditionMap[estado] || 2);
        animalFormData.append('tipo_incidente_id', tipo);
        animalFormData.append('tamano', tamano);
        animalFormData.append('puede_moverse', moverse ? 1 : 0);
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
                window.location.href = '{{ route("cuadrillas.focos-calor") }}?success=Reporte+de+incidente+y+animal+guardados+con+exito';
            },
            error: function(xhr) {
                console.error('Error al guardar reporte de animal:', xhr);
                alert('El reporte de incendio se guardó con éxito, pero falló el reporte del animal: ' + (xhr.responseJSON?.message || 'Error desconocido'));
                window.location.href = '{{ route("cuadrillas.focos-calor") }}';
            }
        });
    }
});
</script>
@endpush
