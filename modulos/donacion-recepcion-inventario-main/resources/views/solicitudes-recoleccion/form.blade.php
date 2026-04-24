<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Información de la Solicitud</h3>
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

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="id_donante">Donante <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <select name="id_donante" class="form-control @error('id_donante') is-invalid @enderror"
                            id="id_donante" required {{ isset($solicitudesRecoleccion) && $solicitudesRecoleccion->id_solicitud ? 'disabled' : '' }}>
                            <option value="">Seleccione un donante</option>
                            @foreach($donantes ?? [] as $donante)
                                <option value="{{ $donante->id_donante }}" {{ old('id_donante', $solicitudesRecoleccion?->id_donante) == $donante->id_donante ? 'selected' : '' }}>
                                    {{ $donante->nombre }} ({{ $donante->tipo ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @if(isset($solicitudesRecoleccion) && $solicitudesRecoleccion->id_solicitud)
                            <input type="hidden" name="id_donante" value="{{ $solicitudesRecoleccion->id_donante }}">
                        @endif
                        @error('id_donante')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_recolector">Recolector</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                        </div>
                        <select name="id_recolector" class="form-control @error('id_recolector') is-invalid @enderror"
                            id="id_recolector">
                            <option value="">Seleccione un recolector</option>
                            @foreach($usuarios ?? [] as $usuario)
                                <option value="{{ $usuario->id_usuario }}" {{ old('id_recolector', $solicitudesRecoleccion?->id_recolector) == $usuario->id_usuario ? 'selected' : '' }}>
                                    {{ $usuario->nombres }} {{ $usuario->apellidos }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_recolector')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="fecha_programada">Fecha Programada <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        </div>
                        <input type="datetime-local" name="fecha_programada"
                            class="form-control @error('fecha_programada') is-invalid @enderror"
                            value="{{ old('fecha_programada', $solicitudesRecoleccion?->fecha_programada ? \Carbon\Carbon::parse($solicitudesRecoleccion->fecha_programada)->format('Y-m-d\TH:i') : '') }}"
                            id="fecha_programada" required>
                        @error('fecha_programada')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="estado">Estado</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                        </div>
                        <select name="estado" class="form-control @error('estado') is-invalid @enderror" id="estado">
                            <option value="">Seleccione un estado</option>
                            <option value="pendiente" {{ old('estado', $solicitudesRecoleccion?->estado ?? 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="en_proceso" {{ old('estado', $solicitudesRecoleccion?->estado ?? 'pendiente') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                            <option value="completada" {{ old('estado', $solicitudesRecoleccion?->estado ?? 'pendiente') == 'completada' ? 'selected' : '' }}>Completada</option>
                            <option value="cancelada" {{ old('estado', $solicitudesRecoleccion?->estado ?? 'pendiente') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
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
                <div class="form-group">
                    <label for="direccion_recoleccion">Dirección de Recolección <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <textarea name="direccion_recoleccion"
                            class="form-control @error('direccion_recoleccion') is-invalid @enderror"
                            id="direccion_recoleccion" placeholder="Dirección completa de recolección" rows="3"
                            required {{ isset($solicitudesRecoleccion) && $solicitudesRecoleccion->id_solicitud ? 'readonly' : '' }}>{{ old('direccion_recoleccion', $solicitudesRecoleccion?->direccion_recoleccion) }}</textarea>
                        @error('direccion_recoleccion')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-comment"></i></span>
                        </div>
                        <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                            id="observaciones" placeholder="Observaciones adicionales"
                            rows="3" {{ isset($solicitudesRecoleccion) && $solicitudesRecoleccion->id_solicitud ? 'readonly' : '' }}>{{ old('observaciones', $solicitudesRecoleccion?->observaciones) }}</textarea>
                        @error('observaciones')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                @if(isset($solicitudesRecoleccion) && $solicitudesRecoleccion->fecha_creacion)
                    <div class="form-group">
                        <label for="fecha_creacion">Fecha de Creación</label>
                        <input type="text" name="fecha_creacion" class="form-control"
                            value="{{ \Carbon\Carbon::parse($solicitudesRecoleccion->fecha_creacion)->format('d/m/Y H:i') }}"
                            id="fecha_creacion" readonly>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Formulario de Donación en Especie (solo si estado es completada) -->
<div class="card card-success" id="donacion-form" style="display: none;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-box"></i> Registrar Donación en Especie Recolectada</h3>
    </div>
    <div class="card-body">
        <input type="hidden" name="crear_donacion" id="crear_donacion" value="0">
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Al completar la recolección, puede registrar la donación en especie recibida.
        </div>

        <div class="form-group">
            <label>Donante</label>
            <input type="text" class="form-control" id="donante_nombre_readonly" readonly style="background-color: #e9ecef;">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="donacion_id_campana">Campaña (opcional)</label>
                    <select name="donacion_id_campana" class="form-control" id="donacion_id_campana">
                        <option value="">-- Ninguna --</option>
                        @foreach($campanas ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="donacion_id_punto_recoleccion">Punto de Recolección (opcional)</label>
                    <select name="donacion_id_punto_recoleccion" class="form-control" id="donacion_id_punto_recoleccion">
                        <option value="">-- Ninguno --</option>
                        @foreach($puntos ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <h5 class="mt-3">Productos Recibidos</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="detalles-donacion-table">
                <thead class="bg-light">
                    <tr>
                        <th width="25%">Producto <span class="text-danger">*</span></th>
                        <th width="12%">Cantidad <span class="text-danger">*</span></th>
                        <th width="12%">Unidad</th>
                        <th width="17%">Almacén <span class="text-danger">*</span></th>
                        <th width="17%">Estante <span class="text-danger">*</span></th>
                        <th width="17%">Espacio <span class="text-danger">*</span></th>
                        <th width="5%"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="detalle-row">
                        <td>
                            <select name="donacion_detalles[0][id_producto]" class="form-control form-control-sm donacion-producto-select">
                                <option value="">-- Seleccione --</option>
                                @foreach($productos ?? [] as $pId => $pName)
                                    <option value="{{ $pId }}">{{ $pName }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input name="donacion_detalles[0][cantidad]" type="number" class="form-control form-control-sm" placeholder="1" min="1" step="1"></td>
                        <td><input name="donacion_detalles[0][unidad_medida]" class="form-control form-control-sm donacion-unidad-input" placeholder="Ej: kg"></td>
                        <td>
                            <select class="form-control form-control-sm donacion-almacen-select" data-row="0">
                                <option value="">-- Seleccione --</option>
                                @foreach($almacenes ?? [] as $almId => $almName)
                                    <option value="{{ $almId }}">{{ $almName }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select class="form-control form-control-sm donacion-estante-select" data-row="0">
                                <option value="">-- Seleccione Almacén --</option>
                            </select>
                        </td>
                        <td>
                            <select name="donacion_detalles[0][id_espacio]" class="form-control form-control-sm donacion-espacio-select" data-row="0">
                                <option value="">-- Seleccione Estante --</option>
                            </select>
                        </td>
                        <td><button class="btn btn-danger btn-sm remove-donacion-row" type="button"><i class="fas fa-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <button id="add-donacion-row" class="btn btn-secondary btn-sm" type="button"><i class="fas fa-plus"></i> Agregar producto</button>

        <div class="form-group mt-3">
            <label for="donacion_observaciones">Observaciones</label>
            <textarea name="donacion_observaciones" class="form-control" rows="2" placeholder="Observaciones adicionales..."></textarea>
        </div>
    </div>
</div>

<!-- Botones de acción (siempre al final) -->
<div class="card-footer" id="form-actions">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Solicitud</button>
    <a href="{{ route('inventario.solicitudes-recoleccions.index') }}" class="btn btn-secondary float-right"><i
            class="fas fa-times"></i> Cancelar</a>
</div>

@push('js')
<script>
    let productosUnidades = @json($productosUnidades ?? []);
    let donacionDetalleIndex = 1;

    // Mostrar/ocultar formulario de donación según estado (ejecutar inmediatamente)
    function toggleDonacionForm() {
        const estado = $('#estado').val();
        const donacionForm = $('#donacion-form');
        const crearDonacionInput = $('#crear_donacion');
        
        console.log('Estado seleccionado:', estado); // Debug
        
        if (estado === 'completada') {
            donacionForm.show(); // Cambié slideDown por show para debug
            crearDonacionInput.val('1');
            
            // Llenar el nombre del donante
            const donanteSelect = $('#id_donante');
            const donanteNombre = donanteSelect.find('option:selected').text();
            $('#donante_nombre_readonly').val(donanteNombre);
            console.log('Formulario de donación mostrado');
        } else {
            donacionForm.hide(); // Cambié slideUp por hide
            crearDonacionInput.val('0');
            console.log('Formulario de donación ocultado');
        }
    }

    // Execute on page load
    $(document).ready(function () {
        // Check initial estado value
        toggleDonacionForm();
        
        // Listen to estado changes
        $('#estado').on('change', toggleDonacionForm);
        
        // Update donante name when donante select changes
        $('#id_donante').on('change', function() {
            if ($('#estado').val() === 'completada') {
                const donanteNombre = $(this).find('option:selected').text();
                $('#donante_nombre_readonly').val(donanteNombre);
            }
        });

        // Validación en tiempo real para campos requeridos
        const requiredFields = {
            'id_donante': 'El campo donante es obligatorio',
            'direccion_recoleccion': 'El campo dirección de recolección es obligatorio',
            'fecha_programada': 'El campo fecha programada es obligatorio'
        };

        // Función para mostrar error
        function showError(fieldName, message) {
            const field = $('#' + fieldName);
            const errorSpan = $(`.error-message[data-field="${fieldName}"]`);

            field.addClass('is-invalid').removeClass('is-valid');
            errorSpan.find('.error-text').text(message);
            errorSpan.show();
        }

        // Función para ocultar error
        function hideError(fieldName) {
            const field = $('#' + fieldName);
            const errorSpan = $(`.error-message[data-field="${fieldName}"]`);

            field.removeClass('is-invalid').addClass('is-valid');
            errorSpan.hide();
        }

        // Eventos para cada campo requerido
        Object.keys(requiredFields).forEach(function (fieldName) {
            const field = $('#' + fieldName);
            let hasInteracted = false;

            // Validar al perder el foco (blur)
            field.on('blur change', function () {
                hasInteracted = true;
                let value;

                if (field.is('select')) {
                    value = field.val();
                } else {
                    value = field.val().trim();
                }

                if (!value) {
                    showError(fieldName, requiredFields[fieldName]);
                } else {
                    hideError(fieldName);
                }
            });

            // Validar mientras cambia (para select)
            if (field.is('select')) {
                field.on('change', function () {
                    const value = field.val();
                    if (hasInteracted || value) {
                        if (!value) {
                            showError(fieldName, requiredFields[fieldName]);
                        } else {
                            hideError(fieldName);
                        }
                    }
                });
            } else {
                // Validar mientras escribe (input) - solo si ya interactuó
                field.on('input', function () {
                    const value = $(this).val().trim();

                    if (hasInteracted || value) {
                        if (!value) {
                            showError(fieldName, requiredFields[fieldName]);
                        } else {
                            hideError(fieldName);
                        }
                    }
                });
            }
        });

        // Validar formulario antes de enviar
        $('form').on('submit', function (e) {
            let hasErrors = false;

            Object.keys(requiredFields).forEach(function (fieldName) {
                const field = $('#' + fieldName);
                let value;

                if (field.is('select')) {
                    value = field.val();
                } else {
                    value = field.val().trim();
                }

                if (!value) {
                    showError(fieldName, requiredFields[fieldName]);
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                e.preventDefault();
                // Mostrar alerta general
                if ($('.alert-danger').length === 0) {
                    $('.card-body').prepend(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                        '<h5><i class="icon fas fa-ban"></i> Por favor, corrige los errores antes de continuar</h5>' +
                        '</div>'
                    );
                }
                // Scroll al primer error
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
            }
        });

        // Auto-fill product unit when product is selected
        $(document).on('change', '.donacion-producto-select', function() {
            const productId = $(this).val();
            const row = $(this).closest('tr');
            const unidadInput = row.find('.donacion-unidad-input');
            
            if (productId && productosUnidades[productId]) {
                unidadInput.val(productosUnidades[productId]);
                unidadInput.attr('readonly', true).css('background-color', '#e9ecef');
            } else {
                unidadInput.val('').attr('readonly', false).css('background-color', '');
            }
        });

        // Cascading dropdowns for donación: Almacen -> Estante -> Espacio
        $(document).on('change', '.donacion-almacen-select', function() {
            const almacenId = $(this).val();
            const rowId = $(this).data('row');
            const estanteSelect = $(`.donacion-estante-select[data-row="${rowId}"]`);
            const espacioSelect = $(`.donacion-espacio-select[data-row="${rowId}"]`);
            
            estanteSelect.html('<option value="">-- Seleccione Almacén --</option>');
            espacioSelect.html('<option value="">-- Seleccione Estante --</option>');
            
            if (almacenId) {
                $.get(`/api/almacenes/${almacenId}/estantes`, function(estantes) {
                    estantes.forEach(function(estante) {
                        estanteSelect.append(`<option value="${estante.id_estante}">${estante.codigo_estante}</option>`);
                    });
                });
            }
        });

        $(document).on('change', '.donacion-estante-select', function() {
            const estanteId = $(this).val();
            const rowId = $(this).data('row');
            const espacioSelect = $(`.donacion-espacio-select[data-row="${rowId}"]`);
            
            espacioSelect.html('<option value="">-- Seleccione Estante --</option>');
            
            if (estanteId) {
                $.get(`/api/estantes/${estanteId}/espacios`, function(espacios) {
                    espacios.forEach(function(espacio) {
                        espacioSelect.append(`<option value="${espacio.id_espacio}">${espacio.codigo_espacio}</option>`);
                    });
                });
            }
        });

        // Add new product row for donación
        $('#add-donacion-row').on('click', function() {
            const newRow = `
                <tr class="detalle-row">
                    <td>
                        <select name="donacion_detalles[${donacionDetalleIndex}][id_producto]" class="form-control form-control-sm donacion-producto-select">
                            <option value="">-- Seleccione --</option>
                            @foreach($productos ?? [] as $pId => $pName)
                                <option value="{{ $pId }}">{{ $pName }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input name="donacion_detalles[${donacionDetalleIndex}][cantidad]" type="number" class="form-control form-control-sm" placeholder="1" min="1" step="1"></td>
                    <td><input name="donacion_detalles[${donacionDetalleIndex}][unidad_medida]" class="form-control form-control-sm donacion-unidad-input" placeholder="Ej: kg"></td>
                    <td>
                        <select class="form-control form-control-sm donacion-almacen-select" data-row="${donacionDetalleIndex}">
                            <option value="">-- Seleccione --</option>
                            @foreach($almacenes ?? [] as $almId => $almName)
                                <option value="{{ $almId }}">{{ $almName }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select class="form-control form-control-sm donacion-estante-select" data-row="${donacionDetalleIndex}">
                            <option value="">-- Seleccione Almacén --</option>
                        </select>
                    </td>
                    <td>
                        <select name="donacion_detalles[${donacionDetalleIndex}][id_espacio]" class="form-control form-control-sm donacion-espacio-select" data-row="${donacionDetalleIndex}">
                            <option value="">-- Seleccione Estante --</option>
                        </select>
                    </td>
                    <td><button class="btn btn-danger btn-sm remove-donacion-row" type="button"><i class="fas fa-trash"></i></button></td>
                </tr>
            `;
            
            $('#detalles-donacion-table tbody').append(newRow);
            donacionDetalleIndex++;
        });

        // Remove product row for donación
        $(document).on('click', '.remove-donacion-row', function() {
            if ($('#detalles-donacion-table tbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                alert('Debe haber al menos un producto.');
            }
        });
    });
</script>
@endpush



