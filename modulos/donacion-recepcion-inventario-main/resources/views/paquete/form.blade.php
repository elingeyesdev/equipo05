<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Información del Paquete</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5>¡Error de validación!</h5>
                <p>Por favor, corrige los siguientes errores antes de continuar:</p>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Campo oculto con id_paquete en modo edición --}}
        @if(isset($paquete) && $paquete->id_paquete)
            <input type="hidden" name="id_paquete" value="{{ $paquete->id_paquete }}">
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="codigo_paquete">Código del Paquete</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                        </div>
                        @if(isset($paquete) && $paquete->id_paquete)
                            <input type="text" name="codigo_paquete"
                                class="form-control @error('codigo_paquete') is-invalid @enderror"
                                value="{{ old('codigo_paquete', $paquete?->codigo_paquete) }}" id="codigo_paquete"
                                placeholder="Código del paquete" readonly>
                        @else
                            <input type="text" name="codigo_paquete" class="form-control"
                                value="{{ old('codigo_paquete') }}" id="codigo_paquete"
                                placeholder="Se generará automáticamente" readonly>
                        @endif
                        <input type="hidden" name="paquete_externo_id" id="paquete_externo_id" value="">
                        @error('codigo_paquete')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    @if(!isset($paquete) || !$paquete->id_paquete)
                        <small class="form-text text-muted">El código se generará automáticamente al crear el
                            paquete</small>
                    @endif
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-clipboard-check"></i></span>
                        </div>
                        <select name="estado" class="form-control @error('estado') is-invalid @enderror" id="estado">
                            <option value="">Seleccione un estado</option>
                            <option value="pendiente" {{ old('estado', $paquete?->estado ?? 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ old('estado', $paquete?->estado ?? 'pendiente') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="despachado" {{ old('estado', $paquete?->estado ?? 'pendiente') == 'despachado' ? 'selected' : '' }}>Despachado</option>
                            <option value="cancelado" {{ old('estado', $paquete?->estado ?? 'pendiente') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                        </select>
                        @error('estado')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                @if(isset($paquete) && $paquete->fecha_creacion)
                    <div class="form-group">
                        <label for="fecha_creacion">Fecha de Creación</label>
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($paquete->fecha_creacion)->format('d/m/Y H:i') }}" readonly>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card card-secondary card-outline mt-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-box-open"></i> Productos del Paquete</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="productos-table">
                <thead>
                    <tr>
                        <th width="50%">Producto Disponible</th>
                        <th width="20%">Total Disponible</th>
                        <th width="20%">Cantidad a Usar</th>
                        <th width="10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($paquete) && isset($paquete->detalles_agrupados) && count($paquete->detalles_agrupados) > 0)
                        @foreach($paquete->detalles_agrupados as $index => $detalle)
                            <tr id="row-{{ $index }}">
                                <td>
                                    <select name="detalles[{{ $index }}][id_producto]" class="form-control producto-select" data-row="{{ $index }}" required>
                                        <option value="">Seleccione Producto</option>
                                        @foreach($productosDisponibles as $prod)
                                            <option value="{{ $prod['id_producto'] }}" 
                                                data-cantidad="{{ $prod['total_disponible'] }}" 
                                                {{ $detalle['id_producto'] == $prod['id_producto'] ? 'selected' : '' }}>
                                                {{ $prod['nombre'] }} (Disp: {{ $prod['total_disponible'] }} {{ $prod['unidad_medida'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    {{-- Buscamos el disponible del producto seleccionado --}}
                                    @php
                                        $disponible = 0;
                                        foreach($productosDisponibles as $p) {
                                            if($p['id_producto'] == $detalle['id_producto']) {
                                                $disponible = $p['total_disponible'];
                                                break;
                                            }
                                        }
                                        // En modo edición, el disponible real es lo que hay libre + lo que ya estamos usando
                                        $disponibleReal = $disponible + $detalle['cantidad_usada'];
                                    @endphp
                                    <input type="text" class="form-control cantidad-disponible" id="disponible-{{ $index }}" value="{{ $disponibleReal }}" readonly>
                                </td>
                                <td>
                                    <input type="number" name="detalles[{{ $index }}][cantidad_usada]" class="form-control cantidad-input" min="1" max="{{ $disponibleReal }}" value="{{ $detalle['cantidad_usada'] }}" required>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr id="row-0">
                            <td>
                                <select name="detalles[0][id_producto]" class="form-control producto-select" data-row="0" required>
                                    <option value="">Seleccione Producto</option>
                                    @foreach($productosDisponibles as $prod)
                                        <option value="{{ $prod['id_producto'] }}" data-cantidad="{{ $prod['total_disponible'] }}">
                                            {{ $prod['nombre'] }} (Disp: {{ $prod['total_disponible'] }} {{ $prod['unidad_medida'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control cantidad-disponible" id="disponible-0" readonly>
                            </td>
                            <td>
                                <input type="number" name="detalles[0][cantidad_usada]" class="form-control cantidad-input" min="1" required>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <button type="button" class="btn btn-success btn-sm mt-2" id="add-row"><i class="fas fa-plus"></i> Agregar Producto</button>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Paquete</button>
        <a href="{{ route('inventario.paquete.index') }}" class="btn btn-secondary float-right"><i class="fas fa-times"></i> Cancelar</a>
    </div>
</div>

<script>
    // Pasar datos de productos a JS
    const productosData = @json($productosDisponibles);
    // Si estamos editando, necesitamos saber cuánto ya estamos usando de cada producto para sumarlo al disponible visual
    const detallesActuales = @json($paquete->detalles_agrupados ?? []);
    
    let rowCount = {{ isset($paquete) && isset($paquete->detalles_agrupados) && count($paquete->detalles_agrupados) > 0 ? count($paquete->detalles_agrupados) : 1 }};

    document.addEventListener('DOMContentLoaded', function() {
        
        // Event delegation para cambios en producto
        document.querySelector('#productos-table').addEventListener('change', function(e) {
            if (e.target.classList.contains('producto-select')) {
                const rowId = e.target.dataset.row;
                const selectedOption = e.target.options[e.target.selectedIndex];
                const prodId = e.target.value;
                
                let cantidadDisp = parseFloat(selectedOption.dataset.cantidad) || 0;
                
                // Si estamos editando y este producto ya estaba seleccionado en esta fila (o en general), 
                // deberíamos considerar la cantidad que ya se está usando como parte del "disponible" para reasignar.
                // Simplificación: Si el producto seleccionado coincide con el que estaba cargado inicialmente en esta fila, sumamos su uso.
                const detalleOriginal = detallesActuales.find(d => d.id_producto == prodId);
                // Nota: Esta lógica es básica. Si el usuario cambia de producto A a B, y luego vuelve a A, 
                // el disponible será el del stock. Solo si es el mismo que ya tenía asignado sumamos.
                // Para hacerlo perfecto necesitaríamos rastrear el estado inicial de cada fila.
                // Por ahora, usamos el dataset que viene del servidor que ya debería tener el total.
                
                // Corrección: En el loop de blade ya calculamos $disponibleReal. 
                // Pero al cambiar con JS, usamos el data-cantidad que viene de $productosDisponibles (que es el remanente global).
                // Si el usuario selecciona un producto que YA estaba en el paquete, ese data-cantidad NO incluye lo que el paquete está usando.
                // Así que debemos sumarlo si existe en detallesActuales.
                
                if (detalleOriginal) {
                    cantidadDisp += parseFloat(detalleOriginal.cantidad_usada);
                }

                document.getElementById(`disponible-${rowId}`).value = cantidadDisp;
                
                const inputCantidad = document.querySelector(`input[name="detalles[${rowId}][cantidad_usada]"]`);
                inputCantidad.max = cantidadDisp;
                inputCantidad.value = ''; // Limpiar valor al cambiar producto para evitar inconsistencias
            }
        });

        // Agregar fila
        document.getElementById('add-row').addEventListener('click', function() {
            const tableBody = document.querySelector('#productos-table tbody');
            const newRow = document.createElement('tr');
            newRow.id = `row-${rowCount}`;
            
            let productosOptions = '<option value="">Seleccione Producto</option>';
            productosData.forEach(p => {
                productosOptions += `<option value="${p.id_producto}" data-cantidad="${p.total_disponible}">
                    ${p.nombre} (Disp: ${p.total_disponible} ${p.unidad_medida})
                </option>`;
            });

            newRow.innerHTML = `
                <td>
                    <select name="detalles[${rowCount}][id_producto]" class="form-control producto-select" data-row="${rowCount}" required>
                        ${productosOptions}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control cantidad-disponible" id="disponible-${rowCount}" readonly>
                </td>
                <td>
                    <input type="number" name="detalles[${rowCount}][cantidad_usada]" class="form-control cantidad-input" min="1" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button>
                </td>
            `;
            
            tableBody.appendChild(newRow);
            rowCount++;
        });

        // Eliminar fila
        document.querySelector('#productos-table').addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                const row = e.target.closest('tr');
                if (document.querySelectorAll('#productos-table tbody tr').length > 1) {
                    row.remove();
                } else {
                    alert('Debe haber al menos un producto en el paquete.');
                }
            }
        });

        // Cargar código de seguimiento desde sessionStorage si existe
        const codigoSeguimiento = sessionStorage.getItem('codigo_seguimiento');
        const paqueteExternoId = sessionStorage.getItem('paquete_externo_id');
        
        if (codigoSeguimiento) {
            document.getElementById('codigo_paquete').value = codigoSeguimiento;
            document.getElementById('paquete_externo_id').value = paqueteExternoId;
            // Limpiar sessionStorage
            sessionStorage.removeItem('codigo_seguimiento');
            sessionStorage.removeItem('paquete_externo_id');
        }
    });
</script>



