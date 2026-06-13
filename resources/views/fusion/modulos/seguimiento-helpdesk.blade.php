@extends('layouts.app')

@section('title', 'Centro de Soporte')

@section('css')
<style>
  .hd-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
  .hd-header h1 { font-size: 2rem; font-weight: 700; margin: 0; }
  .badge-estado-abierta { background: #fee2e2; color: #b91c1c; }
  .badge-estado-en_proceso { background: #fef3c7; color: #b45309; }
  .badge-estado-resuelta { background: #dcfce7; color: #15803d; }
  .badge-estado-cerrada { background: #e2e8f0; color: #475569; }
  .badge-prioridad-alta { background: #fecaca; color: #991b1b; }
  .badge-prioridad-media { background: #fde68a; color: #92400e; }
  .badge-prioridad-baja { background: #dbeafe; color: #1d4ed8; }
  .ticket-desc { color: #64748b; font-size: .9rem; max-width: 420px; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
  <div class="hd-header">
    <h1><i class="fas fa-life-ring text-warning mr-2"></i>Centro de Soporte</h1>
    <div>
      <a href="{{ route('seguimiento.dashboard') }}" class="btn btn-outline-secondary btn-sm mr-1"><i class="fas fa-arrow-left"></i> Dashboard</a>
      <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-warning btn-sm"><i class="fas fa-plus"></i> Nueva consulta</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row mb-3">
    @foreach(['abierta' => 'danger', 'en_proceso' => 'warning', 'resuelta' => 'success', 'cerrada' => 'secondary'] as $est => $color)
      <div class="col-6 col-md-3 mb-2">
        <div class="info-box bg-{{ $color }} mb-0">
          <span class="info-box-icon"><i class="fas fa-ticket-alt"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-capitalize">{{ str_replace('_', ' ', $est) }}</span>
            <span class="info-box-number">{{ $conteoEstados[$est] ?? 0 }}</span>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title mb-0">Consultas y tickets</h3></div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>Asunto</th>
              <th>Voluntario</th>
              <th>Prioridad</th>
              <th>Estado</th>
              <th>Fecha</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($consultas as $c)
              @php
                $est = strtolower($c->estado ?? 'abierta');
                $pri = strtolower($c->prioridad ?? 'media');
              @endphp
              <tr>
                <td>{{ $c->id }}</td>
                <td>
                  <strong>{{ $c->asunto }}</strong>
                  @if(!empty($c->descripcion))
                    <div class="ticket-desc">{{ \Illuminate\Support\Str::limit($c->descripcion, 90) }}</div>
                  @endif
                </td>
                <td>{{ trim(($c->vol_nombre ?? '').' '.($c->vol_apellido ?? '')) ?: '—' }}</td>
                <td><span class="badge badge-pill badge-prioridad-{{ $pri }} text-uppercase">{{ $pri }}</span></td>
                <td><span class="badge badge-pill badge-estado-{{ $est }} text-uppercase">{{ str_replace('_', ' ', $est) }}</span></td>
                <td>{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('d/m/Y H:i') : '—' }}</td>
                <td>
                  <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $c->id]) }}" class="btn btn-xs btn-outline-primary"><i class="fas fa-edit"></i></a>
                </td>
              </tr>
            @empty
              <tr><td colspan="7" class="text-center text-muted py-4">No hay consultas registradas.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
