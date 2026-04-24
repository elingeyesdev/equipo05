@extends('layouts.app')

@section('title', 'Nueva campaña')

@section('header')
    <h1>
        <i class="fas fa-bullhorn icon-title"></i>
        Nueva campaña
    </h1>
    <p class="text-muted mb-0">
        Crea una nueva campaña de recaudación.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('campanias.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Columna izquierda --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Título de la campaña</label>
                            <input type="text" name="titulo" class="form-control"
                                   value="{{ old('titulo') }}" placeholder="Ej: Ayuda Comedor Infantil" required>
                            @error('titulo') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="descripcion" rows="5" class="form-control" 
                                      placeholder="Detalles sobre la causa..." required>{{ old('descripcion') }}</textarea>
                            @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Imagen (URL opcional)</label>
                            <input type="text" name="imagenurl" class="form-control"
                                   value="{{ old('imagenurl') }}" placeholder="https://...">
                            @error('imagenurl') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Columna derecha --}}
                    <div class="col-md-6">
                        
                        {{-- CAMPO DE CREADOR AUTOMÁTICO --}}
                        <div class="form-group">
                            <label>Responsable (Tú)</label>
                            <input type="text" class="form-control" 
                                   value="{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}" 
                                   readonly 
                                   style="background-color: #e9ecef;">
                            <small class="text-muted">Se registrará a tu nombre.</small>
                        </div>

                        <div class="form-group">
                            <label>Meta de recaudación (Bs)</label>
                            <input type="number" step="0.01" name="metarecaudacion" class="form-control"
                                   value="{{ old('metarecaudacion') }}" required>
                            @error('metarecaudacion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha de inicio</label>
                            <input type="date" name="fechainicio" class="form-control"
                                   value="{{ old('fechainicio', date('Y-m-d')) }}" required>
                            @error('fechainicio') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha de fin (opcional)</label>
                            <input type="date" name="fechafin" class="form-control"
                                   value="{{ old('fechafin') }}">
                            @error('fechafin') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="activa" value="1"
                                   id="activaCheck" {{ old('activa', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="activaCheck">
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
                        <i class="fas fa-save mr-1"></i> Crear campaña
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection