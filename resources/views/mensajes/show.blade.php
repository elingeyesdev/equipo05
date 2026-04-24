@extends('layouts.app')

@section('title', 'Detalle del mensaje')

@section('header')
    <h1>
        <i class="fas fa-envelope-open-text icon-title"></i>
        Detalle del mensaje
    </h1>
    <p class="text-muted mb-0">
        Información completa del mensaje y sus respuestas.
    </p>
@endsection

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-1">{{ $mensaje->asunto }}</h5>
            <p class="text-muted mb-3">{{ $mensaje->fechaenvio }}</p>

            <p class="mb-3">{{ $mensaje->contenido }}</p>

            <p class="mb-1">
                <strong>De:</strong>
                {{ optional($mensaje->remitente)->nombre }} {{ optional($mensaje->remitente)->apellido }}
                ({{ optional($mensaje->remitente)->email }})
            </p>
            <p class="mb-1">
                <strong>Para:</strong>
                {{ optional($mensaje->destinatario)->nombre }} {{ optional($mensaje->destinatario)->apellido ?? '—' }}
                ({{ optional($mensaje->destinatario)->email ?? '—' }})
            </p>
            <p class="mb-0">
                <strong>Estado:</strong>
                <span class="badge badge-{{ $mensaje->leido ? 'success' : 'secondary' }}">
                    {{ $mensaje->leido ? 'Leído' : 'No leído' }}
                </span>
                <span class="badge badge-{{ $mensaje->respondido ? 'info' : 'light' }}">
                    {{ $mensaje->respondido ? 'Respondido' : 'Sin respuesta' }}
                </span>
            </p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-reply mr-1"></i> Respuestas
            </h3>
        </div>
        <div class="card-body">
            @forelse($mensaje->respuestas as $r)
                <div class="chat-message-group">
                    <div class="d-flex {{ $r->usuarioid == optional($mensaje->remitente)->usuarioid ? '' : 'justify-content-end' }}">
                        <div class="chat-bubble {{ $r->usuarioid == optional($mensaje->remitente)->usuarioid ? 'chat-bubble-left' : 'chat-bubble-right' }}">
                            <div>{{ $r->contenido }}</div>
                            <div class="chat-meta">
                                <span>{{ optional($r->usuario)->nombre }} {{ optional($r->usuario)->apellido }}</span>
                                <span>{{ $r->fecharespuesta }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted mb-0">Este mensaje aún no tiene respuestas.</p>
            @endforelse
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('respuestasmensajes.create') }}" class="btn btn-outline-primary">
                <i class="fas fa-reply mr-1"></i> Nueva respuesta
            </a>
            <a href="{{ route('mensajes.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver al listado
            </a>
        </div>
    </div>
@endsection
