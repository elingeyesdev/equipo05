@extends('layouts.app')

@push('css')
<style>
/* ====== SOLO ALMACENES (aislado) ====== */
.almx-wrap { font-family: inherit; }

/* Card principal del almacén */
.almx-almacen {
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid #e5e7eb;
  box-shadow: 0 10px 25px rgba(15,23,42,.06);
}

/* Header degradado */
.almx-almacen__head{
  background: linear-gradient(90deg, #4f46e5, #6366f1);
  color:#fff;
  padding: 16px 18px;
}

.almx-almacen__title{
  font-weight: 800;
  margin: 0;
  font-size: 1.05rem;
}
.almx-almacen__sub{
  opacity: .9;
  font-size: .9rem;
}

.almx-pill{
  display:inline-flex;
  align-items:center;
  gap:.35rem;
  padding:.25rem .65rem;
  border-radius: 999px;
  background: rgba(255,255,255,.18);
  font-size:.8rem;
  font-weight:600;
}

/* Estantes como cards pequeñas */
.almx-estante{
  border-radius: 14px;
  border: 1px solid #e5e7eb;
  background:#fff;
  overflow:hidden;
  height: 100%;
}
.almx-estante__head{
  padding: 10px 12px;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 700;
  display:flex;
  align-items:center;
  gap:.5rem;
}
.almx-estante__body{ padding: 12px; }

/* Chips */
.almx-chips{ display:flex; flex-wrap:wrap; gap:.45rem; }

.almx-chip{
  border-radius:999px;
  padding:.25rem .65rem;
  font-size:.78rem;
  border:1px solid #e5e7eb;
  background:#fff;
  display:inline-flex;
  align-items:center;
  gap:.35rem;
  line-height: 1;
}

.almx-chip--click{
  cursor:pointer;
  border-color:#4f46e5;
  background:#eef2ff;
  transition: .15s ease-in-out;
}
.almx-chip--click:hover{
  background:#4f46e5;
  color:#fff;
}
.almx-chip--click:hover .text-muted{ color:#e0e7ff !important; }

.almx-dot{
  width:10px; height:10px; border-radius:50%;
  display:inline-block;
}
.almx-dot--ok{ background:#22c55e; }
.almx-dot--bad{ background:#ef4444; }
.almx-dot--na{ background:#9ca3af; }

/* Leyenda */
.almx-legend{
  margin-top: 10px;
  display:flex;
  gap: 1rem;
  font-size: .85rem;
  color:#6b7280;
  border-top: 1px dashed #e5e7eb;
  padding-top: 10px;
}
.almx-legend span{ display:inline-flex; align-items:center; gap:.4rem; }

/* Modal table compacto */
.almx-table th{ font-size:.78rem; text-transform:uppercase; letter-spacing:.06em; }
.almx-table td{ vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="container py-4 almx-wrap">

  <div class="mb-3">
    <h1 class="h3 font-weight-bold mb-0">Estructura de Almacenes</h1>
    <small class="text-muted">Visualiza almacenes, estantes y espacios sincronizados.</small>
  </div>

  @foreach ($almacenes as $almacen)
    @php
      $totalEstantes = $almacen->estantes->count();
      $totalEspacios = $almacen->estantes->flatMap->espacios->count();
    @endphp

    <div class="almx-almacen mb-4">
      <div class="almx-almacen__head d-flex justify-content-between align-items-start flex-wrap" style="gap:12px;">
        <div>
          <div class="almx-almacen__title">{{ $almacen->nombre }}</div>
          @if($almacen->direccion)
            <div class="almx-almacen__sub">📍 {{ $almacen->direccion }}</div>
          @endif
          @if($almacen->latitud && $almacen->longitud)
            <div class="almx-almacen__sub mt-1">📌 Lat: {{ $almacen->latitud }} · Lng: {{ $almacen->longitud }}</div>
          @endif
        </div>

        <div class="d-flex" style="gap:.5rem;">
          <span class="almx-pill"><i class="fas fa-layer-group"></i> {{ $totalEstantes }} estantes</span>
          <span class="almx-pill"><i class="fas fa-th-large"></i> {{ $totalEspacios }} espacios</span>
        </div>
      </div>

      <div class="p-3 bg-white">
        @if ($almacen->estantes->isEmpty())
          <div class="text-muted">No hay estantes registrados para este almacén.</div>
        @else
          <div class="row">
            @foreach ($almacen->estantes as $estante)
              <div class="col-md-6 col-lg-4 mb-3">
                <div class="almx-estante">
                  <div class="almx-estante__head">
                    <i class="fas fa-stream text-muted"></i>
                    Estante: {{ $estante->codigo_estante }}
                  </div>

                  <div class="almx-estante__body">
                    @if($estante->descripcion)
                      <div class="text-muted mb-2" style="font-size:.85rem;">{{ $estante->descripcion }}</div>
                    @endif

                    @php $espacios = $estante->espacios; @endphp

                    @if($espacios->isEmpty())
                      <span class="text-muted" style="font-size:.85rem;">Sin espacios registrados.</span>
                    @else
                      <div class="almx-chips">
                        @foreach ($espacios as $espacio)
                          @php
                            $items = $espacio->items;
                            $tieneItems = $items->count() > 0;

                            $dot = 'almx-dot--na';
                            if ($tieneItems) $dot = 'almx-dot--bad';
                            elseif (($espacio->estado ?? '') === 'disponible') $dot = 'almx-dot--ok';
                          @endphp

                          @if($tieneItems)
                            {{-- AdminLTE 3 (Bootstrap 4): data-toggle / data-target --}}
                            <button type="button"
                              class="almx-chip almx-chip--click"
                              data-toggle="modal"
                              data-target="#modalEspacio{{ $espacio->espacioid }}">
                              <span class="almx-dot {{ $dot }}"></span>
                              <strong>{{ $espacio->codigo_espacio }}</strong>
                              <span class="text-muted">({{ $items->count() }})</span>
                            </button>

                            {{-- Modal --}}
                            <div class="modal fade" id="modalEspacio{{ $espacio->espacioid }}" tabindex="-1" role="dialog" aria-hidden="true">
                              <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header bg-primary">
                                    <h5 class="modal-title">
                                      <i class="fas fa-box-open mr-1"></i>
                                      Contenido en {{ $espacio->codigo_espacio }}
                                    </h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>

                                  <div class="modal-body">
                                    <p class="text-muted mb-2">
                                      <strong>Ubicación:</strong> {{ $almacen->nombre }} &raquo; {{ $estante->codigo_estante }}
                                    </p>

                                    <div class="table-responsive">
                                      <table class="table table-sm table-hover almx-table">
                                        <thead class="thead-light">
                                          <tr>
                                            <th>Producto</th>
                                            <th class="text-center">Cantidad</th>
                                            <th>Donante</th>
                                            <th>Fecha</th>
                                            <th>Código</th>
                                            <th class="text-right">Link</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          @foreach($items as $item)
                                            <tr>
                                              <td class="font-weight-bold text-primary">{{ $item->nombre_producto }}</td>
                                              <td class="text-center">{{ $item->cantidad_donada }} {{ $item->unidad_empaque }}</td>
                                              <td>{{ $item->nombre_donante }}</td>
                                              <td>{{ \Carbon\Carbon::parse($item->fecha_donacion)->format('d/m/Y') }}</td>
                                              <td class="text-nowrap">{{ $item->codigo_unico }}</td>
                                              <td class="text-right">
                                                    @if($item->codigo_paquete)
                                                        {{-- Enlace a NUESTRA ruta local con diseño mejorado --}}
                                                        <a class="btn btn-xs btn-primary text-white"
                                                        href="{{ route('reportes.trazabilidad.paquete', $item->codigo_paquete) }}">
                                                        <i class="fas fa-eye"></i> Ver Detalles
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                              </td>
                                            </tr>
                                          @endforeach
                                        </tbody>
                                      </table>
                                    </div>
                                  </div>

                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          @else
                            <span class="almx-chip" title="Espacio vacío">
                              <span class="almx-dot {{ $dot }}"></span>
                              {{ $espacio->codigo_espacio }}
                            </span>
                          @endif
                        @endforeach
                      </div>

                      <div class="almx-legend">
                        <span><i class="almx-dot almx-dot--ok"></i> Disponible</span>
                        <span><i class="almx-dot almx-dot--bad"></i> Ocupado</span>
                        <span><i class="almx-dot almx-dot--na"></i> Desconocido</span>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  @endforeach

</div>
@endsection
