@extends('layouts.app')

@section('title', 'Seleccionar donante')

@section('header')
    <h1>
        <i class="fas fa-file-invoice-dollar icon-title"></i>
        Estado de cuenta por usuario
    </h1>
    <p class="text-muted mb-0">
        Selecciona el donante para ver el detalle de sus donaciones, asignaciones y saldos.
    </p>
@endsection

@section('content')
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle mr-1"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('usuarios.estadoCuentaMostrar') }}" method="GET">
                <div class="form-group">
                    <label for="usuarioid">Usuario</label>
                    <select name="usuarioid" id="usuarioid" class="form-control" required>
                        <option value="">-- Seleccionar usuario --</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->usuarioid }}">
                                {{ $usuario->nombre }} {{ $usuario->apellido }} ({{ $usuario->email }})
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        Se mostrará el estado de cuenta con todas sus donaciones y movimientos.
                    </small>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Volver a usuarios
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Ver estado de cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
