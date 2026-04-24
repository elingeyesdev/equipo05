@extends('layouts.app')

@section('title', 'Editar usuario')

@section('header')
    <h1>
        <i class="fas fa-user-edit icon-title"></i>
        Editar usuario
    </h1>
    <p class="text-muted mb-0">
        Modifica los datos y roles del usuario seleccionado.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('usuarios.update', $usuario->usuarioid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Columna izquierda --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $usuario->email) }}" required>
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="text" name="contrasena" class="form-control"
                                   value="{{ old('contrasena') }}" placeholder="Dejar en blanco para mantener la actual">
                            @error('contrasena') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="form-text text-muted">
                                Solo escribe aquí si deseas cambiar la contraseña.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control"
                                   value="{{ old('nombre', $usuario->nombre) }}" required>
                            @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Apellido</label>
                            <input type="text" name="apellido" class="form-control"
                                   value="{{ old('apellido', $usuario->apellido) }}" required>
                            @error('apellido') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Teléfono (opcional)</label>
                            <input type="text" name="telefono" class="form-control"
                                   value="{{ old('telefono', $usuario->telefono) }}">
                            @error('telefono') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Columna derecha --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>URL de imagen (opcional)</label>
                            <input type="text" name="imagenurl" class="form-control"
                                   value="{{ old('imagenurl', $usuario->imagenurl) }}">
                            @error('imagenurl') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha de registro (opcional)</label>
                            <input type="datetime-local" name="fecharegistro" class="form-control"
                                   value="{{ old('fecharegistro', $usuario->fecharegistro ? \Carbon\Carbon::parse($usuario->fecharegistro)->format('Y-m-d\TH:i') : '') }}">
                            @error('fecharegistro') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group form-check mt-2">
                            <input type="hidden" name="activo" value="0">
                            <input class="form-check-input" type="checkbox" name="activo" value="1"
                                   id="activoEditCheck" {{ old('activo', $usuario->activo) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activoEditCheck">
                                Usuario activo
                            </label>
                        </div>

                        <div class="form-group mt-3">
                            <label>Roles</label>
                            <div class="border rounded p-2" style="max-height: 180px; overflow-y: auto;">
                                @foreach($roles as $rolName => $rolLabel)
                                    @php
                                        // $userRoles viene del controlador como un array ['admin' => 'admin']
                                        $checked = in_array($rolName, old('roles', array_keys($userRoles ?? [])));
                                    @endphp
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="roles[]"
                                               value="{{ $rolName }}"
                                               id="rol_{{ $rolName }}"
                                               {{ $checked ? 'checked' : '' }}>
                                        <label class="form-check-label text-capitalize" for="rol_{{ $rolName }}">
                                            {{ $rolLabel }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection