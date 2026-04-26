<div class="row">
    <div class="col-md-6">
        <x-adminlte-input name="name" label="Nombre" placeholder="Nombre completo" 
            value="{{ old('name', $user?->name) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-user text-primary"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-6">
        <x-adminlte-input name="email" label="Correo Electrónico" placeholder="correo@ejemplo.com" 
            type="email" value="{{ old('email', $user?->email) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-envelope text-info"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-6">
        <x-adminlte-input name="telefono" label="Teléfono" placeholder="(+591) 70000000" 
            value="{{ old('telefono', $user?->telefono) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-phone text-success"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-6">
        <x-adminlte-input name="cedula_identidad" label="Cédula de Identidad" placeholder="C.I." 
            value="{{ old('cedula_identidad', $user?->cedula_identidad) }}" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-id-card text-warning"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-6">
        <x-adminlte-input name="password" label="Contraseña" type="password" 
            placeholder="{{ $user && $user->exists ? 'Dejar en blanco para mantener la actual' : 'Contraseña' }}" 
            enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lock text-danger"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-md-6">
        <x-adminlte-input name="password_confirmation" label="Confirmar Contraseña" 
            type="password" placeholder="Confirmar Contraseña" enable-old-support>
            <x-slot name="prependSlot">
                <div class="input-group-text">
                    <i class="fas fa-lock text-danger"></i>
                </div>
            </x-slot>
        </x-adminlte-input>
    </div>

    <div class="col-12 mt-3">
        <x-adminlte-button type="submit" label="Guardar" theme="primary" icon="fas fa-save"/>
        <x-adminlte-button label="Cancelar" theme="danger" icon="fas fa-times" 
            onclick="window.history.back();"/>
    </div>
</div>
