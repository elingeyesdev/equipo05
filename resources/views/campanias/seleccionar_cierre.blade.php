@extends('layouts.app')

@section('title', 'Cierre de campaña')

@section('content')
<div class="container mt-4">
    <h2>Cierre y liquidación de campaña</h2>
    <p class="text-muted">Selecciona la campaña que deseas revisar y cerrar.</p>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('campanias.cierreMostrar') }}" method="GET">
                <div class="form-group">
                    <label for="campaniaid">Campaña</label>
                    <select name="campaniaid" id="campaniaid" class="form-control" required>
                        <option value="">-- Seleccionar campaña --</option>
                        @foreach ($campanias as $campania)
                            <option value="{{ $campania->campaniaid }}">
                                {{ $campania->titulo }}
                                @if (! $campania->activa)
                                    (CERRADA)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    Ver resumen de campaña
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
