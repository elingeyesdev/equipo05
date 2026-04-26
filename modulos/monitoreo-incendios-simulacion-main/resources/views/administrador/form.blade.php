<div class="row">
    <div class="col-md-12">
        <x-adminlte-input name="name" label="Nombre" 
            placeholder="Nombre completo" 
            value="{{ old('name', $administrador->user->name ?? '') }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-user text-primary"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                <span class="text-danger">*</span> Campo requerido
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <x-adminlte-input name="email" label="Email" type="email"
            placeholder="email@example.com" 
            value="{{ old('email', $administrador->user->email ?? '') }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-envelope text-primary"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                <span class="text-danger">*</span> Campo requerido
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <x-adminlte-input name="password" label="Contraseña" type="password"
            placeholder="********" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lock text-primary"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                @if($administrador->exists)
                    <small class="text-muted">(dejar en blanco para mantener la actual)</small>
                @else
                    <span class="text-danger">*</span> Campo requerido
                @endif
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <x-adminlte-input name="password_confirmation" label="Confirmar Contraseña" type="password"
            placeholder="********" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lock text-primary"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                @if($administrador->exists)
                    <small class="text-muted">(dejar en blanco para mantener la actual)</small>
                @else
                    <span class="text-danger">*</span> Campo requerido
                @endif
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <hr>
    </div>

    <div class="col-md-12">
        <x-adminlte-input name="departamento" label="Departamento" 
            placeholder="Ej: Sistemas, Operaciones, etc." 
            value="{{ old('departamento', $administrador?->departamento) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-building text-primary"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                <span class="text-danger">*</span> Campo requerido
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <x-adminlte-input name="nivel_acceso" label="Nivel de Acceso (1-5)" type="number"
            value="{{ old('nivel_acceso', $administrador?->nivel_acceso ?? 1) }}" 
            min="1" max="5" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-key text-primary"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                <small class="text-muted">1 = Bajo, 5 = Máximo</small> <span class="text-danger">*</span>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" name="activo" class="custom-control-input" id="activo" value="1" {{ old('activo', $administrador?->activo ?? true) ? 'checked' : '' }}>
                <label class="custom-control-label" for="activo">
                    {{ __('Activo') }}
                </label>
            </div>
        </div>
    </div>

    <div class="col-12 mt-3">
        <x-adminlte-button type="submit" label="Guardar" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('administradores.index') }}" class="btn btn-danger "><i class="fas fa-arrow-left"></i> Cancelar</a>
    </div>
</div>
