@extends('layouts.app')

@section('title', 'Universidades')

@section('css')
<style>
  .uni-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
  .uni-header h1 { font-size: 2rem; font-weight: 700; margin: 0; }
  .uni-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
  .uni-card { background: #fff; border: 1px solid #e2e8f0; border-top: 4px solid #17a2b8; border-radius: 8px; padding: 1.25rem; box-shadow: 0 2px 6px rgba(0,0,0,.04); }
  .uni-card h3 { font-size: 1.05rem; font-weight: 700; margin: 0 0 .35rem; }
  .uni-meta { color: #64748b; font-size: .9rem; margin-bottom: .75rem; }
  .uni-badge { display: inline-block; background: #e0f2fe; color: #0369a1; font-weight: 600; padding: .2rem .55rem; border-radius: 999px; font-size: .8rem; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
  <div class="uni-header">
    <h1><i class="fas fa-university text-info mr-2"></i>Universidades</h1>
    <div>
      <a href="{{ route('seguimiento.dashboard') }}" class="btn btn-outline-secondary btn-sm mr-1"><i class="fas fa-arrow-left"></i> Dashboard</a>
      <a href="{{ route('seguimiento.crud.create', ['seccion' => $seccion]) }}" class="btn btn-info btn-sm"><i class="fas fa-plus"></i> Agregar universidad</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="row mb-3">
    <div class="col-md-3"><div class="small-box bg-info"><div class="inner"><h3>{{ $universidades->count() }}</h3><p>Universidades registradas</p></div></div></div>
    <div class="col-md-3"><div class="small-box bg-success"><div class="inner"><h3>{{ $totalVoluntarios }}</h3><p>Voluntarios vinculados</p></div></div></div>
  </div>

  @if($universidades->isEmpty())
    <div class="alert alert-light border text-center py-5">
      <i class="fas fa-university fa-3x text-muted mb-3"></i>
      <p class="mb-0 text-muted">No hay universidades registradas. Agregue la primera para vincular voluntarios.</p>
    </div>
  @else
    <div class="uni-grid">
      @foreach($universidades as $uni)
        <div class="uni-card">
          <h3>{{ $uni->nombre }}</h3>
          <div class="uni-meta">
            @if(!empty($uni->sigla))<span class="uni-badge mr-1">{{ $uni->sigla }}</span>@endif
            @if(!empty($uni->ciudad))<i class="fas fa-map-marker-alt mr-1"></i>{{ $uni->ciudad }}@endif
          </div>
          <p class="mb-2"><strong>{{ $uni->voluntarios_count ?? 0 }}</strong> voluntario(s) vinculado(s)</p>
          <a href="{{ route('seguimiento.crud.edit', ['seccion' => $seccion, 'id' => $uni->id_universidad]) }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-edit"></i> Editar
          </a>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
