<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Información del Donante</h3>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Error de validación</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-exclamation-triangle mr-1"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                            value="{{ old('nombre', $donante?->nombre) }}" id="nombre"
                            placeholder="Ingrese el nombre completo" required maxlength="150">
                        @error('nombre')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="tipo">Tipo <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-tags"></i></span>
                        </div>
                        <select name="tipo" class="form-control @error('tipo') is-invalid @enderror" id="tipo" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="persona" {{ old('tipo', $donante?->tipo) == 'persona' ? 'selected' : '' }}>
                                Persona</option>
                            <option value="empresa" {{ old('tipo', $donante?->tipo) == 'empresa' ? 'selected' : '' }}>
                                Empresa</option>
                        </select>
                        @error('tipo')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $donante?->email) }}" id="email" placeholder="ejemplo@correo.com"
                            maxlength="100">
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                            value="{{ old('telefono', $donante?->telefono) }}" id="telefono" placeholder="Ej: 77123456"
                            maxlength="20">
                        @error('telefono')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="password">Contraseña @if(!isset($donante) || !$donante->id_donante)<span
                    class="text-danger">*</span>@endif</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" id="password"
                            placeholder="Mínimo 6 caracteres" minlength="6">
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                    @if(isset($donante) && $donante->id_donante)
                        <small class="form-text text-muted">Dejar en blanco para no cambiar la contraseña</small>
                    @endif
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                        <input type="checkbox" class="custom-control-input" id="cambiar_password" name="cambiar_password" value="1" 
                            {{ old('cambiar_password', $donante?->cambiar_password ?? true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="cambiar_password">
                            <i class="fas fa-key"></i> Requerir cambio de contraseña en próximo inicio de sesión
                        </label>
                    </div>
                    <small class="form-text text-muted">
                        Si está activado, el donante deberá cambiar su contraseña al iniciar sesión en la app móvil
                    </small>
                    <div class="alert alert-warning mt-2" id="password-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Advertencia:</strong> Desactivar esta opción es un riesgo de seguridad. Solo desactívela si es estrictamente necesario.
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                        </div>
                        <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                            id="direccion" rows="3"
                            placeholder="Ingrese la dirección completa">{{ old('direccion', $donante?->direccion) }}</textarea>
                        @error('direccion')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                </div>
            </div>

            @if(isset($donante) && $donante->fecha_registro)
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha_registro">Fecha de Registro</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                            </div>
                            <input type="text" class="form-control"
                                value="{{ \Carbon\Carbon::parse($donante->fecha_registro)->format('d/m/Y H:i') }}" readonly>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Donante</button>
        <a href="{{ route('inventario.donante.index') }}" class="btn btn-secondary float-right"><i class="fas fa-times"></i>
            Cancelar</a>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        // Mostrar/ocultar advertencia al cambiar el estado del checkbox
        $('#cambiar_password').on('change', function() {
            if (!$(this).is(':checked')) {
                $('#password-warning').slideDown();
            } else {
                $('#password-warning').slideUp();
            }
        });

        // Verificar estado inicial
        if (!$('#cambiar_password').is(':checked')) {
            $('#password-warning').show();
        }
    });
</script>
@endpush



