@extends('layouts.app')

@section('title', 'Notificaciones de incendios')

@section('content')
    <div class="row">
        <h1 style="margin:0;">Modulo web de notificaciones</h1>
        <form method="POST" action="{{ route('notificaciones.marcar-todas') }}">
            @csrf
            @method('PATCH')
            <button class="btn" type="submit">Marcar todas como leidas</button>
        </form>
    </div>

    <div class="card">
        @if($notificaciones->isEmpty())
            <p>No hay notificaciones registradas.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Incendio</th>
                        <th>Tipo</th>
                        <th>Mensaje</th>
                        <th>Estado</th>
                        <th>Accion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notificaciones as $notificacion)
                        <tr>
                            <td>{{ $notificacion->created_at?->format('d/m/Y H:i') }}</td>
                            <td>{{ $notificacion->incendio?->titulo ?? 'Sin incendio' }}</td>
                            <td>{{ ucfirst($notificacion->tipo) }}</td>
                            <td>{{ $notificacion->mensaje }}</td>
                            <td>
                                @if($notificacion->leido)
                                    <span class="status status-ok">Leida</span>
                                @else
                                    <span class="status status-pendiente">No leida</span>
                                @endif
                            </td>
                            <td>
                                @if(!$notificacion->leido)
                                    <form method="POST" action="{{ route('notificaciones.marcar', $notificacion) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-light" type="submit">Marcar leida</button>
                                    </form>
                                @else
                                    ---
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection