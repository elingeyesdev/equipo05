@extends('layouts.app')

@section('title', 'Dashboard - Seguimiento de Voluntarios')

@section('css')
<style>
  /* Altura mínima fija para alinear los rows de ambas tablas */
  .list-group-item {
    min-height: 98px;
    display: flex;
    align-items: center;
  }
  
  .rounded-circle {
    flex-shrink: 0;
  }

  .chart-container {
    position: relative;
    height: 300px;
    margin: 10px 0;
  }
  
  .card-chart {
    min-height: 400px;
  }
</style>
@endsection

@section('content')
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-3">
      <div class="col-sm-12 text-center">
        <h1 class="m-0 text-primary font-weight-bold">Dashboard - Seguimiento de Voluntarios</h1>
        <p class="text-muted mb-0">Visualización de estadísticas y actividad del módulo</p>
        @if($gestionCompleta ?? false)
          <p class="mb-0 mt-1"><span class="badge badge-primary">Acceso completo — Administrador / Coordinador</span></p>
        @endif
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    {{-- TARJETAS RESUMEN --}}
    <div class="row">
      <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-info shadow-sm">
          <div class="inner">
            <h3>{{ $voluntariosActivos }}</h3>
            <p>Voluntarios Activos</p>
          </div>
          <div class="icon"><i class="fas fa-users"></i></div>
          <a href="{{ route('seguimiento.voluntarios') }}" class="small-box-footer">
            Ver más <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-secondary shadow-sm">
          <div class="inner">
            <h3>{{ $voluntariosInactivos }}</h3>
            <p>Voluntarios Inactivos</p>
          </div>
          <div class="icon"><i class="fas fa-user-slash"></i></div>
          <a href="{{ route('seguimiento.voluntarios-inactivos') }}" class="small-box-footer">
            Ver más <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-danger shadow-sm">
          <div class="inner">
            <h3>{{ $alertasRecientes }}</h3>
            <p>Alertas Recientes</p>
          </div>
          <div class="icon"><i class="fas fa-heartbeat"></i></div>
          <a href="{{ route('seguimiento.ayudas-solicitadas') }}" class="small-box-footer">
            Ver reportes <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>

      <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-success shadow-sm">
          <div class="inner">
            <h3>{{ $evaluacionesCompletadas }}</h3>
            <p>Evaluaciones Completadas</p>
          </div>
          <div class="icon"><i class="fas fa-chart-bar"></i></div>
          <a href="{{ route('seguimiento.evaluacion-pruebas') }}" class="small-box-footer">
            Ver más <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>

    @if($gestionCompleta ?? false)
    <div class="row mb-2">
      <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-primary shadow-sm">
          <div class="inner">
            <h3>{{ $totalAdministradores ?? 0 }}</h3>
            <p>Administradores del módulo</p>
          </div>
          <div class="icon"><i class="fas fa-user-shield"></i></div>
          <a href="{{ route('seguimiento.administradores') }}" class="small-box-footer">
            Gestionar <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-teal shadow-sm">
          <div class="inner">
            <h3>{{ $totalUniversidades ?? 0 }}</h3>
            <p>Universidades vinculadas</p>
          </div>
          <div class="icon"><i class="fas fa-university"></i></div>
          <a href="{{ route('seguimiento.universidades') }}" class="small-box-footer">
            Ver catálogo <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-warning shadow-sm">
          <div class="inner">
            <h3>{{ $consultasAbiertas ?? 0 }}</h3>
            <p>Consultas abiertas / en proceso</p>
          </div>
          <div class="icon"><i class="fas fa-life-ring"></i></div>
          <a href="{{ route('seguimiento.helpdesk') }}" class="small-box-footer">
            Centro de soporte <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
        <div class="small-box bg-indigo shadow-sm" style="background-color:#6610f2!important;">
          <div class="inner">
            <h3>{{ $conversacionesChat ?? 0 }}</h3>
            <p>Conversaciones activas</p>
          </div>
          <div class="icon"><i class="fas fa-comments"></i></div>
          <a href="{{ route('seguimiento.chat-consulta') }}" class="small-box-footer">
            Abrir chat <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
    </div>
    @endif

    {{-- PANELES INFORMATIVOS --}}
    <div class="row mb-4">
      {{-- Voluntarios --}}
      <div class="col-lg-6 mb-3">
        <div class="card card-outline card-primary shadow-sm h-100">
          <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
              <i class="fas fa-users mr-2"></i>Últimos voluntarios registrados
            </h3>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              @forelse($ultimosVoluntarios as $vol)
                @php
                  $iniciales = mb_substr($vol->nombre ?? '', 0, 1, 'UTF-8') . mb_substr($vol->apellido ?? '', 0, 1, 'UTF-8');
                  if (empty($iniciales)) {
                      $iniciales = 'V';
                  }
                  $estado = $vol->activo ? 'activo' : 'inactivo';
                @endphp
                <li class="list-group-item d-flex align-items-center">
                  <div class="rounded-circle bg-primary text-white text-center mr-3"
                       style="width:40px;height:40px;line-height:40px;font-weight:bold;flex-shrink:0;">
                    {{ strtoupper($iniciales) }}
                  </div>

                  <div>
                    <strong>{{ $vol->nombre ?? '' }} {{ $vol->apellido ?? '' }}</strong><br>

                    @if($estado === 'activo')
                      <small class="text-success font-weight-bold">
                        <i class="fas fa-check-circle"></i> Activo
                      </small>
                    @else
                      <small class="text-danger font-weight-bold">
                        <i class="fas fa-times-circle"></i> Inactivo
                      </small>
                    @endif

                    @if(!empty($vol->created_at))
                      <br>
                      <small class="text-muted">
                        <i class="far fa-calendar-alt"></i>
                        Registrado el {{ \Carbon\Carbon::parse($vol->created_at)->format('d/m/Y H:i') }}
                      </small>
                    @endif
                  </div>
                </li>
              @empty
                <li class="list-group-item text-muted text-center py-4">
                  <i class="fas fa-inbox fa-2x mb-2"></i><br>No hay voluntarios registrados todavía.
                </li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>

      {{-- Reportes --}}
      <div class="col-lg-6 mb-3">
        <div class="card card-outline card-danger shadow-sm h-100">
          <div class="card-header bg-danger text-white">
            <h3 class="card-title mb-0">
              <i class="fas fa-file-medical mr-2"></i>Últimos reportes generados
            </h3>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              @forelse($ultimosReportes as $rep)
                @php
                  $inicial = 'R';
                  $estado = $rep->estado ?? 'Sin estado';
                  $estadoLower = mb_strtolower($estado, 'UTF-8');

                  $estadoClass = ($estadoLower === 'pendiente' || $estadoLower === 'crítico' || $estadoLower === 'critico')
                      ? 'text-danger'
                      : ($estadoLower === 'en_proceso' ? 'text-warning' : 'text-success');
                  
                  $estadoIcon = ($estadoLower === 'pendiente' || $estadoLower === 'crítico' || $estadoLower === 'critico')
                      ? 'fas fa-exclamation-triangle'
                      : ($estadoLower === 'en_proceso' ? 'fas fa-clock' : 'fas fa-check-circle');
                @endphp

                <li class="list-group-item d-flex align-items-center">
                  <div class="rounded-circle bg-danger text-white text-center mr-3"
                      style="width:40px;height:40px;line-height:40px;font-weight:bold;flex-shrink:0;">
                    {{ $inicial }}
                  </div>
                  <div>
                    <strong>Reporte #{{ $rep->id }}</strong><br>
                    <small class="font-weight-bold {{ $estadoClass }}">
                      <i class="{{ $estadoIcon }}"></i> {{ ucfirst($estado) }}
                    </small><br>
                    @if(!empty($rep->created_at))
                      <small class="text-muted">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($rep->created_at)->format('d/m/Y H:i') }}
                      </small>
                    @endif
                  </div>
                </li>
              @empty
                <li class="list-group-item text-muted text-center py-4">
                  <i class="fas fa-inbox fa-2x mb-2"></i><br>No hay reportes generados todavía.
                </li>
              @endforelse
            </ul>
          </div>
        </div>
      </div>
    </div>

    {{-- SECCION DE GRÁFICOS PIE CHARTS --}}
    <div class="row">
      {{-- Universidades --}}
      <div class="col-lg-4 mb-3">
        <div class="card card-outline card-info shadow-sm card-chart h-100">
          <div class="card-header bg-info text-white">
            <h4 class="card-title mb-0">
              <i class="fas fa-university mr-2"></i>Universidades
            </h4>
          </div>
          <div class="card-body d-flex flex-column justify-content-center">
            <div class="chart-container" id="containerUniversidades">
              <canvas id="chartUniversidades"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- Necesidades --}}
      <div class="col-lg-4 mb-3">
        <div class="card card-outline card-warning shadow-sm card-chart h-100">
          <div class="card-header bg-warning">
            <h4 class="card-title mb-0 text-dark">
              <i class="fas fa-clipboard-list mr-2"></i>Necesidades
            </h4>
          </div>
          <div class="card-body d-flex flex-column justify-content-center">
            <div class="chart-container" id="containerNecesidades">
              <canvas id="chartNecesidades"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- Capacitaciones --}}
      <div class="col-lg-4 mb-3">
        <div class="card card-outline card-success shadow-sm card-chart h-100">
          <div class="card-header bg-success text-white">
            <h4 class="card-title mb-0">
              <i class="fas fa-chalkboard-teacher mr-2"></i>Capacitaciones
            </h4>
          </div>
          <div class="card-body d-flex flex-column justify-content-center">
            <div class="chart-container" id="containerCapacitaciones">
              <canvas id="chartCapacitaciones"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  
  const universidadesData = @json($universidadesData);
  const necesidadesData = @json($necesidadesData);
  const capacitacionesData = @json($capacitacionesData);

  const colorsUniversidades = [
    '#17a2b8', '#20c997', '#ffc107', '#fd7e14', '#e83e8c',
    '#6f42c1', '#007bff', '#28a745', '#dc3545', '#6c757d'
  ];

  const colorsNecesidades = [
    '#ffc107', '#fd7e14', '#e83e8c', '#17a2b8', '#20c997',
    '#6f42c1', '#007bff', '#28a745', '#dc3545', '#6c757d'
  ];

  const colorsCapacitaciones = [
    '#28a745', '#20c997', '#17a2b8', '#007bff', '#6f42c1',
    '#ffc107', '#fd7e14', '#e83e8c', '#dc3545', '#6c757d'
  ];

  const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          padding: 15,
          font: {
            size: 11
          }
        }
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            let label = context.label || '';
            if (label) {
              label += ': ';
            }
            label += context.parsed;
            return label;
          }
        }
      }
    }
  };

  // Gráfico Universidades
  const hasUniversidades = universidadesData && universidadesData.length > 0 && universidadesData.some(item => item.total > 0);
  if (hasUniversidades) {
    const ctxUniversidades = document.getElementById('chartUniversidades').getContext('2d');
    new Chart(ctxUniversidades, {
      type: 'pie',
      data: {
        labels: universidadesData.map(item => item.label),
        datasets: [{
          data: universidadesData.map(item => item.total),
          backgroundColor: colorsUniversidades.slice(0, universidadesData.length),
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: commonOptions
    });
  } else {
    document.getElementById('containerUniversidades').innerHTML = 
      '<div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted"><i class="fas fa-chart-pie fa-3x mb-3 text-secondary"></i>Sin datos disponibles</div>';
  }

  // Gráfico Necesidades
  const hasNecesidades = necesidadesData && necesidadesData.length > 0 && necesidadesData.some(item => item.total > 0);
  if (hasNecesidades) {
    const ctxNecesidades = document.getElementById('chartNecesidades').getContext('2d');
    new Chart(ctxNecesidades, {
      type: 'pie',
      data: {
        labels: necesidadesData.map(item => item.label),
        datasets: [{
          data: necesidadesData.map(item => item.total),
          backgroundColor: colorsNecesidades.slice(0, necesidadesData.length),
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: commonOptions
    });
  } else {
    document.getElementById('containerNecesidades').innerHTML = 
      '<div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted"><i class="fas fa-chart-pie fa-3x mb-3 text-secondary"></i>Sin datos disponibles</div>';
  }

  // Gráfico Capacitaciones
  const hasCapacitaciones = capacitacionesData && capacitacionesData.length > 0 && capacitacionesData.some(item => item.total > 0);
  if (hasCapacitaciones) {
    const ctxCapacitaciones = document.getElementById('chartCapacitaciones').getContext('2d');
    new Chart(ctxCapacitaciones, {
      type: 'pie',
      data: {
        labels: capacitacionesData.map(item => item.label),
        datasets: [{
          data: capacitacionesData.map(item => item.total),
          backgroundColor: colorsCapacitaciones.slice(0, capacitacionesData.length),
          borderWidth: 2,
          borderColor: '#fff'
        }]
      },
      options: commonOptions
    });
  } else {
    document.getElementById('containerCapacitaciones').innerHTML = 
      '<div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted"><i class="fas fa-chart-pie fa-3x mb-3 text-secondary"></i>Sin datos disponibles</div>';
  }

});
</script>
@endsection
