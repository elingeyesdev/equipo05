<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="name" class="form-label">Nombre (mostrado)</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user?->name) }}" id="name" placeholder="Nombre">
            {!! $errors->first('name', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="email" class="form-label">Correo electrónico</label>
            <input type="text" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user?->email) }}" id="email" placeholder="correo@ejemplo.com">
            {!! $errors->first('email', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="password" class="form-label">Contraseña @if(!($user?->exists ?? false))<span class="text-danger">*</span>@endif</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="password" autocomplete="new-password">
            {!! $errors->first('password', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
            @if($user?->exists ?? false)
                <small class="text-muted">Dejar vacío para mantener la contraseña actual.</small>
            @endif
        </div>
        <div class="form-group mb-2 mb20">
            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" autocomplete="new-password">
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2 d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
        <a href="{{ route('rescate.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</div>