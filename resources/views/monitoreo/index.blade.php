@extends('layouts.app')

@section('title', 'Monitoreo de incendios')

@section('content')
    <div class="row">
        <h1 style="margin:0;">Monitoreo de incendios (Tiempo real)</h1>
        <span>Total en pantalla: <strong>{{ $incendios->count() }}</strong></span>
    </div>
    <p style="margin-top:.3rem;">Vista de incidentes activos o controlados. Recarga manual para ver cambios recientes.</p>

    <div class="card">
        @if ($incendios->isEmpty())
            <p>No hay incendios en monitoreo.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Estado</th>
                        <th>Nivel de riesgo</th>
                        <th>Ubicacion</th>
                        <th>Inicio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incendios as $incendio)
                        <tr>
                            <td>{{ $incendio->titulo }}</td>
                            <td>{{ ucfirst($incendio->estado) }}</td>
                            <td>{{ ucfirst($incendio->nivel_riesgo) }}</td>
                            <td>{{ $incendio->latitud }}, {{ $incendio->longitud }}</td>
                            <td>{{ $incendio->fecha_inicio?->timezone(config('app.timezone'))->format('d/m/Y H:i') ?? '---' }}</td>
                            <td class="row">
                                <a class="btn btn-light" href="{{ route('incendios.edit', $incendio) }}">Editar</a>
                                <form method="POST" action="{{ route('incendios.destroy', $incendio) }}" onsubmit="return confirm('¿Eliminar incendio?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-gray" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
