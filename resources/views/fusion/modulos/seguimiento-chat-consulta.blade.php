@extends('layouts.app')

@section('title', 'Chat de Voluntarios')

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
      <div>
        <h1 class="m-0"><i class="fas fa-comments text-primary mr-2"></i>Chat de Voluntarios</h1>
        <p class="text-muted mb-0">Coordinación en tiempo real con brigadas y voluntarios activos.</p>
      </div>
      <a href="{{ route('seguimiento.dashboard') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
        <i class="fas fa-arrow-left"></i> Dashboard
      </a>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="chat-container">
      <div class="chat-sidebar">
        <div class="card mb-0">
          <div class="card-header py-2"><strong>Conversaciones</strong></div>
          <div class="list-group list-group-flush" style="max-height: 520px; overflow-y: auto;">
            @forelse($conversaciones as $conv)
              <a href="{{ route('seguimiento.chat-consulta', ['conversacion' => $conv->conversacion_id]) }}"
                 class="list-group-item list-group-item-action {{ (int) $conversacionActiva === (int) $conv->conversacion_id ? 'active' : '' }}">
                <div class="d-flex justify-content-between">
                  <strong>{{ trim($conv->nombre.' '.$conv->apellido) }}</strong>
                  <small>{{ $conv->ultimo_mensaje_at ? \Carbon\Carbon::parse($conv->ultimo_mensaje_at)->format('d/m H:i') : '' }}</small>
                </div>
                <small class="{{ (int) $conversacionActiva === (int) $conv->conversacion_id ? 'text-white-50' : 'text-muted' }}">
                  {{ \Illuminate\Support\Str::limit($conv->ultimo_mensaje ?? 'Sin mensajes', 48) }}
                </small>
              </a>
            @empty
              <div class="p-3 text-muted text-center">No hay conversaciones activas.</div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="chat-main">
        <div class="card mb-0">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
              @if($voluntarioActivo)
                <i class="fas fa-user mr-1"></i> {{ $voluntarioActivo->nombre }} {{ $voluntarioActivo->apellido }}
              @else
                Seleccione una conversación
              @endif
            </h3>
            @if($voluntarioActivo)
              <small class="text-muted">{{ $voluntarioActivo->email ?? '' }}</small>
            @endif
          </div>

          <div class="card-body chat-window" style="min-height: 380px; max-height: 480px; overflow-y: auto; background: #f8fafc;">
            @if($voluntarioActivo)
              @forelse($mensajes as $msg)
                @php
                  $tipo = $msg->remitente_tipo ?? 'voluntario';
                  $esStaff = in_array($tipo, ['coordinador', 'administrador'], true);
                  $autorLabel = match ($tipo) {
                      'administrador' => 'Administración',
                      'coordinador' => 'Coordinación',
                      default => 'Voluntario',
                  };
                @endphp
                <div class="d-flex mb-3 {{ $esStaff ? 'justify-content-end' : '' }}">
                  <div>
                    <div class="chat-bubble {{ $esStaff ? 'chat-bubble-right' : 'chat-bubble-left' }}">
                      {{ $msg->mensaje }}
                    </div>
                    <div class="chat-meta text-muted small {{ $esStaff ? 'text-right' : '' }}">
                      {{ $autorLabel }}
                      · {{ $msg->created_at ? \Carbon\Carbon::parse($msg->created_at)->format('d/m/Y H:i') : '' }}
                    </div>
                  </div>
                </div>
              @empty
                <p class="text-muted text-center mt-5">Sin mensajes en esta conversación. Escriba el primero abajo.</p>
              @endforelse
            @else
              <p class="text-muted text-center mt-5">Seleccione un voluntario en la lista para ver el chat.</p>
            @endif
          </div>

          @if($voluntarioActivo)
            <div class="card-footer">
              <form method="POST" action="{{ route('seguimiento.chat.enviar') }}" class="d-flex" style="gap: .5rem;">
                @csrf
                <input type="hidden" name="conversacion_id" value="{{ $conversacionActiva }}">
                <input type="text" name="mensaje" class="form-control" placeholder="Escriba un mensaje como coordinación..." maxlength="2000" required autocomplete="off">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Enviar</button>
              </form>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var win = document.querySelector('.chat-window');
  if (win) win.scrollTop = win.scrollHeight;
});
</script>
@endsection
