@extends('layouts.app')

@section('title', 'Asignar donación')

@section('header')
    <h1>
        <i class="fas fa-plus icon-title"></i>
        Nueva asignación de donación
    </h1>
    <p class="text-muted mb-0">
        Registra cuánto de una donación será usada en una asignación.
    </p>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('donacionesasignaciones.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Donación</label>
                    <select name="donacionid" class="form-control" required>
                        <option value="">Seleccione una donación</option>
                        @foreach($donaciones as $d)
                            <option value="{{ $d->donacionid }}">
                                #{{ $d->donacionid }} —
                                Bs {{ number_format($d->monto,2,',','.') }} —
                                {{ optional($d->usuario)->nombre ?? 'Anónima' }}
                            </option>
                        @endforeach
                    </select>
                    @error('donacionid') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Asignación</label>
                    <select name="asignacionid" class="form-control" required>
                        <option value="">Seleccione una asignación</option>
                        @foreach($asignaciones as $a)
                            <option value="{{ $a->asignacionid }}">
                                #{{ $a->asignacionid }} — {{ $a->descripcion }}
                            </option>
                        @endforeach
                    </select>
                    @error('asignacionid') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Monto a asignar</label>
                    <input type="number" step="0.01" name="montoasignado" class="form-control"
                           value="{{ old('montoasignado') }}" required>
                    @error('montoasignado') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Fecha de asignación</label>
                    <input type="datetime-local" name="fechaasignacion" class="form-control"
                           value="{{ old('fechaasignacion') }}">
                    @error('fechaasignacion') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('donacionesasignaciones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Guardar registro
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection
