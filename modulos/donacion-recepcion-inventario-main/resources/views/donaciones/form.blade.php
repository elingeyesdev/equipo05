@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h5><i class="icon fas fa-ban"></i> {{ __('¡Error de validación!') }}</h5>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li><i class="fas fa-exclamation-triangle mr-1"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Paso 1: Información Básica -->
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-info-circle"></i> Paso 1: Información Básica</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="id_donante">
                Donante <span class="text-danger">*</span>
                <button type="button" class="btn btn-xs btn-info ml-1" data-toggle="modal" data-target="#createDonorModal"
                    title="Crear nuevo donante">
                    <i class="fas fa-plus"></i>
                </button>
            </label>
            <select name="id_donante" class="form-control @error('id_donante') is-invalid @enderror" id="id_donante">
                <option value="">Seleccione un donante</option>
                @foreach($donantes ?? [] as $id => $name)
                    <option value="{{ $id }}" {{ (string) old('id_donante', $donacion?->id_donante) === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @error('id_donante')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="form-group">
            <label for="tipo">Tipo de Donación <span class="text-danger">*</span></label>
            <select id="tipo" name="tipo" class="form-control @error('tipo') is-invalid @enderror">
                <option value="">-- Seleccione --</option>
                <option value="dinero" {{ old('tipo', $donacion?->tipo) === 'dinero' ? 'selected' : '' }}>Dinero</option>
                <option value="especie" {{ old('tipo', $donacion?->tipo) === 'especie' ? 'selected' : '' }}>Especie</option>
            </select>
            @error('tipo')
                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_campana">
                        Campaña (opcional)
                        <button type="button" class="btn btn-xs btn-warning ml-1" data-toggle="modal" data-target="#createCampaignModal"
                            title="Crear nueva campaña">
                            <i class="fas fa-plus"></i>
                        </button>
                    </label>
                    <select name="id_campana" class="form-control" id="id_campana">
                        <option value="">-- Ninguna --</option>
                        @foreach($campanas ?? [] as $id => $name)
                            <option value="{{ $id }}" {{ (string) old('id_campana', $donacion?->id_campana) === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_punto_recoleccion">
                        Punto de Recolección (opcional)
                        <button type="button" class="btn btn-xs btn-secondary ml-1" data-toggle="modal" data-target="#createCollectionPointModal"
                            title="Crear nuevo punto de recolección">
                            <i class="fas fa-plus"></i>
                        </button>
                    </label>
                    <select name="id_punto_recoleccion" class="form-control" id="id_punto_recoleccion">
                        <option value="">-- Ninguno --</option>
                        @foreach($puntos ?? [] as $id => $name)
                            <option value="{{ $id }}" {{ (string) old('id_punto_recoleccion', $donacion?->id_punto_recoleccion) === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Paso 2: Detalles de Donación (dinero o productos) -->
<div class="card card-success" id="card-step2">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-dollar-sign"></i> Paso 2: Detalles de la Donación</h3>
    </div>
    <div class="card-body">
        <div id="block-dinero" style="display:none;">
            <div class="form-group">
                <label for="monto">Monto <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="monto" class="form-control @error('monto') is-invalid @enderror" value="{{ old('monto', $donacion?->dinero->monto ?? '') }}" placeholder="0.00">
                @error('monto')<span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>@enderror
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="moneda">Moneda</label>
                        <input type="text" name="moneda" class="form-control" value="BOB" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="metodo_pago">Método de Pago</label>
                        <select name="metodo_pago" class="form-control">
                            <option value="">-- Seleccione --</option>
                            <option value="efectivo" {{ old('metodo_pago', $donacion?->dinero->metodo_pago ?? '') === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="transferencia" {{ old('metodo_pago', $donacion?->dinero->metodo_pago ?? '') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="pasarela" {{ old('metodo_pago', $donacion?->dinero->metodo_pago ?? '') === 'pasarela' ? 'selected' : '' }}>Pasarela</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="referencia_pago_file">Referencia de Pago (Imagen de comprobante)</label>
                <div class="custom-file">
                    <input type="file" name="referencia_pago_file" class="custom-file-input @error('referencia_pago_file') is-invalid @enderror" 
                        id="referencia_pago_file" accept="image/*" onchange="previewReferenciaPago(event)">
                    <label class="custom-file-label" for="referencia_pago_file">Seleccionar comprobante...</label>
                </div>
                @error('referencia_pago_file')
                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF, PDF. Tamaño máximo: 5MB</small>
                
                @if($donacion?->dinero?->referencia_pago)
                    <div class="mt-3">
                        <label>Comprobante actual:</label>
                        <div>
                            @if(Str::endsWith($donacion->dinero->referencia_pago, '.pdf'))
                                <a href="{{ asset($donacion->dinero->referencia_pago) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-file-pdf"></i> Ver PDF
                                </a>
                            @else
                                <img src="{{ asset($donacion->dinero->referencia_pago) }}" alt="Comprobante" 
                                    class="img-thumbnail" style="max-width: 300px; max-height: 200px;" id="current-referencia">
                            @endif
                        </div>
                    </div>
                @endif
                
                <div class="mt-3" id="preview-referencia-container" style="display: none;">
                    <label>Vista previa:</label>
                    <div>
                        <img id="referencia-preview" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                    </div>
                </div>
            </div>
        </div>

        <div id="block-detalles" style="display:none;">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="detalles-table">
                    <thead class="bg-light">
                        <tr>
                            <th width="15%">
                                Producto <span class="text-danger">*</span>
                                <button type="button" class="btn btn-xs btn-success ml-1" data-toggle="modal" data-target="#createProductModal" title="Crear nuevo producto">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </th>
                            <th width="8%">Cantidad <span class="text-danger">*</span></th>
                            <th width="8%">Unidad</th>
                            <th width="11%">Almacén <span class="text-danger">*</span></th>
                            <th width="11%">Estante <span class="text-muted">(opcional)</span></th>
                            <th width="13%">Espacio <span class="text-muted">(opcional)</span></th>
                            <th width="10%">Talla</th>
                            <th width="10%">F. Caducidad</th>
                            <th width="6%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php 
                            $oldDetalles = old('detalles');
                            $existingDetalles = ($donacion && $donacion->exists && $donacion->detalles) ? $donacion->detalles : null;
                            $hasDetalles = $oldDetalles || ($existingDetalles && $existingDetalles->count() > 0);
                        @endphp
                        
                        @if($hasDetalles)
                            @foreach(($oldDetalles ?? $existingDetalles) as $idx => $det)
                                @php
                                    // Para edición, intentamos obtener almacén y estante desde las relaciones
                                    $ubicacion = null;
                                    $espacioActual = null;
                                    $estanteActual = null;
                                    $almacenActual = null;
                                    
                                    if(is_object($det) && $det->ubicaciones && $det->ubicaciones->first()) {
                                        $ubicacion = $det->ubicaciones->first();
                                        $espacioActual = $ubicacion->espacio;
                                        if($espacioActual && $espacioActual->estante) {
                                            $estanteActual = $espacioActual->estante;
                                            $almacenActual = $estanteActual->almacene;
                                        }
                                    }
                                @endphp
                                <tr class="detalle-row">
                                    <td>
                                        <select name="detalles[{{ $idx }}][id_producto]" class="form-control form-control-sm producto-select">
                                            <option value="">-- Seleccione --</option>
                                            @foreach($productos ?? [] as $pId => $pName)
                                                <option value="{{ $pId }}" {{ (string) ($det['id_producto'] ?? $det->id_producto ?? '') === (string) $pId ? 'selected' : '' }}>{{ $pName }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input name="detalles[{{ $idx }}][cantidad]" type="number" class="form-control form-control-sm" value="{{ $det['cantidad'] ?? $det->cantidad ?? '' }}" placeholder="1" min="1" step="1"></td>
                                    <td><input name="detalles[{{ $idx }}][unidad_medida]" class="form-control form-control-sm unidad-input" value="{{ $det['unidad_medida'] ?? $det->unidad_medida ?? '' }}" placeholder="Ej: kg, unidad"></td>
                                    <td>
                                        <select class="form-control form-control-sm almacen-select" data-row="{{ $idx }}">
                                            <option value="">-- Seleccione --</option>
                                            @foreach($almacenes ?? [] as $almId => $almName)
                                                <option value="{{ $almId }}" {{ $almacenActual && $almacenActual->id_almacen == $almId ? 'selected' : '' }}>{{ $almName }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control form-control-sm estante-select" data-row="{{ $idx }}">
                                            <option value="">-- Seleccione Almacén --</option>
                                            @if($estanteActual)
                                                <option value="{{ $estanteActual->id_estante }}" selected>{{ $estanteActual->codigo_estante }}</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="detalles[{{ $idx }}][id_espacio]" class="form-control form-control-sm espacio-select" data-row="{{ $idx }}">
                                            <option value="">-- Seleccione Estante --</option>
                                            @if($espacioActual)
                                                <option value="{{ $espacioActual->id_espacio }}" selected>{{ $espacioActual->codigo_espacio }}</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select name="detalles[{{ $idx }}][id_talla]" class="form-control form-control-sm talla-select">
                                            <option value="">-- Seleccione --</option>
                                            @foreach($tallas ?? [] as $tallaId => $tallaNombre)
                                                <option value="{{ $tallaId }}" {{ (string)($det['id_talla'] ?? $det->id_talla ?? '') === (string)$tallaId ? 'selected' : '' }}>{{ $tallaNombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input name="detalles[{{ $idx }}][fecha_caducidad]" type="date" class="form-control form-control-sm fecha-caducidad-input" value="{{ $det['fecha_caducidad'] ?? $det->fecha_caducidad ?? '' }}">
                                    </td>
                                    <td><button class="btn btn-danger btn-sm remove-row" type="button"><i class="fas fa-trash"></i></button></td>
                                </tr>
                            @endforeach
                        @else
                            {{-- Fila inicial para create --}}
                            <tr class="detalle-row">
                                <td>
                                    <select name="detalles[0][id_producto]" class="form-control form-control-sm producto-select">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($productos ?? [] as $pId => $pName)
                                            <option value="{{ $pId }}">{{ $pName }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input name="detalles[0][cantidad]" type="number" class="form-control form-control-sm" placeholder="1" min="1" step="1"></td>
                                <td><input name="detalles[0][unidad_medida]" class="form-control form-control-sm unidad-input" placeholder="Ej: kg, unidad"></td>
                                <td>
                                    <select class="form-control form-control-sm almacen-select" data-row="0">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($almacenes ?? [] as $almId => $almName)
                                            <option value="{{ $almId }}" {{ isset($defaultAlmacenId) && $defaultAlmacenId == $almId ? 'selected' : '' }}>{{ $almName }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select class="form-control form-control-sm estante-select" data-row="0">
                                        <option value="">-- Seleccione Almacén --</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="detalles[0][id_espacio]" class="form-control form-control-sm espacio-select" data-row="0">
                                        <option value="">-- Seleccione Estante --</option>
                                    </select>
                                </td>
                                <td>
                                    <select name="detalles[0][id_talla]" class="form-control form-control-sm talla-select">
                                        <option value="">-- Seleccione --</option>
                                        @foreach($tallas ?? [] as $tallaId => $tallaNombre)
                                            <option value="{{ $tallaId }}">{{ $tallaNombre }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input name="detalles[0][fecha_caducidad]" type="date" class="form-control form-control-sm fecha-caducidad-input">
                                </td>
                                <td><button class="btn btn-danger btn-sm remove-row" type="button"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <button id="add-row" class="btn btn-secondary btn-sm" type="button"><i class="fas fa-plus"></i> Agregar producto</button>
        </div>
    </div>
</div>

<!-- Paso 3: Información Adicional -->
<div class="card card-info" id="card-step3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-sticky-note"></i> Paso 3: Información Adicional (Opcional)</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="3" placeholder="Ingrese cualquier observación adicional...">{{ old('observaciones', $donacion?->observaciones ?? '') }}</textarea>
        </div>
    </div>
</div>



@push('js')
<script>
// Product units data from controller
// Make sure productosUnidades is a let/var so we can update it
let productosUnidades = @json($productosUnidades ?? []);
const productosCategorias = @json($productosCategorias ?? []);
const almacenesData = @json($almacenes ?? []);
const defaultAlmacenId = @json($defaultAlmacenId ?? null);

document.addEventListener('DOMContentLoaded', function(){
    const tipoSelect = document.getElementById('tipo');
    const blockDinero = document.getElementById('block-dinero');
    const blockDetalles = document.getElementById('block-detalles');
    let detalleIndex = 1;
    
    // Function to update field availability for a specific row based on product category
    function updateRowFieldAvailability(row) {
        const select = row.querySelector('.producto-select');
        const productId = select ? select.value : null;
        const categoria = productId ? productosCategorias[productId] : null;
        
        const requiresExpiry = categoria && categoria.requiere_fecha_vencimiento;
        const isClothingProduct = categoria && (categoria.requiere_talla || categoria.tipo_categoria === 'VESTIMENTA');
        
        // Get the fields for this specific row
        const fechaCaducidadInput = row.querySelector('.fecha-caducidad-input');
        const tallaSelect = row.querySelector('.talla-select');
        
        // Handle fecha_caducidad field (enabled when category requires expiry date)
        if (fechaCaducidadInput) {
            if (requiresExpiry) {
                fechaCaducidadInput.removeAttribute('readonly');
                fechaCaducidadInput.style.backgroundColor = '';
                fechaCaducidadInput.style.pointerEvents = '';
            } else {
                fechaCaducidadInput.setAttribute('readonly', 'readonly');
                fechaCaducidadInput.value = '';
                fechaCaducidadInput.style.backgroundColor = '#e9ecef';
                fechaCaducidadInput.style.pointerEvents = 'none';
            }
        }
        
        // Handle talla field (only enabled for clothing products)
        if (tallaSelect) {
            if (isClothingProduct) {
                tallaSelect.disabled = false;
                tallaSelect.style.backgroundColor = '';
                tallaSelect.style.pointerEvents = '';
            } else {
                // For select, we keep disabled but will handle submission separately
                tallaSelect.disabled = false; // Keep enabled so it submits
                tallaSelect.value = '';
                tallaSelect.style.backgroundColor = '#e9ecef';
                tallaSelect.style.pointerEvents = 'none'; // Prevent interaction visually
            }
        }
    }
    
    // Function to setup product change listeners
    function setupProductListeners(container = document, triggerChange = true) {
        container.querySelectorAll('select[name*="[id_producto]"]:not(.js-listener-attached)').forEach(function(select) {
            select.classList.add('js-listener-attached');
            
            select.addEventListener('change', function() {
                const productId = this.value;
                const row = this.closest('tr');
                const unidadInput = row.querySelector('input[name*="[unidad_medida]"]');
                
                // Handle unit autofill
                if (unidadInput && productId && productosUnidades[productId]) {
                    unidadInput.value = productosUnidades[productId];
                    unidadInput.setAttribute('readonly', 'readonly');
                    unidadInput.style.backgroundColor = '#e9ecef';
                } else if (unidadInput) {
                    unidadInput.value = '';
                    unidadInput.removeAttribute('readonly');
                    unidadInput.style.backgroundColor = '';
                }
                
                // Update field availability for this specific row
                updateRowFieldAvailability(row);
            });
            
            // Trigger change event for existing selections if requested
            if (triggerChange && select.value) {
                select.dispatchEvent(new Event('change'));
            }
        });
    }

    // Function to setup cascading dropdowns
    function setupCascadingDropdowns(container = document, triggerChange = true) {
        // Almacen change -> load Estantes
        container.querySelectorAll('.almacen-select:not(.js-listener-attached)').forEach(function(almacenSelect) {
            almacenSelect.classList.add('js-listener-attached');
            
            almacenSelect.addEventListener('change', function() {
                const almacenId = this.value;
                const rowId = this.getAttribute('data-row');
                const estanteSelect = document.querySelector(`.estante-select[data-row="${rowId}"]`);
                const espacioSelect = document.querySelector(`.espacio-select[data-row="${rowId}"]`);
                
                // Reset estante and espacio
                estanteSelect.innerHTML = '<option value="">-- Seleccione Almacén --</option>';
                espacioSelect.innerHTML = '<option value="">-- Seleccione Estante --</option>';
                
                if (almacenId) {
                    // Fetch estantes for this almacen
                    fetch(`/api/almacenes/${almacenId}/estantes`)
                        .then(response => response.json())
                        .then(estantes => {
                            estanteSelect.innerHTML = '<option value="">-- Seleccione --</option>';
                            estantes.forEach(estante => {
                                const option = document.createElement('option');
                                option.value = estante.id_estante;
                                option.textContent = `${estante.codigo_estante} - ${estante.descripcion || ''}`;
                                estanteSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error loading estantes:', error));
                }
            });
            
            // Trigger on page load if value exists and requested
            if (triggerChange && almacenSelect.value) {
                almacenSelect.dispatchEvent(new Event('change'));
            }
        });
        
        // Estante change -> load Espacios
        container.querySelectorAll('.estante-select:not(.js-listener-attached)').forEach(function(estanteSelect) {
            estanteSelect.classList.add('js-listener-attached');
            
            estanteSelect.addEventListener('change', function() {
                const estanteId = this.value;
                const rowId = this.getAttribute('data-row');
                const espacioSelect = document.querySelector(`.espacio-select[data-row="${rowId}"]`);
                
                // Reset espacio
                espacioSelect.innerHTML = '<option value="">-- Seleccione Estante --</option>';
                
                if (estanteId) {
                    // Fetch espacios for this estante
                    fetch(`/api/estantes/${estanteId}/espacios`)
                        .then(response => response.json())
                        .then(espacios => {
                            espacioSelect.innerHTML = '<option value="">-- Seleccione --</option>';
                            espacios.forEach(espacio => {
                                const option = document.createElement('option');
                                option.value = espacio.id_espacio;
                                option.textContent = espacio.codigo_espacio;
                                espacioSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error loading espacios:', error));
                }
            });
            
            // Trigger on page load if value exists and requested
            if (triggerChange && estanteSelect.value) {
                estanteSelect.dispatchEvent(new Event('change'));
            }
        });
    }
    
    function toggleBlocks(){
        const tipo = tipoSelect.value;
        
        console.log('Tipo seleccionado:', tipo);
        
        if(tipo === 'dinero'){
            blockDinero.style.display = 'block';
            blockDetalles.style.display = 'none';
        } else if(tipo === 'especie'){
            blockDinero.style.display = 'none';
            blockDetalles.style.display = 'block';
            
            // Calculate next index based on max existing index to avoid collisions
            let maxIndex = -1;
            document.querySelectorAll('[name^="detalles["]').forEach(el => {
                const match = el.getAttribute('name').match(/detalles\[(\d+)\]/);
                if(match){
                    const idx = parseInt(match[1]);
                    if(idx > maxIndex) maxIndex = idx;
                }
            });
            detalleIndex = maxIndex + 1;
            console.log('Filas existentes:', detalleIndex);
            
            // Setup listeners for existing rows
            setupProductListeners();
            setupCascadingDropdowns();
        } else {
            blockDinero.style.display = 'none';
            blockDetalles.style.display = 'none';
        }
    }
    
    tipoSelect.addEventListener('change', toggleBlocks);
    toggleBlocks();
    
    // Agregar filas - usar event delegation
    document.addEventListener('click', function(e){
        const target = e.target.closest('#add-row');
        if(target){
            e.preventDefault();
            console.log('Agregando nueva fila...');
            
            const tbody = document.querySelector('#detalles-table tbody');
            if(!tbody){
                console.error('No se encontró tbody');
                return;
            }
            
            // Forzar visibilidad temporal para buscar filas
            const wasHidden = blockDetalles.style.display === 'none';
            if(wasHidden){
                blockDetalles.style.display = 'block';
            }
            
            const allRows = tbody.querySelectorAll('tr.detalle-row');
            console.log('Filas encontradas:', allRows.length);
            
            if(wasHidden){
                blockDetalles.style.display = 'none';
            }
            
            if(allRows.length === 0){
                console.error('No hay filas para clonar');
                alert('Error: No se puede agregar fila');
                return;
            }
            
            const templateRow = allRows[allRows.length - 1]; // Use last row instead of first
            const newRow = templateRow.cloneNode(true);
            
            // Store previous row's warehouse, shelf AND space values
            const prevAlmacenSelect = templateRow.querySelector('.almacen-select');
            const prevEstanteSelect = templateRow.querySelector('.estante-select');
            const prevEspacioSelect = templateRow.querySelector('.espacio-select');
            
            const prevAlmacenValue = prevAlmacenSelect ? prevAlmacenSelect.value : '';
            const prevEstanteValue = prevEstanteSelect ? prevEstanteSelect.value : '';
            const prevEstanteOptions = prevEstanteSelect ? prevEstanteSelect.innerHTML : '';
            const prevEspacioValue = prevEspacioSelect ? prevEspacioSelect.value : '';
            const prevEspacioOptions = prevEspacioSelect ? prevEspacioSelect.innerHTML : '';
            
            // Remove js-listener-attached class from all elements in newRow
            newRow.querySelectorAll('.js-listener-attached').forEach(el => el.classList.remove('js-listener-attached'));

            // Calculate next index based on max existing index to avoid collisions
            let maxIndex = -1;
            document.querySelectorAll('[name^="detalles["]').forEach(el => {
                const match = el.getAttribute('name').match(/detalles\[(\d+)\]/);
                if(match){
                    const idx = parseInt(match[1]);
                    if(idx > maxIndex) maxIndex = idx;
                }
            });
            let detalleIndex = maxIndex + 1;

            // Actualizar índices y limpiar valores
            newRow.querySelectorAll('input, select, textarea').forEach(function(input){
                const name = input.getAttribute('name');
                const dataRow = input.getAttribute('data-row');
                
                if(name){
                    const newName = name.replace(/detalles\[\d+\]/, 'detalles[' + detalleIndex + ']');
                    input.setAttribute('name', newName);
                    
                    // Update data-row attribute if present
                    if(dataRow !== null){
                        input.setAttribute('data-row', detalleIndex);
                    }
                    
                    // Clear values for product and cantidad only. Keep almacen, estante AND espacio.
                    if(name.includes('[id_producto]') || name.includes('[cantidad]')) {
                        input.value = '';
                        if(input.tagName === 'SELECT'){
                            input.selectedIndex = 0;
                        }
                    }
                    
                    // Remove readonly from cloned unidad_medida input and clear it
                    if(name.includes('[unidad_medida]')){
                        input.removeAttribute('readonly');
                        input.style.backgroundColor = '';
                        input.value = '';
                    }
                }
                
                if(dataRow !== null){
                    input.setAttribute('data-row', detalleIndex);
                }
            });
            
            // Restore almacen, estante and espacio values in the new row
            const newAlmacenSelect = newRow.querySelector('.almacen-select');
            const newEstanteSelect = newRow.querySelector('.estante-select');
            const newEspacioSelect = newRow.querySelector('.espacio-select');
            
            // Use previous warehouse if exists, otherwise use default (Almacén Central)
            if(newAlmacenSelect) {
                if(prevAlmacenValue) {
                    newAlmacenSelect.value = prevAlmacenValue;
                } else if(defaultAlmacenId) {
                    newAlmacenSelect.value = defaultAlmacenId;
                }
            }
            
            if(newEstanteSelect && prevEstanteOptions) {
                newEstanteSelect.innerHTML = prevEstanteOptions;
                if(prevEstanteValue) {
                    newEstanteSelect.value = prevEstanteValue;
                }
            }
            
            if(newEspacioSelect && prevEspacioOptions) {
                newEspacioSelect.innerHTML = prevEspacioOptions;
                if(prevEspacioValue) {
                    newEspacioSelect.value = prevEspacioValue;
                }
            }
            
            tbody.appendChild(newRow);
            detalleIndex++;
            console.log('Fila agregada. Nuevo índice:', detalleIndex);
            
            // Setup listeners for the new row
            setupProductListeners(newRow, false);
            setupCascadingDropdowns(newRow, false);
            
            // If no previous values but default warehouse is set, trigger cascade to load shelves
            if(!prevAlmacenValue && defaultAlmacenId && newAlmacenSelect) {
                newAlmacenSelect.dispatchEvent(new Event('change'));
            }
        }
    });
    
    // Eliminar filas
    document.addEventListener('click', function(e){
        const target = e.target.closest('.remove-row');
        if(target){
            e.preventDefault();
            const row = target.closest('tr');
            const allRows = document.querySelectorAll('#detalles-table tbody tr.detalle-row');
            
            if(allRows.length > 1){
                row.remove();
                console.log('Fila eliminada');
            } else {
                alert('Debe mantener al menos un detalle.');
            }
        }
    });

    // Quick Product Creation Logic
    const quickProductForm = document.getElementById('quickProductForm');
    if(quickProductForm) {
        quickProductForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';
            
            fetch('{{ route("inventario.producto.store") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    // 1. Add new product to all dropdowns
                    const newOption = new Option(data.producto.nombre, data.producto.id_producto);
                    document.querySelectorAll('.producto-select').forEach(select => {
                        select.add(newOption.cloneNode(true));
                    });
                    
                    // 2. Update units data
                    productosUnidades[data.producto.id_producto] = data.producto.unidad_medida;
                    
                    // 3. Select in the last row
                    const rows = document.querySelectorAll('.detalle-row');
                    const lastRow = rows[rows.length - 1];
                    const select = lastRow.querySelector('.producto-select');
                    select.value = data.producto.id_producto;
                    select.dispatchEvent(new Event('change'));
                    
                    // 4. Close modal and reset form
                    $('#createProductModal').modal('hide');
                    quickProductForm.reset();
                } else {
                    alert('Error al crear producto: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Ocurrió un error al procesar la solicitud.';
                
                if (error.errors) {
                    errorMessage = 'Errores de validación:\n';
                    Object.keys(error.errors).forEach(key => {
                        errorMessage += `- ${error.errors[key].join(', ')}\n`;
                    });
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                alert(errorMessage);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            });
        });
    }

    // Quick Category Creation Logic - Hide product modal while category modal is open
    const btnOpenCategoryModal = document.getElementById('btnOpenCategoryModal');
    const closeCategoryModal = document.getElementById('closeCategoryModal');
    const cancelCategoryModal = document.getElementById('cancelCategoryModal');
    const quickCategoryForm = document.getElementById('quickCategoryForm');

    // Open category modal
    if(btnOpenCategoryModal) {
        btnOpenCategoryModal.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Hide product modal temporarily
            $('#createProductModal').modal('hide');
            
            // Show category modal using Bootstrap modal API
            setTimeout(function() {
                $('#createCategoryModal').modal('show');
                
                // Focus the first input after modal is shown
                setTimeout(function() {
                    $('#category_nombre').focus();
                }, 300);
            }, 500); // Wait for product modal to fully hide
        });
    }

    // Close category modal function
    function closeCategoryModalFunc() {
        // Hide category modal
        $('#createCategoryModal').modal('hide');
        
        // Show product modal again after category modal is hidden
        setTimeout(function() {
            $('#createProductModal').modal('show');
        }, 500);
        
        if(quickCategoryForm) {
            quickCategoryForm.reset();
        }
    }

    // Close button handlers
    if(closeCategoryModal) {
        closeCategoryModal.addEventListener('click', closeCategoryModalFunc);
    }
    if(cancelCategoryModal) {
        cancelCategoryModal.addEventListener('click', closeCategoryModalFunc);
    }

    // Category form submission
    if(quickCategoryForm) {
        quickCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';
            
            fetch('{{ route("inventario.categorias-producto.store") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    // 1. Add new category to the dropdown in product modal
                    const categorySelect = document.getElementById('modal_id_categoria');
                    const newOption = new Option(data.categoria.nombre, data.categoria.id_categoria);
                    categorySelect.add(newOption);
                    
                    // 2. Select the new category
                    categorySelect.value = data.categoria.id_categoria;
                    
                    // 3. Close modal and reset form
                    closeCategoryModalFunc();
                } else {
                    alert('Error al crear categoría: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Ocurrió un error al procesar la solicitud.';
                
                if (error.errors) {
                    errorMessage = 'Errores de validación:\n';
                    Object.keys(error.errors).forEach(key => {
                        errorMessage += `- ${error.errors[key].join(', ')}\n`;
                    });
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                alert(errorMessage);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            });
        });
    }

    // Quick Donor Creation Logic
    const quickDonorForm = document.getElementById('quickDonorForm');
    if(quickDonorForm) {
        quickDonorForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';
            
            fetch('{{ route("inventario.donante.store") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    // 1. Add new donor to dropdown
                    const donanteSelect = document.getElementById('id_donante');
                    const newOption = new Option(data.donante.nombre, data.donante.id_donante);
                    donanteSelect.add(newOption);
                    
                    // 2. Select the new donor
                    donanteSelect.value = data.donante.id_donante;
                    
                    // 3. Close modal and reset form
                    $('#createDonorModal').modal('hide');
                    quickDonorForm.reset();
                } else {
                    alert('Error al crear donante: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Ocurrió un error al procesar la solicitud.';
                
                if (error.errors) {
                    errorMessage = 'Errores de validación:\n';
                    Object.keys(error.errors).forEach(key => {
                        errorMessage += `- ${error.errors[key].join(', ')}\n`;
                    });
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                alert(errorMessage);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            });
        });
    }

    // Quick Campaign Creation Logic
    const quickCampaignForm = document.getElementById('quickCampaignForm');
    if(quickCampaignForm) {
        quickCampaignForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';

            fetch('{{ route("inventario.campana.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    // 1. Add new campaign to dropdown
                    const campaignSelect = document.getElementById('id_campana');
                    const newOption = new Option(data.campana.nombre, data.campana.id_campana);
                    campaignSelect.add(newOption);
                    
                    // 2. Select the new campaign
                    campaignSelect.value = data.campana.id_campana;
                    
                    // 3. Close modal and reset form
                    $('#createCampaignModal').modal('hide');
                    quickCampaignForm.reset();
                } else {
                    alert('Error al crear campaña: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Ocurrió un error al procesar la solicitud.';
                
                if (error.errors) {
                    errorMessage = 'Errores de validación:\n';
                    Object.keys(error.errors).forEach(key => {
                        errorMessage += `- ${error.errors[key].join(', ')}\n`;
                    });
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                alert(errorMessage);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            });
        });
    }

    // Quick Collection Point Creation Logic
    const quickCollectionPointForm = document.getElementById('quickCollectionPointForm');
    if(quickCollectionPointForm) {
        quickCollectionPointForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';

            fetch('{{ route("inventario.puntos-recoleccion.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    // 1. Add new point to the dropdown
                    const pointSelect = document.getElementById('id_punto_recoleccion');
                    const newOption = document.createElement('option');
                    newOption.value = data.punto.id_punto_recoleccion;
                    newOption.text = data.punto.nombre;
                    newOption.selected = true;
                    pointSelect.appendChild(newOption);
                    
                    // 2. Close modal and reset form
                    $('#createCollectionPointModal').modal('hide');
                    quickCollectionPointForm.reset();
                    
                    // 3. Show success message (optional)
                    // alert('Punto de recolección creado exitosamente');
                } else {
                    alert('Error al crear punto de recolección: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Ocurrió un error al procesar la solicitud.';
                
                if (error.errors) {
                    // Validation errors
                    errorMessage = 'Errores de validación:\n';
                    Object.keys(error.errors).forEach(key => {
                        errorMessage += `- ${error.errors[key].join(', ')}\n`;
                    });
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                alert(errorMessage);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            });
        });
    }
});

// Preview de referencia de pago
function previewReferenciaPago(event) {
    const file = event.target.files[0];
    const label = event.target.nextElementSibling;
    const previewContainer = document.getElementById('preview-referencia-container');
    const preview = document.getElementById('referencia-preview');
    const currentReferencia = document.getElementById('current-referencia');
    
    if (file) {
        label.textContent = file.name;
        
        // Solo mostrar preview si es imagen
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.style.display = 'block';
                if (currentReferencia) {
                    currentReferencia.style.opacity = '0.5';
                }
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    } else {
        label.textContent = 'Seleccionar comprobante...';
        previewContainer.style.display = 'none';
        if (currentReferencia) {
            currentReferencia.style.opacity = '1';
        }
    }
}
</script>
@endpush






