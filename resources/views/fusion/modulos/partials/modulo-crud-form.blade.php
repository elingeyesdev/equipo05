@php
    $coordenadasMapaActivo = in_array($seccion, ['destino', 'ubicacion', 'focos-calor', 'reportes-incendio'], true)
        && in_array('latitud', $columns ?? [], true)
        && in_array('longitud', $columns ?? [], true);
    $esLogistica = ($moduloKey ?? '') === 'logistica';
    $esSeguimiento = ($moduloKey ?? '') === 'seguimiento';
    $listCardClass = $esLogistica ? 'logistica-list-card shadow-sm'
        : ($esSeguimiento ? 'seg-list-card shadow-sm' : 'shadow-sm');
@endphp

@if($coordenadasMapaActivo)
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #modulo-coordenadas-map { z-index: 1; border: 1px solid #dee2e6; height: 360px; border-radius: 8px; }
</style>
@endpush
@endif

<div class="container-fluid px-0">
    <div class="card {{ $listCardClass }}">
        <div class="card-header d-flex justify-content-between align-items-center">
            @unless($esLogistica || $esSeguimiento)
                <strong>{{ $registro ? 'Editar' : 'Crear' }} {{ $tituloSeccion }}</strong>
            @else
                <span></span>
            @endunless
            <a href="{{ route("{$routePrefix}.$seccion") }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ $registro ? route("{$routePrefix}.crud.update", ['seccion' => $seccion, 'id' => data_get($registro, $primaryKey)]) : route("{$routePrefix}.crud.store", ['seccion' => $seccion]) }}">
                @csrf
                @if($registro)
                    @method('PUT')
                @endif
                <div class="row">
                    @foreach($columns as $column)
                        @php
                            $isIdField = str_starts_with($column, 'id_') || str_ends_with($column, '_id');
                            $label = str_replace('_', ' ', $column);
                            if (str_starts_with($label, 'id ')) {
                                $label = substr($label, 3);
                            }
                            if (str_ends_with($label, ' id')) {
                                $label = substr($label, 0, -3);
                            }
                            $label = ucwords(trim($label));
                            $placeholder = \App\Support\ModuloCrudEjemplos::placeholder($moduloKey, $seccion, $column);
                        @endphp
                        <div class="col-md-4 mb-3">
                            <label>{{ $label }}</label>
                            @if($isIdField)
                                <select name="{{ $column }}" class="form-control">
                                    @if(!empty($options[$column]))
                                        <option value="">Seleccione {{ strtolower($label) }}...</option>
                                        @foreach($options[$column] as $option)
                                            @php $value = data_get($registro, $column, old($column)); @endphp
                                            <option value="{{ $option->id }}" {{ (string) $value === (string) $option->id ? 'selected' : '' }}>
                                                {{ $option->nombre }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Sin opciones disponibles</option>
                                    @endif
                                </select>
                            @elseif(str_contains($column, 'fecha'))
                                <input type="date" name="{{ $column }}" class="form-control" value="{{ old($column, data_get($registro, $column)) }}">
                                <small class="text-muted">Formato: dia/mes/anio.</small>
                            @elseif($coordenadasMapaActivo && $column === 'latitud')
                                <input type="number" step="any" name="{{ $column }}" id="modulo-coordenadas-lat" class="form-control" value="{{ old($column, data_get($registro, $column)) }}" placeholder="{{ $placeholder }}">
                            @elseif($coordenadasMapaActivo && $column === 'longitud')
                                <input type="number" step="any" name="{{ $column }}" id="modulo-coordenadas-lng" class="form-control" value="{{ old($column, data_get($registro, $column)) }}" placeholder="{{ $placeholder }}">
                            @elseif($seccion === 'helpdesk' && $column === 'estado')
                                <select name="{{ $column }}" class="form-control">
                                    @foreach(['abierta', 'en_proceso', 'resuelta', 'cerrada'] as $estado)
                                        <option value="{{ $estado }}" {{ old($column, data_get($registro, $column, 'abierta')) === $estado ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $estado)) }}</option>
                                    @endforeach
                                </select>
                            @elseif($seccion === 'helpdesk' && $column === 'prioridad')
                                <select name="{{ $column }}" class="form-control">
                                    @foreach(['baja', 'media', 'alta'] as $prioridad)
                                        <option value="{{ $prioridad }}" {{ old($column, data_get($registro, $column, 'media')) === $prioridad ? 'selected' : '' }}>{{ ucfirst($prioridad) }}</option>
                                    @endforeach
                                </select>
                            @elseif(in_array($column, ['activo', 'administrador', 'usado'], true))
                                @php
                                    $rawBool = old($column, data_get($registro, $column, $registro ? null : '1'));
                                    $boolVal = filter_var($rawBool, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                                    if ($boolVal === null && $rawBool !== null && $rawBool !== '') {
                                        $boolVal = in_array(strtolower((string) $rawBool), ['1', 'true', 'si', 'sí', 'activo'], true);
                                    }
                                    if ($boolVal === null) {
                                        $boolVal = true;
                                    }
                                @endphp
                                <select name="{{ $column }}" class="form-control">
                                    <option value="1" {{ $boolVal ? 'selected' : '' }}>Sí</option>
                                    <option value="0" {{ ! $boolVal ? 'selected' : '' }}>No</option>
                                </select>
                            @elseif($column === 'tipo_sangre')
                                <select name="{{ $column }}" class="form-control">
                                    <option value="">Seleccione tipo de sangre...</option>
                                    @foreach(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'] as $tipo)
                                        <option value="{{ $tipo }}" {{ old($column, data_get($registro, $column)) === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                    @endforeach
                                </select>
                            @elseif(str_contains($column, 'descripcion') || str_contains($column, 'contenido') || str_contains($column, 'insumo') || str_contains($column, 'observacion'))
                                <textarea name="{{ $column }}" rows="3" class="form-control" placeholder="{{ $placeholder }}">{{ old($column, data_get($registro, $column)) }}</textarea>
                            @elseif(str_contains($column, 'cantidad') || str_contains($column, 'capacidad') || str_contains($column, 'puntaje') || str_contains($column, 'nota'))
                                <input type="number" name="{{ $column }}" class="form-control" value="{{ old($column, data_get($registro, $column)) }}" placeholder="{{ $placeholder }}">
                            @else
                                <input type="text" name="{{ $column }}" class="form-control" value="{{ old($column, data_get($registro, $column)) }}" placeholder="{{ $placeholder }}">
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($coordenadasMapaActivo)
                <div class="row mt-2">
                    <div class="col-12 mb-3">
                        <label class="d-block font-weight-bold">Ubicacion en mapa</label>
                        <p class="small text-muted mb-2">Haz clic en el mapa para definir latitud y longitud.</p>
                        <div id="modulo-coordenadas-map"></div>
                    </div>
                </div>
                @endif

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </form>
        </div>
    </div>
</div>

@if($coordenadasMapaActivo)
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var latInput = document.getElementById('modulo-coordenadas-lat');
    var lngInput = document.getElementById('modulo-coordenadas-lng');
    if (!latInput || !lngInput) return;
    var lat = parseFloat(latInput.value) || -17.886;
    var lng = parseFloat(lngInput.value) || -63.755;
    var map = L.map('modulo-coordenadas-map').setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
    var marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    function syncInputs(ll) { latInput.value = ll.lat.toFixed(6); lngInput.value = ll.lng.toFixed(6); }
    map.on('click', function (e) { marker.setLatLng(e.latlng); syncInputs(e.latlng); });
    marker.on('dragend', function () { syncInputs(marker.getLatLng()); });
    setTimeout(function () { map.invalidateSize(); }, 300);
})();
</script>
@endpush
@endif
