@extends('layouts.app')

@section('title', 'Editar donación')

@section('header')
    <h1>
        <i class="fas fa-hand-holding-heart icon-title"></i>
        Editar donación #{{ $donacion->donacionid }}
    </h1>
    <p class="text-muted mb-0">
        Modifica los datos de la donación registrada.
    </p>
@endsection

@section('content')
    @php
        $fechaValor = old('fechadonacion');
        if ($fechaValor === null && $donacion->fechadonacion) {
            $fechaValor = \Carbon\Carbon::parse($donacion->fechadonacion)->format('Y-m-d\TH:i');
        }
        $tipoValor = old('tipodonacion', $donacion->tipodonacion);
        $esAnonima = old('esanonima', $donacion->esanonima);
    @endphp

    <div class="card">
        <div class="card-body">
            <form action="{{ route('donaciones.update', $donacion->donacionid) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Donante</label>
                            <select name="usuarioid" class="form-control">
                                <option value="">Donación anónima (sin donante)</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->usuarioid }}"
                                        {{ (string) old('usuarioid', $donacion->usuarioid) === (string) $u->usuarioid ? 'selected' : '' }}>
                                        {{ $u->nombre }} {{ $u->apellido }} — {{ $u->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('usuarioid') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="form-text text-muted">
                                Si lo dejas vacío, la donación quedará sin donante asignado.
                            </small>
                        </div>

                        <div class="form-group">
                            <label>Campaña</label>
                            <select name="campaniaid" class="form-control" required>
                                <option value="">Seleccione una campaña</option>
                                @foreach($campanias as $c)
                                    <option value="{{ $c->campaniaid }}"
                                        {{ (string) old('campaniaid', $donacion->campaniaid) === (string) $c->campaniaid ? 'selected' : '' }}>
                                        {{ $c->titulo }} (Meta: Bs {{ number_format($c->metarecaudacion, 2, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('campaniaid') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Monto (Bs)</label>
                            <input type="number" step="0.01" name="monto" class="form-control"
                                   value="{{ old('monto', $donacion->monto) }}" placeholder="0.00" required>
                            @error('monto') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de donación</label>
                            <select name="tipodonacion" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Monetaria" {{ strtolower((string) $tipoValor) === 'monetaria' ? 'selected' : '' }}>Monetaria</option>
                                <option value="Especie" {{ strtolower((string) $tipoValor) === 'especie' ? 'selected' : '' }}>En especie</option>
                            </select>
                            @error('tipodonacion') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estadoid" class="form-control" required>
                                @foreach($estados as $e)
                                    <option value="{{ $e->estadoid }}"
                                        {{ (string) old('estadoid', $donacion->estadoid) === (string) $e->estadoid ? 'selected' : '' }}>
                                        {{ $e->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estadoid') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>Fecha de donación</label>
                            <input type="datetime-local" name="fechadonacion" class="form-control"
                                   value="{{ $fechaValor }}">
                            @error('fechadonacion') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="form-text text-muted">
                                Si lo dejas vacío, se mantendrá la fecha actual del registro.
                            </small>
                        </div>

                        <div class="form-group form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="esanonima" value="1"
                                   id="esanonimaCheck" {{ $esAnonima ? 'checked' : '' }}>
                            <label class="form-check-label" for="esanonimaCheck">
                                Registrar como donación anónima
                            </label>
                            <small class="d-block text-muted">
                                (El donante queda interno; públicamente aparece como Anónimo)
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descripción / Detalle</label>
                    <textarea name="descripcion" rows="4" class="form-control"
                              placeholder="Descripción de la donación (opcional)...">{{ old('descripcion', $donacion->descripcion) }}</textarea>
                    @error('descripcion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('donaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Actualizar donación
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
