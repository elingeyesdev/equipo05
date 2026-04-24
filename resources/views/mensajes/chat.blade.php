@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Chat con {{ $usuario->nombre }}</h3>

    <div class="border p-3 mb-3" style="height:400px; overflow-y:auto;">
        @foreach($mensajes as $m)
            <div class="mb-2 {{ $m->remitenteid == Auth::id() ? 'text-end' : 'text-start' }}">
                <span class="badge {{ $m->remitenteid == Auth::id() ? 'bg-primary' : 'bg-secondary' }}">
                    {{ $m->contenido }}
                </span>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('mensajes.enviar', $usuario->usuarioid) }}">
        @csrf
        <textarea name="contenido" class="form-control" required></textarea>
        <button class="btn btn-success mt-2">Enviar</button>
    </form>
</div>
@endsection
