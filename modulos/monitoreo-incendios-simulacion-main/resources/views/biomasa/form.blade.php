@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .biomasa-map {
        height: 500px;
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        margin-bottom: 15px;
    }
    
    .map-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .map-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: bold;
        display: block;
    }
    
    .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .map-controls {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }
    
    .polygon-point {
        background: #4CAF50;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        display: inline-block;
        margin: 5px;
        font-size: 0.85rem;
    }
    
    .instruction-box {
        background: #e3f2fd;
        border-left: 4px solid #2196F3;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 15px;
    }
    
    .instruction-box i {
        color: #2196F3;
        margin-right: 8px;
    }
</style>
@endsection

<div class="row padding-1 p-1">
    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h5><i class="fas fa-exclamation-triangle"></i> Errores de validación:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    
    {{-- Información Básica --}}
    <div class="col-md-12">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-leaf"></i> Información de la Biomasa</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="fecha_reporte"><i class="fas fa-calendar"></i> Fecha de Reporte</label>
                            <input type="date" 
                                   name="fecha_reporte" 
                                   id="fecha_reporte" 
                                   class="form-control @error('fecha_reporte') is-invalid @enderror" 
                                   value="{{ old('fecha_reporte', $biomasa->fecha_reporte ?? date('Y-m-d')) }}" 
                                   required>
                            @error('fecha_reporte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="tipo_biomasa_id"><i class="fas fa-tree"></i> Tipo de Biomasa</label>
                            <select name="tipo_biomasa_id" 
                                    id="tipo_biomasa_id" 
                                    class="form-control @error('tipo_biomasa_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Seleccione un tipo --</option>
                                @foreach($tipoBiomasas as $tipo)
                                    <option value="{{ $tipo->id }}" 
                                            {{ old('tipo_biomasa_id', $biomasa->tipo_biomasa_id) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->tipo_biomasa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_biomasa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label for="densidad"><i class="fas fa-chart-bar"></i> Densidad de Vegetación</label>
                            <select name="densidad" id="densidad" class="form-control @error('densidad') is-invalid @enderror" required>
                                <option value="baja" {{ old('densidad', $biomasa->densidad) == 'baja' ? 'selected' : '' }}>Baja (0-30%)</option>
                                <option value="media" {{ old('densidad', $biomasa->densidad) == 'media' ? 'selected' : '' }} selected>Media (30-70%)</option>
                                <option value="alta" {{ old('densidad', $biomasa->densidad) == 'alta' ? 'selected' : '' }}>Alta (70-100%)</option>
                            </select>
                            @error('densidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="descripcion"><i class="fas fa-comment-alt"></i> Observaciones (Opcional)</label>
                            <textarea name="descripcion" 
                                      id="descripcion" 
                                      rows="2" 
                                      class="form-control @error('descripcion') is-invalid @enderror"
                                      placeholder="Describe características relevantes de la biomasa observada...">{{ old('descripcion', $biomasa->descripcion) }}</textarea>
                            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delimitación del Área en Mapa --}}
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-map-marked-alt"></i> Delimitación del Área de Biomasa</h3>
            </div>
            <div class="card-body">
                <div class="instruction-box">
                    <i class="fas fa-info-circle"></i>
                    <strong>Instrucciones:</strong> Haz clic en el mapa para marcar los límites del área de biomasa. 
                    Mínimo 3 puntos para formar el polígono. El área se calculará automáticamente.
                </div>

                <div class="map-controls">
                    <button type="button" class="btn btn-danger btn-sm" onclick="resetPolygon()" id="resetBtn" style="display:none;">
                        <i class="fas fa-undo"></i> Reiniciar Polígono
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="completePolygon()" id="completeBtn" style="display:none;">
                        <i class="fas fa-check"></i> Completar Polígono
                    </button>
                </div>

                <div id="biomasaMap" class="biomasa-map"></div>

                <div class="map-info-card">
                    <div class="map-stats">
                        <div class="stat-item">
                            <span class="stat-value" id="pointCount">0</span>
                            <span class="stat-label">Puntos Marcados</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value" id="areaDisplay">0</span>
                            <span class="stat-label">Área (km²)</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value" id="perimeterDisplay">0</span>
                            <span class="stat-label">Perímetro (km)</span>
                        </div>
                    </div>
                    <div id="pointsList" style="margin-top: 15px;"></div>
                </div>

                {{-- Campos ocultos para guardar los datos --}}
                <input type="hidden" name="coordenadas" id="coordenadas" value="{{ old('coordenadas', is_array($biomasa->coordenadas) ? json_encode($biomasa->coordenadas) : $biomasa->coordenadas) }}">
                <input type="hidden" name="area_m2" id="area_m2" value="{{ old('area_m2', $biomasa->area_m2) }}">
                <input type="hidden" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $biomasa->ubicacion) }}">
            </div>
        </div>
    </div>

    <div class="col-md-12 mt20 mt-3">
        <button type="submit" class="btn btn-success btn-lg btn-block">
            <i class="fas fa-save"></i> Guardar Zona de Biomasa
        </button>
        <a href="{{ route('biomasas.index') }}" class="btn btn-danger btn-lg btn-block mt-2">
            <i class="fas fa-arrow-left"></i> Cancelar y Volver
        </a>
    </div>
