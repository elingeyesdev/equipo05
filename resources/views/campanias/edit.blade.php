@extends('layouts.app')

@section('title', 'Editar campaña')

@section('header')
    <h1>
        <i class="fas fa-edit icon-title"></i>
        Editar campaña
    </h1>
    <p class="text-muted mb-0">
        Modifica la información de la campaña seleccionada.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('campanias.update', $campania->campaniaid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Columna izquierda --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" name="titulo" class="form-control"
                                   value="{{ old('titulo', $campania->titulo) }}" required>
                            @error('titulo') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="descripcion" rows="5" class="form-control" required>{{ old('descripcion', $campania->descripcion) }}</textarea>
                            @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Imagen (URL opcional)</label>
                            <input type="text" name="imagenurl" class="form-control"
                                   value="{{ old('imagenurl', $campania->imagenurl) }}">
                            @error('imagenurl') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Columna derecha --}}
                    <div class="col-md-6">
                        
                        {{-- SOLUCIÓN AL ERROR: Mostrar creador original sin select --}}
                        <div class="form-group">
                            <label>Creado por</label>
                            <input type="text" class="form-control" 
                                   value="{{ optional($campania->creador)->nombre }} {{ optional($campania->creador)->apellido }}" 
                                   readonly 
                                   style="background-color: #e9ecef;">
                            <small class="text-muted">El creador no se puede cambiar.</small>
                        </div>

                        <div class="form-group">
                            <label>Meta de recaudación (Bs)</label>
                            <input type="number" step="0.01" name="metarecaudacion" class="form-control"
                                   value="{{ old('metarecaudacion', $campania->metarecaudacion) }}" required>
                            @error('metarecaudacion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha de inicio</label>
                            <input type="date" name="fechainicio" class="form-control"
                                   value="{{ old('fechainicio', $campania->fechainicio) }}" required>
                            @error('fechainicio') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha de fin (opcional)</label>
                            <input type="date" name="fechafin" class="form-control"
                                   value="{{ old('fechafin', $campania->fechafin) }}">
                            @error('fechafin') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="activa" value="1"
                                   id="activaCheckEdit" {{ old('activa', $campania->activa) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activaCheckEdit">
                                Campaña activa
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('campanias.index') }}" class="btn btn-secondary">
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