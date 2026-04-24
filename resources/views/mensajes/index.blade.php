@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Bandeja de entrada</h3>

    <ul class="list-group">
        @forelse($mensajes as $m)
            <li class="list-group-item">
                <a href="{{ route('mensajes.chat', $m->remitenteid) }}">
                    <strong>{{ $m->remitente->nombre }}</strong>
                    — {{ Str::limit($m->contenido, 50) }}
                </a>

                @if(!$m->leido)
                    <span class="badge bg-danger">Nuevo</span>
                @endif
            </li>
        @empty
            <li class="list-group-item text-muted">
                No tienes mensajes.
            </li>
        @endforelse
    </ul>
</div>
@endsection
