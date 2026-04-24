@extends('layouts.app')

@section('title', 'Nuevo rol')

@section('header')
    <h1>
        <i class="fas fa-id-badge icon-title"></i>
        Crear nuevo rol
    </h1>
    <p class="text-muted mb-0">
        Define un nuevo rol para organizar permisos y responsabilidades.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nombre del rol</label>
                    <input type="text" name="nombre" class="form-control"
                           value="{{ old('nombre') }}" required>
                    @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
                    <small class="form-text text-muted">
                        Ejemplos: Administrador, Donante, Voluntario (Se guardará como 'name' en la BD).
                    </small>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3" class="form-control"
                              placeholder="Describe para qué se usa este rol (opcional)...">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar rol
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection