@extends('layouts.app')

@section('title', 'Centro de mensajes - Seleccionar usuario')

@section('content')
<div class="container mt-4">
    <h2>Centro de mensajes</h2>
    <p class="text-muted">Selecciona el usuario para ver todas sus conversaciones y donaciones relacionadas.</p>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('mensajes.centroUsuario') }}" method="GET">
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
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    Ver centro de mensajes
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
