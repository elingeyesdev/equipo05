@extends('layouts.app')

@section('title', 'Nuevo saldo de donación')

@section('header')
    <h1>
        <i class="fas fa-wallet icon-title"></i>
        Nuevo saldo de donación
    </h1>
    <p class="text-muted mb-0">
        Crea manualmente un registro de saldo para una donación.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('saldosdonaciones.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Donación</label>
                    <select name="donacionid" class="form-control" required>
                        <option value="">Seleccione una donación</option>
                        @foreach($donaciones as $d)
                            @php
                                $usuario  = optional($d->usuario);
                                $campania = optional($d->campania);
                            @endphp
                            <option value="{{ $d->donacionid }}" {{ old('donacionid') == $d->donacionid ? 'selected' : '' }}>
                                #{{ $d->donacionid }} — Bs {{ number_format($d->monto,2,',','.') }}
                                ({{ $campania->titulo ?? 'Sin campaña' }})
                                — {{ $usuario->nombre ?? 'Anónimo' }} {{ $usuario->apellido ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('donacionid') <small class="text-danger">{{ $message }}</small> @enderror
                    <small class="form-text text-muted">
                        Cada donación solo puede tener un saldo asociado.
                    </small>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Monto original</label>
                            <input type="number" step="0.01" name="montooriginal" class="form-control"
                                   value="{{ old('montooriginal') }}" required>
                            @error('montooriginal') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Monto utilizado</label>
                            <input type="number" step="0.01" name="montoutilizado" class="form-control"
                                   value="{{ old('montoutilizado', 0) }}">
                            @error('montoutilizado') <small class="text-danger">{{ $message }}</small> @enderror
                            <small class="form-text text-muted">
                                Si no se ha usado nada aún, deja 0.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Saldo disponible</label>
                            <input type="number" step="0.01" name="saldodisponible" class="form-control"
                                   value="{{ old('saldodisponible') }}" required>
                            @error('saldodisponible') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Última actualización</label>
                    <input type="datetime-local" name="ultimaactualizacion" class="form-control"
                           value="{{ old('ultimaactualizacion') }}">
                    @error('ultimaactualizacion') <small class="text-danger">{{ $message }}</small> @enderror
                    <small class="form-text text-muted">
                        Si lo dejas vacío, se usará la fecha y hora actual.
                    </small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('saldosdonaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar saldo
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
