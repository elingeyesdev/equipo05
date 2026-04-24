@extends('layouts.app')

@section('title', 'Inventario de Trazabilidad')

@section('content')
<div class="content-wrapper">

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1><i class="fas fa-warehouse"></i> Inventario de Trazabilidad</h1>
                <p class="text-muted mb-0">Vista global de existencias físicas y paquetes por almacén.</p>
            </div>
            <div class="col-sm-4 text-right">
                <a href="{{ route('reportes.trazabilidad.pdf') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf"></i> PDF Global
                </a>
                <a href="{{ route('reportes.trazabilidad.excel') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Excel Global
                </a>
            </div>
        </div>
    </div>
</section>

<section class="content">
<div class="container-fluid">

@forelse($almacenes as $alm)
    @if($alm->lista_productos->count() || $alm->lista_paquetes->count())
    <div class="card card-outline card-primary mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="card-title font-weight-bold"><i class="fas fa-building"></i> {{ $alm->nombre }}</h3>
                    <div class="text-muted text-sm"><i class="fas fa-map-marker-alt"></i> {{ $alm->direccion ?? 'Sin dirección' }}</div>
                </div>
                <div>
                    <span class="badge badge-info p-2 mr-2">{{ $alm->lista_productos->count() }} Items</span>
                    <span class="badge badge-warning p-2">{{ $alm->lista_paquetes->count() }} Paquetes</span>
                </div>
            </div>
        </div>

        <div class="card-body bg-light">
            <div class="row">
                {{-- PRODUCTOS --}}
                <div class="col-lg-7 border-right">
                    <h5 class="text-primary font-weight-bold mb-3"><i class="fas fa-list"></i> Productos en Stock</h5>
                    @if($alm->lista_productos->isEmpty())
                        <div class="alert alert-secondary text-center">No hay productos disponibles.</div>
                    @else
                        <div class="table-responsive" style="max-height:400px;">
                            <table class="table table-sm table-hover">
                                <thead class="thead-dark sticky-top">
                                    <tr><th>Código</th><th>Producto</th><th>Ubicación</th><th class="text-center">Cant.</th></tr>
                                </thead>
                                <tbody>
                                @foreach($alm->lista_productos as $prod)
                                    <tr>
                                        <td>
                                            <span class="badge badge-secondary">{{ $prod->codigo_unico }}</span>
                                            @if($prod->codigo_paquete) <span class="badge badge-warning ml-1">PKG</span> @endif
                                        </td>
                                        <td>
                                            <strong>{{ $prod->nombre_producto }}</strong><br>
                                            <small class="text-muted">Donante: {{ $prod->nombre_donante }}</small>
                                        </td>
                                        <td><span class="badge badge-light">{{ $prod->estante_codigo ?? '-' }} / {{ $prod->espacio_codigo ?? '-' }}</span></td>
                                        <td class="text-center text-success font-weight-bold">{{ $prod->cantidad_donada }} {{ $prod->unidad_empaque }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- PAQUETES --}}
                <div class="col-lg-5">
                    <h5 class="text-warning font-weight-bold mb-3"><i class="fas fa-cubes"></i> Paquetes Armados</h5>
                    @if($alm->lista_paquetes->isEmpty())
                        <div class="alert alert-secondary text-center">No existen paquetes asociados.</div>
                    @else
                        <div style="max-height:400px; overflow-y:auto;">
                            @foreach($alm->lista_paquetes as $pack)
                                <div class="card card-outline card-warning mb-2">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><i class="fas fa-cube"></i> {{ $pack->codigo_paquete }}</strong>
                                                <div class="text-muted text-sm">
                                                    <i class="far fa-calendar-alt"></i> {{ $pack->fecha_creacion_paquete ? \Carbon\Carbon::parse($pack->fecha_creacion_paquete)->format('d/m/Y') : 'N/A' }}
                                                </div>
                                            </div>
                                            <span class="badge badge-warning">{{ $pack->items_count }} items</span>
                                        </div>
                                        <hr class="my-2">
                                        {{-- BOTÓN MODAL --}}
                                        <button type="button" class="btn btn-sm btn-outline-dark btn-block btn-ver-gateway" data-codigo="{{ $pack->codigo_paquete }}">
                                            <i class="fas fa-search-plus"></i> Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
@empty
    <div class="text-center py-5">
        <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
        <h4 class="text-muted">No existen almacenes sincronizados</h4>
    </div>
@endforelse

</div>
</section>
</div>

{{-- MODAL REUTILIZABLE (Misma estructura, textos limpios) --}}
<div class="modal fade" id="modalGateway" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title"><i class="fas fa-cube"></i> Detalle de Paquete <span class="badge badge-dark ml-2" id="gwCodigo">...</span></h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div id="gwLoading" class="text-center py-4" style="display:none;">
          <div class="spinner-border text-warning" role="status"></div>
          <div class="text-muted mt-2">Cargando información...</div>
        </div>
        <div id="gwError" class="alert alert-danger" style="display:none;"></div>
        <div id="gwContent" style="display:none;">
          <div class="row">
            <div class="col-md-6">
              <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                <div class="info-box-content"><span class="info-box-text">Estado</span><span class="info-box-number" id="gwEstado">-</span></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content"><span class="info-box-text">Total productos</span><span class="info-box-number" id="gwTotalProductos">0</span></div>
              </div>
            </div>
          </div>
          {{-- Resto del contenido del modal igual, solo textos limpios --}}
          <div class="text-muted mb-2"><strong>Fecha creación:</strong> <span id="gwFecha">-</span> | <strong>Registros salida:</strong> <span id="gwSalidas">0</span></div>
          <div class="text-muted mb-3"><strong>Registrado por:</strong> <span id="gwUsuario">-</span></div>
          <hr>
          <h6 class="font-weight-bold mb-2"><i class="fas fa-list"></i> Contenido</h6>
          <div class="table-responsive" style="max-height:260px;">
            <table class="table table-sm table-hover">
              <thead class="thead-light"><tr><th>Producto</th><th class="text-center">Cant.</th><th>Donante</th><th>Fecha</th></tr></thead>
              <tbody id="gwDetalles"></tbody>
            </table>
          </div>
          <div class="mt-3">
            <h6 class="font-weight-bold mb-2"><i class="fas fa-truck"></i> Historial de Salidas</h6>
            <ul class="list-group" id="gwRegistrosSalida"></ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
