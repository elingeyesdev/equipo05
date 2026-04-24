@extends('layouts.app')

@section('title', 'Nueva donación')

@section('header')
    <h1>
        <i class="fas fa-hand-holding-heart icon-title"></i>
        Nueva donación
    </h1>
    <p class="text-muted mb-0">
        Registra una nueva donación monetaria o en especie.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('donaciones.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Columna izquierda --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Donante (opcional)</label>
                            <select name="usuarioid" class="form-control">
                                <option value="">Donación anónima</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->usuarioid }}" {{ old('usuarioid') == $u->usuarioid ? 'selected' : '' }}>
                                        {{ $u->nombre }} {{ $u->apellido }} — {{ $u->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('usuarioid') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="form-text text-muted">
                                Si lo dejas vacío, la donación se registrará como anónima.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Campaña</label>
                            <select name="campaniaid" class="form-control" required>
                                <option value="">Seleccione una campaña</option>
                                @foreach($campanias as $c)
                                    <option value="{{ $c->campaniaid }}" {{ old('campaniaid') == $c->campaniaid ? 'selected' : '' }}>
                                        {{ $c->titulo }} (Meta: Bs {{ number_format($c->metarecaudacion,2,',','.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('campaniaid') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Monto</label>
                            <input type="number" step="0.01" name="monto" class="form-control"
                                   value="{{ old('monto') }}" required>
                            @error('monto') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Columna derecha --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de donación</label>
                            <select name="tipodonacion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Monetaria" {{ old('tipodonacion') == 'Monetaria' ? 'selected' : '' }}>Monetaria</option>
                                <option value="Especie" {{ old('tipodonacion') == 'Especie' ? 'selected' : '' }}>En especie</option>
                            </select>
                            @error('tipodonacion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estadoid" class="form-control" required>
                                @foreach($estados as $e)
                                    <option value="{{ $e->estadoid }}" {{ old('estadoid') == $e->estadoid ? 'selected' : '' }}>
                                        {{ $e->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estadoid') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha de donación</label>
                            <input type="datetime-local" name="fechadonacion" class="form-control"
                                   value="{{ old('fechadonacion') }}">
                            @error('fechadonacion') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="form-text text-muted">
                                Si lo dejas vacío, se usará la fecha y hora actual.
                            </small>
                        </div>

                        <div class="form-group form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="esanonima" value="1"
                                   id="esanonimaCheck" {{ old('esanonima') ? 'checked' : '' }}>
                            <label class="form-check-label" for="esanonimaCheck">
                                Registrar como donación anónima
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descripción / Detalle</label>
                    <textarea name="descripcion" rows="4" class="form-control"
                              placeholder="Descripción de la donación (opcional)...">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('donaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar donación
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
