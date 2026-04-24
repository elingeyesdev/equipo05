@extends('layouts.app')

@section('content')
<div class="container">
  <h3 class="mb-3">Chat</h3>

  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">Usuarios</div>
        <div class="list-group list-group-flush">
          @foreach($usuarios as $u)
            <a class="list-group-item list-group-item-action"
               href="{{ route('chat.conversacion', $u->usuarioid) }}">
              {{ $u->nombre }} {{ $u->apellido }}
            </a>
          @endforeach
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Conversaciones</div>
        <div class="list-group list-group-flush">
          @forelse($conversaciones as $c)
            @php
              $otro = $c->usuarios->first();
              $ultimo = $c->mensajes->first();
            @endphp

            <a class="list-group-item list-group-item-action"
               href="{{ route('chat.conversacion', $otro->usuarioid) }}">
              <strong>{{ $otro->nombre ?? 'Usuario' }}</strong>
              @if($ultimo)
                — {{ \Illuminate\Support\Str::limit($ultimo->contenido, 60) }}
              @endif
            </a>
          @empty
            <div class="list-group-item text-muted">Aún no tienes conversaciones.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