@push('scripts')
<script>
function safeText(v, fallback='-') {
  return (v === null || v === undefined || v === '') ? fallback : v;
}

function renderGatewayModal(payload) {
  // payload puede ser: {codigo_paquete, services:{donaciones:{...}}}
  // o directamente el bloque guardado en DB. Lo normalizamos:
  const data = payload || {};

  let don = null;

  // Caso 1: respuesta completa del gateway
  if (data.services && data.services.donaciones) {
    don = data.services.donaciones;
  }

  // Caso 2: guardaste SOLO services
  if (!don && data.donaciones) {
    don = data.donaciones;
  }

  // Caso 3: guardaste SOLO el bloque donaciones
  if (!don && data.success !== undefined && (data.paquete || data.detalles || data.registros_salida)) {
    don = data;
  }

  if (!don || don.success !== true) {
    $('#gwError').show().text('El Gateway respondió, pero no hay datos del servicio de donaciones.');
    return;
  }

  const paquete = don.paquete || {};
  $('#gwEstado').text(safeText(paquete.estado));
  $('#gwTotalProductos').text(safeText(paquete.total_productos, 0));
  $('#gwFecha').text(safeText(paquete.fecha_creacion));
  $('#gwSalidas').text(safeText(paquete.total_registros_salida, 0));

  const usr = paquete.usuario_registro || {};
  const usrTxt = usr.nombre_completo ? `${usr.nombre_completo}${usr.ci ? ' ('+usr.ci+')' : ''}` : '-';
  $('#gwUsuario').text(usrTxt);

  // Tabla detalles
  const detalles = Array.isArray(don.detalles) ? don.detalles : [];
  let htmlDet = '';
  if (!detalles.length) {
    htmlDet = `<tr><td colspan="4" class="text-center text-muted">Sin contenido</td></tr>`;
  } else {
    detalles.forEach(d => {
      const prod = d.producto?.nombre ?? '-';
      const cant = d.cantidad_usada ?? '-';
      const donante = d.donacion?.donante?.nombre ?? '-';
      const fecha = d.donacion?.fecha ?? '-';

      htmlDet += `
        <tr>
          <td class="font-weight-bold text-primary">${prod}</td>
          <td class="text-center">${cant}</td>
          <td>${donante}</td>
          <td>${fecha}</td>
        </tr>
      `;
    });
  }
  $('#gwDetalles').html(htmlDet);

  // Lista de salidas
  const salidas = Array.isArray(don.registros_salida) ? don.registros_salida : [];
  let htmlSal = '';
  if (!salidas.length) {
    htmlSal = `<li class="list-group-item text-muted">Sin salidas</li>`;
  } else {
    salidas.forEach(s => {
      htmlSal += `
        <li class="list-group-item">
          <div class="d-flex justify-content-between">
            <strong>${safeText(s.destino)}</strong>
            <span class="text-muted">${safeText(s.fecha_salida)}</span>
          </div>
          <div class="text-muted text-sm">Obs: ${safeText(s.observaciones, 'N/A')}</div>
        </li>
      `;
    });
  }
  $('#gwRegistrosSalida').html(htmlSal);
}

$(document).on('click', '.btn-ver-gateway', function () {
  const codigo = $(this).data('codigo');

  $('#gwCodigo').text(codigo);

  // Reset UI
  $('#gwError').hide().text('');
  $('#gwContent').hide();
  $('#gwLoading').show();

  $('#gwEstado').text('-');
  $('#gwTotalProductos').text('0');
  $('#gwFecha').text('-');
  $('#gwSalidas').text('0');
  $('#gwUsuario').text('-');
  $('#gwDetalles').html(`<tr><td colspan="4" class="text-center text-muted">Cargando...</td></tr>`);
  $('#gwRegistrosSalida').html(`<li class="list-group-item text-muted">Cargando...</li>`);

  $('#modalGateway').modal('show');

  $.get("{{ url('/reportes/trazabilidad/paquete') }}/" + codigo + "/ajax")
    .done(function(res){
      // OJO: tu controller devuelve {success:true, data: ...}
      if (!res || res.success !== true) {
        $('#gwLoading').hide();
        $('#gwError').show().text(res?.message || 'Respuesta inválida del servidor.');
        return;
      }

      renderGatewayModal(res.data);

      $('#gwLoading').hide();
      $('#gwContent').show();
    })
    .fail(function(xhr){
      $('#gwLoading').hide();
      $('#gwError').show().text('No se pudo obtener información (AJAX falló).');
      console.log('AJAX fail', xhr);
    });
});
</script>
@endpush