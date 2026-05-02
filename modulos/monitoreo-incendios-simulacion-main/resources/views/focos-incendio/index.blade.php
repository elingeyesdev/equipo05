@extends('layouts.app')

@section('subtitle', 'Focos de Incendio')
@section('content_header_title', 'Gestión de Focos de Incendio')
@section('content_header_subtitle', '- Listado')

@section('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapModal .modal-dialog {
        max-width: 90%;
    }
    #addFireMap {
        height: 600px;
        width: 100%;
    }
    .fire-marker {
        background: transparent !important;
        border: none !important;
    }
    /* Evitar que la paginación ocupe todo el ancho y se sobreponga */
    .pagination {
        display: inline-flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 4px;
    }
    .pagination .page-item {
        display: inline-block;
    }
    .pagination .page-link {
        white-space: nowrap;
    }
    /* No eliminar por CSS las flechas: usamos una vista de paginación personalizada sin prev/next */
</style>
@endsection

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if ($message = Session::get('success'))
                    <x-adminlte-alert theme="success" dismissable>
                        {{ $message }}
                    </x-adminlte-alert>
                @endif

                <x-adminlte-card title="Focos de Incendio" theme="danger" icon="fas fa-fire">
                    <x-slot name="toolsSlot">
                        <button type="button" class="btn btn-primary btn-sm mr-2" 
                                onclick="loadFirmsData()"
                                id="loadFirmsBtn"
                                title="Cargar focos detectados por NASA FIRMS">
                            <i class="fas fa-satellite"></i> Cargar desde NASA FIRMS
                        </button>
                        <button type="button" class="btn btn-info btn-sm mr-2" data-toggle="modal" data-target="#mapModal"
                                title="Agregar focos marcándolos en el mapa">
                            <i class="fas fa-map-marked-alt"></i> Agregar desde Mapa
                        </button>
                        <a href="{{ route('incendios.focos-incendios.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Crear Nuevo
                        </a>
                    </x-slot>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Fecha</th>
                                    <th>Ubicación</th>
                                    <th>Coordenadas</th>
                                    <th>Intensidad</th>
                                    <th style="width: 240px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($focosIncendios as $focosIncendio)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ optional($focosIncendio->fecha)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $focosIncendio->ubicacion }}</td>
                                        <td>
                                            @if($focosIncendio->coordenadas)
                                                <small class="text-muted">
                                                    @php
                                                        $coords = $focosIncendio->coordenadas;
                                                        // Normalizar: si viene como JSON string, decodificar
                                                        if (is_string($coords)) {
                                                            $decoded = json_decode($coords, true);
                                                            if (json_last_error() === JSON_ERROR_NONE) {
                                                                $coords = $decoded;
                                                            }
                                                        }

                                                        // Usar data_get para soportar arrays asociativos, indexados o stdClass
                                                        $lat = data_get($coords, 'lat', data_get($coords, 0, 'N/A'));
                                                        $lng = data_get($coords, 'lng', data_get($coords, 1, 'N/A'));
                                                    @endphp
                                                    Lat: {{ $lat }}<br>
                                                    Lng: {{ $lng }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $focosIncendio->intensidad > 7 ? 'danger' : ($focosIncendio->intensidad > 4 ? 'warning' : 'info') }}">
                                                {{ $focosIncendio->intensidad }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('incendios.focos-incendios.show', $focosIncendio->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('incendios.focos-incendios.edit', $focosIncendio->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('incendios.focos-incendios.destroy', $focosIncendio->id) }}" method="POST" style="display: inline;" 
                                                    onsubmit="return confirm('¿Está seguro de eliminar este foco?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-center">
                        {!! $focosIncendios->withQueryString()->links('vendor.pagination.no-prev-next') !!}
                    </div>
                </x-adminlte-card>
            </div>
        </div>
    </div>

    <!-- Modal para agregar focos desde el mapa -->
    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">
                        <i class="fas fa-map-marked-alt"></i> Agregar Focos de Incendio
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Instrucciones:</strong> Haz clic en el mapa para agregar focos de incendio. 
                        Los nombres de ubicación se obtendrán automáticamente.
                    </div>
                    
                    <div id="addFireMap"></div>
                    
                    <div class="mt-3" id="firesList" style="display: none;">
                        <h6><i class="fas fa-fire"></i> Focos agregados (<span id="firesCount">0</span>):</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ubicación</th>
                                        <th>Coordenadas</th>
                                        <th>Intensidad</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="firesTableBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" onclick="clearAllFires()">
                        <i class="fas fa-broom"></i> Limpiar Todo
                    </button>
                    <button type="button" class="btn btn-success" onclick="saveFires()" id="saveFiresBtn">
                        <i class="fas fa-save"></i> Guardar Focos
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let map;
let markers = [];
let fires = [];

// Inicializar mapa cuando se abre el modal
$('#mapModal').on('shown.bs.modal', function () {
    if (!map) {
        map = L.map('addFireMap').setView([-17.8857, -60.7556], 10);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Click en el mapa para agregar foco
        map.on('click', function(e) {
            addFire(e.latlng.lat, e.latlng.lng);
        });
    } else {
        map.invalidateSize();
    }
});

async function addFire(lat, lng) {
    const fireId = Date.now() + Math.random();
    
    // Agregar marcador
    const marker = L.circleMarker([lat, lng], {
        radius: 10,
        fillColor: '#dc2626',
        color: '#fff',
        weight: 2,
        opacity: 1,
        fillOpacity: 0.8
    }).addTo(map);
    
    marker.fireId = fireId;
    markers.push(marker);
    
    // Mostrar loading
    const ubicacion = 'Obteniendo ubicación...';
    
    const fire = {
        id: fireId,
        lat: lat,
        lng: lng,
        ubicacion: ubicacion,
        intensidad: 5,
        marker: marker
    };
    
    fires.push(fire);
    updateFiresList();
    
    // Obtener nombre de ubicación
    try {
        const locationName = await getLocationName(lat, lng);
        fire.ubicacion = locationName;
        updateFiresList();
    } catch (error) {
        fire.ubicacion = `Coordenadas: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        updateFiresList();
    }
}

async function getLocationName(lat, lng) {
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10&addressdetails=1`,
            {
                headers: {
                    'User-Agent': 'SIPII-App/1.0'
                }
            }
        );
        
        if (!response.ok) {
            return `Coordenadas: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        }
        
        const data = await response.json();
        const address = data.address || {};
        const parts = [];
        
        if (address.village || address.town || address.city) {
            parts.push(address.village || address.town || address.city);
        }
        if (address.county) {
            parts.push(address.county);
        }
        if (address.state) {
            parts.push(address.state);
        }
        
        return parts.length > 0 
            ? parts.join(', ') 
            : data.display_name || `Coordenadas: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            
    } catch (error) {
        console.error('Error getting location name:', error);
        return `Coordenadas: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
    }
}

function updateFiresList() {
    const count = fires.length;
    document.getElementById('firesCount').textContent = count;
    
    if (count > 0) {
        document.getElementById('firesList').style.display = 'block';
        const tbody = document.getElementById('firesTableBody');
        tbody.innerHTML = fires.map(fire => `
            <tr>
                <td>${fire.ubicacion}</td>
                <td><small class="text-muted">${fire.lat.toFixed(4)}, ${fire.lng.toFixed(4)}</small></td>
                <td>
                    <input type="number" min="1" max="10" value="${fire.intensidad}" 
                           class="form-control form-control-sm" style="width: 80px;"
                           onchange="updateIntensity('${fire.id}', this.value)">
                </td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeFire('${fire.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } else {
        document.getElementById('firesList').style.display = 'none';
    }
}

function updateIntensity(fireId, value) {
    const fire = fires.find(f => f.id == fireId);
    if (fire) {
        fire.intensidad = parseFloat(value);
    }
}

function removeFire(fireId) {
    const fire = fires.find(f => f.id == fireId);
    if (fire && fire.marker) {
        map.removeLayer(fire.marker);
    }
    fires = fires.filter(f => f.id != fireId);
    markers = markers.filter(m => m.fireId != fireId);
    updateFiresList();
}

function clearAllFires() {
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    fires = [];
    updateFiresList();
}

async function saveFires() {
    if (fires.length === 0) {
        Swal.fire('Error', 'No hay focos para guardar', 'warning');
        return;
    }
    
    const btn = document.getElementById('saveFiresBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    
    try {
        const focosData = fires.map(fire => ({
            ubicacion: fire.ubicacion,
            coordenadas: {
                lat: fire.lat,
                lng: fire.lng
            },
            intensidad: fire.intensidad,
            fecha: new Date().toISOString().split('T')[0]
        }));
        
        const response = await fetch('{{ route('incendios.focos-incendios.import-firms') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                focos: focosData
            })
        });
        
        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Error al guardar focos');
        }
        
        await Swal.fire({
            icon: 'success',
            title: 'Focos Guardados',
            text: `${fires.length} focos guardados exitosamente`,
            timer: 2000
        });
        
        $('#mapModal').modal('hide');
        clearAllFires();
        location.reload();
        
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Error al guardar focos', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Guardar Focos';
    }
}

