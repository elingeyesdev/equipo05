@extends('layouts.app')

@section('title', 'Centro de mensajes - Usuario')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <h1>
            <i class="fas fa-comments icon-title"></i>
            Centro de mensajes
        </h1>
        <p class="text-muted mb-0">
            Conversaciones y donaciones relacionadas con el usuario seleccionado.
        </p>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        {{-- Mensajes flash si los hubiera --}}
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="chat-container">

            {{-- LADO IZQUIERDO: info usuario + donaciones --}}
            <div class="chat-sidebar">

                {{-- Datos del usuario --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="mr-3">
                                <i class="fas fa-user-circle fa-3x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">
                                    {{ $usuario->nombre }} {{ $usuario->apellido }}
                                </h5>
                                <small class="text-muted">{{ $usuario->email }}</small>
                            </div>
                        </div>
                        <p class="mb-1">
                            <strong>Teléfono:</strong>
                            {{ $usuario->telefono ?? '—' }}
                        </p>
                        <p class="mb-0">
                            <strong>Registrado:</strong>
                            {{ $usuario->fecharegistro ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Resumen de donaciones --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-hand-holding-heart mr-1"></i>
                            Donaciones
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Campaña</th>
                                    <th class="text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($donaciones as $donacion)
                                    <tr>
                                        <td>
                                            {{ optional($donacion->campania)->titulo ?? '—' }}<br>
                                            <small class="text-muted">
                                                {{ $donacion->fechadonacion ?? '—' }}
                                            </small>
                                        </td>
                                        <td class="text-right">
                                            <span class="amount-positive">
                                                {{ number_format($donacion->monto, 2) }} Bs
                                            </span><br>
                                            <small class="text-muted">
                                                {{ optional($donacion->estado)->nombre ?? '—' }}
                                            </small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            Sin donaciones registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="{{ route('mensajes.centroSeleccion') }}" class="btn btn-secondary btn-block mt-3">
                    <i class="fas fa-arrow-left mr-1"></i> Cambiar usuario
                </a>
            </div>

            {{-- LADO DERECHO: chat --}}
            <div class="chat-main">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-comments mr-1"></i>
                            Conversaciones
                        </h3>
                        <small class="text-muted">
                            Mensajes entre <strong>{{ $usuario->nombre }}</strong> y el sistema / administradores.
                        </small>
                    </div>
                    <div class="card-body chat-window">

                        @forelse ($mensajes as $mensaje)
                            @php
                                $esDelUsuario = $mensaje->remitenteid == $usuario->usuarioid;
                                $resps = $respuestas[$mensaje->mensajeid] ?? collect();
                            @endphp

                            <div class="chat-message-group">

                                {{-- Mensaje principal --}}
                                <div class="chat-label">
                                    Mensaje #{{ $mensaje->mensajeid }}
                                </div>

                                <div class="d-flex {{ $esDelUsuario ? '' : 'justify-content-end' }}">
                                    <div class="chat-bubble {{ $esDelUsuario ? 'chat-bubble-left' : 'chat-bubble-right' }}">
                                        <div class="chat-asunto">
                                            {{ $mensaje->asunto }}
                                        </div>
                                        <div>
                                            {{ $mensaje->contenido }}
                                        </div>
                                        <div class="chat-meta">
                                            <span>
                                                De:
                                                {{ $mensaje->remitente_nombre ?? '—' }}
                                            </span>
                                            <span>
                                                Para:
                                                {{ $mensaje->destinatario_nombre ?? '—' }}
                                            </span>
                                            <span>
                                                {{ $mensaje->fechaenvio ?? '—' }}
                                            </span>
                                            <span>
                                                Leído: {{ $mensaje->leido ? 'Sí' : 'No' }}
                                            </span>
                                            <span>
                                                Respondido: {{ $mensaje->respondido ? 'Sí' : 'No' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Acciones mensaje --}}
                                <div class="mt-1 mb-2 text-right">
                                    <a href="{{ route('mensajes.show', $mensaje->mensajeid) }}"
                                       class="btn btn-xs btn-outline-secondary">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('mensajes.edit', $mensaje->mensajeid) }}"
                                       class="btn btn-xs btn-outline-primary">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('mensajes.destroy', $mensaje->mensajeid) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('¿Eliminar este mensaje y sus respuestas asociadas?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-outline-danger">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </div>

                                {{-- Respuestas --}}
                                @if ($resps->count())
                                    @foreach ($resps as $resp)
                                        @php
                                            $respEsUsuario = $resp->usuarioid == $usuario->usuarioid;
                                        @endphp
                                        <div class="d-flex {{ $respEsUsuario ? '' : 'justify-content-end' }}">
                                            <div class="chat-bubble {{ $respEsUsuario ? 'chat-bubble-left' : 'chat-bubble-right' }}">
                                                <div>
                                                    {{ $resp->contenido }}
                                                </div>
                                                <div class="chat-meta">
                                                    <span>{{ $resp->usuario_nombre ?? 'Usuario' }}</span>
                                                    <span>{{ $resp->fecharespuesta ?? '—' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                {{-- Separador visual --}}
                                <hr>
                            </div>
                        @empty
                            <div class="chat-empty">
                                <i class="fas fa-comment-slash fa-2x mb-2 d-block"></i>
                                Este usuario todavía no tiene mensajes registrados.
                            </div>
                        @endforelse

                    </div>

                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('mensajes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> Nuevo mensaje
                        </a>
                        <a href="{{ route('respuestasmensajes.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-reply mr-1"></i> Nueva respuesta
                        </a>
                    </div>
                </div>
            </div>

        </div> {{-- /.chat-container --}}
    </div>
</section>
@endsection
