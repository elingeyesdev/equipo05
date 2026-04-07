@extends('layouts.app')

@section('title', 'Historial de incendios')

@section('content')
    <div class="row">
        <h1 style="margin:0;">Historial de cambios de incendios</h1>
        <a class="btn btn-light" href="{{ route('monitoreo.index') }}">Volver al monitoreo</a>
    </div>

    <div class="card">
        <form method="GET" action="{{ route('historial.index') }}" class="row">
            <div style="min-width:220px;flex:1;">
                <label for="incendio_id">Incendio</label>
                <select id="incendio_id" name="incendio_id">
                    <option value="">Todos</option>
                    @foreach($incendios as $incendio)
                        <option value="{{ $incendio->id }}" @selected((int)$incendioId === $incendio->id)>{{ $incendio->titulo }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:180px;flex:1;">
                <label for="estado_nuevo">Estado nuevo</label>
                <select id="estado_nuevo" name="estado_nuevo">
                    <option value="">Todos</option>
                    @foreach(['activo', 'controlado', 'extinguido'] as $estado)
                        <option value="{{ $estado }}" @selected($estadoNuevo === $estado)>{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="align-self:end;">
                <button class="btn" type="submit">Filtrar</button>
                <a class="btn btn-light" href="{{ route('historial.index') }}">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card">
        @if($historial->isEmpty())
            <p>No hay registros de historial.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Fecha de cambio</th>
                        <th>Incendio</th>
                        <th>Estado anterior</th>
                        <th>Estado nuevo</th>
                        <th>Descripcion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($historial as $registro)
                        <tr>
                            <td>{{ $registro->fecha_cambio?->format('d/m/Y H:i') ?? '---' }}</td>
                            <td>{{ $registro->incendio?->titulo ?? 'Sin incendio' }}</td>
                            <td>{{ $registro->estado_anterior ?? '---' }}</td>
                            <td>{{ $registro->estado_nuevo ?? '---' }}</td>
                            <td>{{ $registro->descripcion ?? 'Sin descripcion' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
