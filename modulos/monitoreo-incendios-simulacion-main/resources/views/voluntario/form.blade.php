<div class="row">
    <div class="col-md-12">
        <x-adminlte-input name="name" label="Nombre" 
            placeholder="Nombre completo" 
            value="{{ old('name', $voluntario->user->name ?? '') }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-user text-teal"></i>
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
            value="{{ old('email', $voluntario->user->email ?? '') }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-envelope text-teal"></i>
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
                    <i class="fas fa-lock text-teal"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                @if($voluntario->exists)
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
                    <i class="fas fa-lock text-teal"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                @if($voluntario->exists)
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
        <x-adminlte-input name="direccion" label="Dirección" 
            placeholder="Calle, número, etc." 
            value="{{ old('direccion', $voluntario?->direccion) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-home text-teal"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                <span class="text-danger">*</span> Campo requerido
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <x-adminlte-input name="ciudad" label="Ciudad" 
            placeholder="Nombre de la ciudad" 
            value="{{ old('ciudad', $voluntario?->ciudad) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-city text-teal"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                <span class="text-danger">*</span> Campo requerido
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <x-adminlte-input name="zona" label="Zona" 
            placeholder="Zona o barrio" 
            value="{{ old('zona', $voluntario?->zona) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-map-marker-alt text-teal"></i>
                </div>
            </x-slot>
            <x-slot name="bottomSlot">
                <span class="text-danger">*</span> Campo requerido
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-12">
        <x-adminlte-textarea name="notas" label="Notas" rows="3"
            placeholder="Información adicional (opcional)" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-sticky-note text-teal"></i>
                </div>
            </x-slot>
            {{ old('notas', $voluntario?->notas) }}
        </x-adminlte-textarea>
    </div>

    <div class="col-12 mt-3">
        <x-adminlte-button type="submit" label="Guardar" theme="primary" icon="fas fa-save"/>
        <a href="{{ route('voluntarios.index') }}" class="btn btn-danger "><i class="fas fa-arrow-left"></i> Cancelar</a>
    </div>
</div>