async function loadFirmsData() {
    const btn = document.getElementById('loadFirmsBtn');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cargando...';
    
    try {
        // Obtener focos de NASA FIRMS
        const response = await fetch(@json(url('api/incendios/fires')) + '?cluster=true&radius=20&days=2');
        
        if (!response.ok) {
            throw new Error('Error al obtener datos de NASA FIRMS');
        }
        
        const data = await response.json();
        const firmsData = data.data || [];
        
        if (firmsData.length === 0) {
            Swal.fire('Sin Datos', 'No se encontraron focos de calor en los últimos 2 días', 'info');
            return;
        }
        
        // Mostrar confirmación con preview
        const result = await Swal.fire({
            icon: 'question',
            title: 'Focos Detectados por NASA FIRMS',
            html: `
                <div style="text-align: left;">
                    <p><strong>${firmsData.length}</strong> focos detectados en los últimos 2 días</p>
                    <hr>
                    <p class="text-muted">¿Deseas importar estos focos a la base de datos?</p>
                    <small>Se obtendrán automáticamente los nombres de ubicación</small>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-check"></i> Sí, Importar',
            cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d'
        });
        
        if (!result.isConfirmed) {
            return;
        }
        
        // Mostrar progreso
        Swal.fire({
            title: 'Importando Focos',
            html: '<div id="progressText">Obteniendo ubicaciones... 0%</div>',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Obtener ubicaciones para cada foco
        const focosWithLocation = [];
        for (let i = 0; i < firmsData.length; i++) {
            const fire = firmsData[i];
            
            // Actualizar progreso
            const progress = Math.round(((i + 1) / firmsData.length) * 100);
            document.getElementById('progressText').textContent = 
                `Obteniendo ubicaciones... ${progress}% (${i + 1}/${firmsData.length})`;
            
            const ubicacion = await getLocationName(fire.lat, fire.lng);
            
            focosWithLocation.push({
                ubicacion: ubicacion,
                coordenadas: {
                    lat: fire.lat,
                    lng: fire.lng
                },
                intensidad: calculateIntensity(fire.frp, fire.confidence),
                fecha: fire.date
            });
            
            // Pequeña pausa para no saturar la API
            if (i < firmsData.length - 1) {
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }
        
        // Guardar en base de datos
        document.getElementById('progressText').textContent = 'Guardando en base de datos...';
        
        const saveResponse = await fetch('{{ route('incendios.focos-incendios.import-firms') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                focos: focosWithLocation
            })
        });
        
        if (!saveResponse.ok) {
            const errorData = await saveResponse.json();
            throw new Error(errorData.message || 'Error al guardar focos');
        }
        
        await Swal.fire({
            icon: 'success',
            title: 'Importación Exitosa',
            html: `
                <div style="text-align: left;">
                    <p><i class="fas fa-check-circle text-success"></i> <strong>${focosWithLocation.length}</strong> focos importados exitosamente</p>
                    <hr>
                    <small class="text-muted">Los focos han sido registrados con sus ubicaciones</small>
                </div>
            `,
            timer: 3000
        });
        
        location.reload();
        
    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Error al cargar datos de NASA FIRMS', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

function calculateIntensity(frp, confidence) {
    // Calcular intensidad basada en FRP (Fire Radiative Power) y confianza
    let intensity = Math.min(10, Math.max(1, Math.round(frp / 20)));
    
    // Ajustar por confianza
    if (confidence === 'h') intensity = Math.min(10, intensity + 1);
    if (confidence === 'l') intensity = Math.max(1, intensity - 1);
    
    return intensity;
}
</script>
@endsection