</div>

@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/@turf/turf@6/turf.min.js"></script>
<script>
    let map;
    let polygonPoints = [];
    let markers = [];
    let polygon = null;
    let isComplete = false;
    let currentColor = '#4CAF50'; // Color por defecto
    
    // Cargar colores de tipos de biomasa
    const tipoColors = {
        @foreach($tipoBiomasas as $tipo)
        {{ $tipo->id }}: '{{ $tipo->color ?? "#4CAF50" }}',
        @endforeach
    };
    
    // Actualizar color cuando cambie el tipo de biomasa
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo_biomasa_id');
        if (tipoSelect) {
            tipoSelect.addEventListener('change', function() {
                const selectedTipo = this.value;
                if (selectedTipo && tipoColors[selectedTipo]) {
                    currentColor = tipoColors[selectedTipo];
                    // Redibujar polígono con nuevo color
                    if (polygon) {
                        drawPolygon();
                    }
                }
            });
            
            // Establecer color inicial si hay un tipo seleccionado
            if (tipoSelect.value && tipoColors[tipoSelect.value]) {
                currentColor = tipoColors[tipoSelect.value];
            }
        }
        
        initMap();
    });
    
    // Inicializar mapa
    function initMap() {
        const centerPosition = [-17.8, -61.5]; // Chiquitanía
        
        map = L.map('biomasaMap').setView(centerPosition, 9);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);
        
        // Cargar datos existentes si estamos editando
        const existingCoords = document.getElementById('coordenadas').value;
        if (existingCoords) {
            try {
                const coords = JSON.parse(existingCoords);
                if (Array.isArray(coords) && coords.length > 0) {
                    polygonPoints = coords;
                    drawPolygon();
                    isComplete = true;
                    updateUI();
                    
                    // Centrar en el polígono existente
                    if (polygon) {
                        map.fitBounds(polygon.getBounds(), { padding: [50, 50] });
                    }
                }
            } catch (e) {
                console.error('Error parsing existing coordinates:', e);
            }
        }
        
        // Evento de clic en el mapa
        map.on('click', function(e) {
            if (isComplete) {
                alert('El polígono ya está completo. Usa "Reiniciar Polígono" para empezar de nuevo.');
                return;
            }
            
            if (polygonPoints.length >= 20) {
                alert('Máximo 20 puntos permitidos');
                return;
            }
            
            const { lat, lng } = e.latlng;
            addPoint([lat, lng]);
        });
    }
    
    function addPoint(latlng) {
        polygonPoints.push(latlng);
        
        // Agregar marcador con el color actual
        const marker = L.circleMarker(latlng, {
            radius: 6,
            fillColor: currentColor,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }).addTo(map);
        
        marker.bindPopup(`Punto ${polygonPoints.length}`).openPopup();
        markers.push(marker);
        
        drawPolygon();
        updateUI();
    }
    
    function drawPolygon() {
        // Eliminar polígono anterior
        if (polygon) {
            map.removeLayer(polygon);
        }
        
        if (polygonPoints.length < 3) return;
        
        // Función para oscurecer el color (para el borde)
        function darkenColor(color, percent) {
            const num = parseInt(color.replace("#",""), 16);
            const amt = Math.round(2.55 * percent);
            const R = (num >> 16) - amt;
            const G = (num >> 8 & 0x00FF) - amt;
            const B = (num & 0x0000FF) - amt;
            return "#" + (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + 
                   (G<255?G<1?0:G:255)*0x100 + 
                   (B<255?B<1?0:B:255)).toString(16).slice(1);
        }
        
        // Dibujar nuevo polígono con el color del tipo de biomasa
        polygon = L.polygon(polygonPoints, {
            color: darkenColor(currentColor, 30), // Borde más oscuro
            fillColor: currentColor,
            fillOpacity: 0.5,
            weight: 3,
            opacity: 1
        }).addTo(map);
        
        // Ajustar vista al polígono
        map.fitBounds(polygon.getBounds(), { padding: [50, 50] });
        
        calculateArea();
    }
    
    function calculateArea() {
        if (polygonPoints.length < 3) {
            document.getElementById('areaDisplay').textContent = '0';
            document.getElementById('perimeterDisplay').textContent = '0';
            document.getElementById('area_m2').value = '0';
            return;
        }
        
        // Crear polígono de Turf (cerrando el polígono)
        const turfPolygon = turf.polygon([[...polygonPoints.map(p => [p[1], p[0]]), [polygonPoints[0][1], polygonPoints[0][0]]]]);
        
        // Calcular área
        const areaM2 = turf.area(turfPolygon);
        const areaKm2 = (areaM2 / 1_000_000).toFixed(2);
        
        // Calcular perímetro
        const perimeterM = turf.length(turfPolygon, { units: 'meters' }) * 1000;
        const perimeterKm = (perimeterM / 1000).toFixed(2);
        
        // Actualizar UI
        document.getElementById('areaDisplay').textContent = areaKm2;
        document.getElementById('perimeterDisplay').textContent = perimeterKm;
        document.getElementById('area_m2').value = Math.round(areaM2);
        
        // Actualizar coordenadas en campo oculto
        document.getElementById('coordenadas').value = JSON.stringify(polygonPoints);
        
        // Calcular centroide para ubicación
        const centroid = turf.centroid(turfPolygon);
        const [lng, lat] = centroid.geometry.coordinates;
        document.getElementById('ubicacion').value = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
    }
    
    function updateUI() {
        document.getElementById('pointCount').textContent = polygonPoints.length;
        
        // Mostrar/ocultar botones
        if (polygonPoints.length > 0) {
            document.getElementById('resetBtn').style.display = 'inline-block';
        }
        
        if (polygonPoints.length >= 3 && !isComplete) {
            document.getElementById('completeBtn').style.display = 'inline-block';
        } else {
            document.getElementById('completeBtn').style.display = 'none';
        }
        
        // Mostrar lista de puntos
        let pointsHtml = '';
        polygonPoints.forEach((point, index) => {
            pointsHtml += `<span class="polygon-point" style="background-color: ${currentColor};">Punto ${index + 1}: ${point[0].toFixed(6)}, ${point[1].toFixed(6)}</span>`;
        });
        document.getElementById('pointsList').innerHTML = pointsHtml;
    }
    
    function resetPolygon() {
        if (!confirm('¿Estás seguro de que deseas reiniciar el polígono?')) {
            return;
        }
        
        // Limpiar marcadores
        markers.forEach(m => map.removeLayer(m));
        markers = [];
        
        // Limpiar polígono
        if (polygon) {
            map.removeLayer(polygon);
            polygon = null;
        }
        
        polygonPoints = [];
        isComplete = false;
        
        document.getElementById('coordenadas').value = '';
        document.getElementById('area_m2').value = '0';
        document.getElementById('ubicacion').value = '';
        
        updateUI();
        calculateArea();
    }
    
    function completePolygon() {
        if (polygonPoints.length < 3) {
            alert('Se necesitan al menos 3 puntos para completar el polígono');
            return;
        }
        
        isComplete = true;
        updateUI();
        alert(`Polígono completado con ${polygonPoints.length} puntos. Área: ${document.getElementById('areaDisplay').textContent} km²`);
    }
</script>
@endpush
