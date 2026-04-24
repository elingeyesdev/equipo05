@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h3 class="m-0">Chat con {{ $usuario->nombre }}</h3>
        <a href="{{ route('chat.inbox') }}" class="btn btn-secondary btn-sm">Volver</a>
    </div>

    {{-- errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body chat-window" id="chatWindow" style="height:400px; overflow-y:auto;">
            @forelse($mensajes as $m)
                @php
                    // ✅ Con el modelo nuevo: usuarioid = autor del mensaje
                    $esMio = ($m->usuarioid == Auth::id());
                @endphp

                <div class="d-flex mb-2 {{ $esMio ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="chat-bubble {{ $esMio ? 'chat-bubble-right' : 'chat-bubble-left' }}">
                        <div class="chat-asunto">{{ $m->asunto }}</div>
                        <div class="chat-text">{{ $m->contenido }}</div>

                        <div class="chat-meta">
                            {{ optional($m->fechaenvio)->format('H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-muted text-center py-4">No hay mensajes aún.</div>
            @endforelse
        </div>

        <div class="card-footer">
            <form method="POST" action="{{ route('chat.enviar', $usuario->usuarioid) }}">
                @csrf

                <input type="text"
                       name="asunto"
                       class="form-control mb-2"
                       placeholder="Asunto"
                       required
                       maxlength="150"
                       value="{{ old('asunto') }}">

                <textarea name="contenido"
                          class="form-control"
                          required
                          maxlength="5000"
                          rows="2"
                          placeholder="Escribe tu mensaje...">{{ old('contenido') }}</textarea>

                <button class="btn btn-success mt-2">Enviar</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ✅ bajar automáticamente al último mensaje
    (function () {
        const chat = document.getElementById('chatWindow');
        if (chat) chat.scrollTop = chat.scrollHeight;
    })();
</script>
@endpush
