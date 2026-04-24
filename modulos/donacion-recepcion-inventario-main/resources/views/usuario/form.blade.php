<div class="row padding-1 p-1">
    <div class="col-md-12">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> {{ __('¡Error de validación!') }}</h5>
                <p>{{ __('Por favor, corrige los siguientes errores antes de continuar:') }}</p>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-triangle mr-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(isset($usuario) && $usuario->id_usuario)
        <div class="form-group mb-2 mb20">
            <label for="id_usuario" class="form-label">{{ __('Id Usuario') }}</label>
            <input type="text" name="id_usuario" class="form-control" value="{{ old('id_usuario', $usuario->id_usuario) }}" id="id_usuario" readonly>
        </div>
        @endif
        
        {{-- Campo CI primero para búsqueda automática --}}
        <div class="form-group mb-2 mb20">
            <label for="ci" class="form-label">{{ __('Carnet de Identidad (CI)') }} <span class="text-danger">*</span></label>
            <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $usuario?->ci) }}" id="ci" placeholder="Ingrese el CI para buscar datos" required maxlength="20">
            @error('ci')
                <span class="invalid-feedback d-block error-message" role="alert" data-field="ci">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <span class="invalid-feedback d-block error-message" role="alert" data-field="ci" style="display: none;">
                <strong><span class="error-text"></span></strong>
            </span>
            <small class="text-muted" id="ci-help-text">Al ingresar el CI se buscarán datos automáticamente en el sistema</small>
            <small class="text-info" id="ci-loading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i> Buscando datos...
            </small>
        </div>

        {{-- Alerta para mostrar datos encontrados --}}
        <div id="ci-lookup-alert" class="alert alert-info alert-dismissible fade show" style="display: none;" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="fas fa-info-circle mr-2"></i>
            <span id="ci-lookup-message"></span>
        </div>

        <div class="form-group mb-2 mb20">
            <label for="nombres" class="form-label">{{ __('Nombres') }} <span class="text-danger">*</span></label>
            <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror" value="{{ old('nombres', $usuario?->nombres) }}" id="nombres" placeholder="Nombres" required maxlength="100">
            @error('nombres')
                <span class="invalid-feedback d-block error-message" role="alert" data-field="nombres">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <span class="invalid-feedback d-block error-message" role="alert" data-field="nombres" style="display: none;">
                <strong><span class="error-text"></span></strong>
            </span>
        </div>
        <div class="form-group mb-2 mb20">
            <label for="apellidos" class="form-label">{{ __('Apellidos') }} <span class="text-danger">*</span></label>
            <input type="text" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror" value="{{ old('apellidos', $usuario?->apellidos) }}" id="apellidos" placeholder="Apellidos" required maxlength="150">
            @error('apellidos')
                <span class="invalid-feedback d-block error-message" role="alert" data-field="apellidos">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <span class="invalid-feedback d-block error-message" role="alert" data-field="apellidos" style="display: none;">
                <strong><span class="error-text"></span></strong>
            </span>
        </div>
        <div class="form-group mb-2 mb20" style="display: none;">
            <label for="foto_ci" class="form-label">{{ __('Foto Ci') }}</label>
            <input type="text" name="foto_ci" class="form-control @error('foto_ci') is-invalid @enderror" value="{{ old('foto_ci', $usuario?->foto_ci) }}" id="foto_ci" placeholder="Foto Ci">
            @error('foto_ci')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20">
            <div class="form-check">
                <input type="checkbox" name="is_recolector" class="form-check-input" id="is_recolector" value="1" {{ old('is_recolector', $usuario?->is_recolector) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_recolector">
                    {{ __('¿Es recolector?') }}
                </label>
            </div>
            <small class="text-muted">Si es recolector, la licencia de conducir será obligatoria</small>
        </div>
        <div class="form-group mb-2 mb20" id="licencia-group">
            <label for="licencia_conducir" class="form-label">
                {{ __('Licencia Conducir') }} 
                <span class="text-danger licencia-required" style="display: none;">*</span>
            </label>
            <input type="text" name="licencia_conducir" class="form-control @error('licencia_conducir') is-invalid @enderror" value="{{ old('licencia_conducir', $usuario?->licencia_conducir) }}" id="licencia_conducir" placeholder="Licencia Conducir" maxlength="50">
            @error('licencia_conducir')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20" style="display: none;">
            <label for="foto_licencia" class="form-label">{{ __('Foto Licencia') }}</label>
            <input type="text" name="foto_licencia" class="form-control @error('foto_licencia') is-invalid @enderror" value="{{ old('foto_licencia', $usuario?->foto_licencia) }}" id="foto_licencia" placeholder="Foto Licencia">
            @error('foto_licencia')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20">
            <label for="genero" class="form-label">{{ __('Genero') }}</label>
            <select name="genero" class="form-control @error('genero') is-invalid @enderror" id="genero">
                <option value="">Seleccione un género</option>
                <option value="Masculino" {{ old('genero', $usuario?->genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="Femenino" {{ old('genero', $usuario?->genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                <option value="Otro" {{ old('genero', $usuario?->genero) == 'Otro' ? 'selected' : '' }}>Otro</option>
            </select>
            @error('genero')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20">
            <label for="correo" class="form-label">{{ __('Correo') }} <span class="text-danger">*</span></label>
            <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $usuario?->correo) }}" id="correo" placeholder="Correo" required maxlength="100">
            @error('correo')
                <span class="invalid-feedback d-block error-message" role="alert" data-field="correo">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <span class="invalid-feedback d-block error-message" role="alert" data-field="correo" style="display: none;">
                <strong><span class="error-text"></span></strong>
            </span>
        </div>
        <div class="form-group mb-2 mb20">
            <label for="telefono" class="form-label">{{ __('Telefono') }}</label>
            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $usuario?->telefono) }}" id="telefono" placeholder="Telefono" maxlength="20">
            @error('telefono')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20">
            <label for="direccion_domicilio" class="form-label">{{ __('Direccion Domicilio') }}</label>
            <input type="text" name="direccion_domicilio" class="form-control @error('direccion_domicilio') is-invalid @enderror" value="{{ old('direccion_domicilio', $usuario?->direccion_domicilio) }}" id="direccion_domicilio" placeholder="Direccion Domicilio">
            @error('direccion_domicilio')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20">
            <label for="contrasena" class="form-label">
                {{ __('Contraseña') }} 
                @if(!isset($usuario) || !$usuario->id_usuario)
                    <span class="text-danger">*</span>
                @else
                    <small class="text-muted">(dejar en blanco para no cambiar)</small>
                @endif
            </label>
            <input type="password" name="contrasena" class="form-control @error('contrasena') is-invalid @enderror" value="{{ old('contrasena') }}" id="contrasena" placeholder="Contraseña" {{ (!isset($usuario) || !$usuario->id_usuario) ? 'required' : '' }}>
            @error('contrasena')
                <span class="invalid-feedback d-block error-message" role="alert" data-field="contrasena">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <span class="invalid-feedback d-block error-message" role="alert" data-field="contrasena" style="display: none;">
                <strong><span class="error-text"></span></strong>
            </span>
        </div>
        <div class="form-group mb-2 mb20">
            <label for="estado" class="form-label">{{ __('Estado') }}</label>
            <select name="estado" class="form-control @error('estado') is-invalid @enderror" id="estado">
                <option value="">Seleccione un estado</option>
                <option value="Activo" {{ old('estado', $usuario?->estado ?? 'Activo') == 'Activo' ? 'selected' : '' }}>Activo</option>
                <option value="Inactivo" {{ old('estado', $usuario?->estado ?? 'Activo') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20" style="display: none;">
            <label for="entidad_pertenencia" class="form-label">{{ __('Entidad Pertenencia') }}</label>
            <input type="text" name="entidad_pertenencia" class="form-control @error('entidad_pertenencia') is-invalid @enderror" value="{{ old('entidad_pertenencia', $usuario?->entidad_pertenencia) }}" id="entidad_pertenencia" placeholder="Entidad Pertenencia" maxlength="150">
            @error('entidad_pertenencia')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20" style="display: none;">
            <label for="tipo_sangre" class="form-label">{{ __('Tipo Sangre') }}</label>
            <input type="text" name="tipo_sangre" class="form-control @error('tipo_sangre') is-invalid @enderror" value="{{ old('tipo_sangre', $usuario?->tipo_sangre) }}" id="tipo_sangre" placeholder="Tipo Sangre" maxlength="5">
            @error('tipo_sangre')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group mb-2 mb20">
            <label for="rol" class="form-label">{{ __('Rol') }}</label>
            <select name="rol" class="form-control @error('rol') is-invalid @enderror" id="rol">
                <option value="">Seleccione un rol</option>
                @foreach($roles ?? [] as $role)
                    <option value="{{ $role->name }}" {{ old('rol', $usuarioRol ?? null) == $role->name ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('rol')
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        @if(isset($usuario) && $usuario->fecha_registro)
        <div class="form-group mb-2 mb20">
            <label for="fecha_registro" class="form-label">{{ __('Fecha Registro') }}</label>
            <input type="text" name="fecha_registro" class="form-control" value="{{ old('fecha_registro', $usuario->fecha_registro) }}" id="fecha_registro" readonly>
        </div>
        @endif

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>

<script>
// ===== Búsqueda automática por CI (JavaScript Vanilla) =====
(function() {
    console.log('Script de usuario cargado');
    
    const apiBaseUrl = @json(env('API_BASE_URL_ADS', ''));
    const ciInput = document.getElementById('ci');
    const nombresInput = document.getElementById('nombres');
    const apellidosInput = document.getElementById('apellidos');
    const telefonoInput = document.getElementById('telefono');
    const ciLoadingSpinner = document.getElementById('ci-loading');
    const ciHelpText = document.getElementById('ci-help-text');
    const ciLookupAlert = document.getElementById('ci-lookup-alert');
    const ciLookupMessage = document.getElementById('ci-lookup-message');

    console.log('API Base URL:', apiBaseUrl);
    console.log('CI Input encontrado:', !!ciInput);

    let lastLookupCi = null;
    let isFetching = false;

    if (ciInput && apiBaseUrl) {
        console.log('Agregando evento blur al campo CI');
        
        ciInput.addEventListener('blur', async function() {
            console.log('Evento blur disparado');
            const ci = (ciInput.value || '').trim();
            console.log('CI ingresado:', ci);

            // Solo buscar si el CI tiene al menos 5 caracteres y no se ha buscado antes
            if (ci.length < 5) {
                console.log('CI muy corto, se requieren al menos 5 caracteres');
                return;
            }
            
            if (ci === lastLookupCi) {
                console.log('CI ya fue buscado anteriormente');
                return;
            }
            
            if (isFetching) {
                console.log('Ya hay una búsqueda en progreso');
                return;
            }

            lastLookupCi = ci;
            isFetching = true;

            // Mostrar spinner de carga
            console.log('Mostrando spinner');
            if (ciLoadingSpinner) {
                ciLoadingSpinner.style.display = 'block';
            }
            if (ciHelpText) {
                ciHelpText.style.display = 'none';
            }

            try {
                const url = `${apiBaseUrl}/api/gateway/registro/ci/${encodeURIComponent(ci)}`;
                console.log('Llamando a URL:', url);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Client-System': 'donaciones',
                    },
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    console.warn('Gateway lookup failed with status', response.status);
                    return;
                }

                const json = await response.json();
                console.log('Respuesta JSON:', json);

                if (!json.success || !json.found) {
                    console.log('No se encontraron datos');
                    // Ocultar alerta si no se encontraron datos
                    if (ciLookupAlert) {
                        ciLookupAlert.style.display = 'none';
                    }
                    return;
                }

                // Obtener datos (pueden venir directamente o dentro de data)
                const nombre = json.nombre || (json.data && json.data.nombre) || '';
                const apellido = json.apellido || (json.data && json.data.apellido) || '';
                const telefono = json.telefono || (json.data && json.data.telefono) || '';
                const sistema = json.system || 'el sistema';

                console.log('Datos encontrados:', { nombre, apellido, telefono, sistema });

                // Mostrar mensaje de éxito
                if (ciLookupAlert && ciLookupMessage) {
                    ciLookupMessage.textContent = `¡Datos encontrados del sistema "${sistema}"! Los campos han sido autocompletados.`;
                    ciLookupAlert.style.display = 'block';
                    ciLookupAlert.className = 'alert alert-success alert-dismissible fade show';
                }

                // Rellenar campos solo si están vacíos
                if (nombresInput && !nombresInput.value.trim() && nombre) {
                    console.log('Llenando campo nombres');
                    nombresInput.value = nombre;
                    // Disparar evento input para validación
                    const event = new Event('input', { bubbles: true });
                    nombresInput.dispatchEvent(event);
                }
                if (apellidosInput && !apellidosInput.value.trim() && apellido) {
                    console.log('Llenando campo apellidos');
                    apellidosInput.value = apellido;
                    const event = new Event('input', { bubbles: true });
                    apellidosInput.dispatchEvent(event);
                }
                if (telefonoInput && !telefonoInput.value.trim() && telefono) {
                    console.log('Llenando campo telefono');
                    telefonoInput.value = telefono;
                }

            } catch (error) {
                console.error('Error llamando al gateway para autocompletar', error);
                // Mostrar error discreto
                if (ciLookupAlert && ciLookupMessage) {
                    ciLookupMessage.textContent = 'No se pudo conectar con el servicio de búsqueda. Por favor complete los datos manualmente.';
                    ciLookupAlert.style.display = 'block';
                    ciLookupAlert.className = 'alert alert-warning alert-dismissible fade show';
                }
            } finally {
                isFetching = false;
                console.log('Ocultando spinner');
                // Ocultar spinner
                if (ciLoadingSpinner) {
                    ciLoadingSpinner.style.display = 'none';
                }
                if (ciHelpText) {
                    ciHelpText.style.display = 'block';
                }
            }
        });
    } else {
        if (!ciInput) {
            console.warn('No se encontró el campo CI');
        }
        if (!apiBaseUrl) {
            console.warn('No se encontró API_BASE_URL_ADS en .env');
        }
    }
})();

// ===== Resto del código con jQuery =====
$(document).ready(function() {
    // Manejo del checkbox is_recolector
    function toggleLicenciaRequired() {
        const isRecolector = $('#is_recolector').is(':checked');
        const licenciaInput = $('#licencia_conducir');
        const requiredMark = $('.licencia-required');
        
        if (isRecolector) {
            licenciaInput.attr('required', 'required');
            requiredMark.show();
            $('#licencia-group').addClass('required-field');
        } else {
            licenciaInput.removeAttr('required');
            requiredMark.hide();
            $('#licencia-group').removeClass('required-field');
        }
    }
    
    // Ejecutar al cargar la página
    toggleLicenciaRequired();
    
    // Ejecutar al cambiar el checkbox
    $('#is_recolector').on('change', function() {
        toggleLicenciaRequired();
    });

    // Validación en tiempo real para campos requeridos
    const requiredFields = {
        'nombres': {
            required: 'El campo nombres es obligatorio',
            maxlength: 'El campo nombres no puede exceder 100 caracteres'
        },
        'apellidos': {
            required: 'El campo apellidos es obligatorio',
            maxlength: 'El campo apellidos no puede exceder 150 caracteres'
        },
        'ci': {
            required: 'El campo CI es obligatorio',
            maxlength: 'El campo CI no puede exceder 20 caracteres'
        },
        'correo': {
            required: 'El campo correo es obligatorio',
            email: 'Por favor ingrese un correo electrónico válido',
            maxlength: 'El campo correo no puede exceder 100 caracteres'
        },
        'contrasena': 'El campo contraseña es obligatorio'
    };

    // Campos con longitud máxima
    const maxLengthFields = {
        'licencia_conducir': 50,
        'telefono': 20,
        'entidad_pertenencia': 150,
        'tipo_sangre': 5
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

    // Validar email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Eventos para cada campo
    Object.keys(requiredFields).forEach(function(fieldName) {
        const field = $('#' + fieldName);
        let hasInteracted = false;
        
        // Validar al perder el foco (blur)
        field.on('blur', function() {
            hasInteracted = true;
            const value = $(this).val().trim();
            const maxLength = field.attr('maxlength');
            
            if (!value) {
                const message = typeof requiredFields[fieldName] === 'object' 
                    ? requiredFields[fieldName].required 
                    : requiredFields[fieldName];
                showError(fieldName, message);
            } else if (fieldName === 'correo' && !validateEmail(value)) {
                showError(fieldName, requiredFields[fieldName].email);
            } else if (maxLength && value.length > parseInt(maxLength)) {
                const message = typeof requiredFields[fieldName] === 'object' && requiredFields[fieldName].maxlength
                    ? requiredFields[fieldName].maxlength
                    : `El campo no puede exceder ${maxLength} caracteres`;
                showError(fieldName, message);
            } else {
                hideError(fieldName);
            }
        });

        // Validar mientras escribe (input) - solo si ya interactuó
        field.on('input', function() {
            const value = $(this).val().trim();
            const maxLength = field.attr('maxlength');
            
            if (hasInteracted || value) {
                if (!value) {
                    const message = typeof requiredFields[fieldName] === 'object' 
                        ? requiredFields[fieldName].required 
                        : requiredFields[fieldName];
                    showError(fieldName, message);
                } else if (fieldName === 'correo' && !validateEmail(value)) {
                    showError(fieldName, requiredFields[fieldName].email);
                } else if (maxLength && value.length > parseInt(maxLength)) {
                    const message = typeof requiredFields[fieldName] === 'object' && requiredFields[fieldName].maxlength
                        ? requiredFields[fieldName].maxlength
                        : `El campo no puede exceder ${maxLength} caracteres`;
                    showError(fieldName, message);
                } else {
                    hideError(fieldName);
                }
            }
        });
    });

    // Validar campos con longitud máxima
    Object.keys(maxLengthFields).forEach(function(fieldName) {
        const field = $('#' + fieldName);
        const maxLength = maxLengthFields[fieldName];
        
        field.on('input', function() {
            const value = $(this).val();
            if (value.length > maxLength) {
                field.addClass('is-invalid').removeClass('is-valid');
                if (!field.next('.invalid-feedback').length) {
                    field.after(`<span class="invalid-feedback d-block" role="alert"><strong>El campo no puede exceder ${maxLength} caracteres</strong></span>`);
                }
            } else {
                field.removeClass('is-invalid').addClass('is-valid');
                field.next('.invalid-feedback').remove();
            }
        });
    });

    // Validar formulario antes de enviar
    $('form').on('submit', function(e) {
        let hasErrors = false;
        
        Object.keys(requiredFields).forEach(function(fieldName) {
            const field = $('#' + fieldName);
            const value = field.val().trim();
            const maxLength = field.attr('maxlength');
            
            if (!value) {
                const message = typeof requiredFields[fieldName] === 'object' 
                    ? requiredFields[fieldName].required 
                    : requiredFields[fieldName];
                showError(fieldName, message);
                hasErrors = true;
            } else if (fieldName === 'correo' && !validateEmail(value)) {
                showError(fieldName, requiredFields[fieldName].email);
                hasErrors = true;
            } else if (maxLength && value.length > parseInt(maxLength)) {
                const message = typeof requiredFields[fieldName] === 'object' && requiredFields[fieldName].maxlength
                    ? requiredFields[fieldName].maxlength
                    : `El campo no puede exceder ${maxLength} caracteres`;
                showError(fieldName, message);
                hasErrors = true;
            }
        });

        // Validar campos opcionales con longitud máxima
        Object.keys(maxLengthFields).forEach(function(fieldName) {
            const field = $('#' + fieldName);
            const value = field.val();
            const maxLength = maxLengthFields[fieldName];
            
            if (value && value.length > maxLength) {
                field.addClass('is-invalid').removeClass('is-valid');
                if (!field.next('.invalid-feedback').length) {
                    field.after(`<span class="invalid-feedback d-block" role="alert"><strong>El campo no puede exceder ${maxLength} caracteres</strong></span>`);
                }
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
});
</script>




