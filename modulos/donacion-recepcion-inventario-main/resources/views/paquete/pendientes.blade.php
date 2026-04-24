@extends('adminlte::page')

@section('title', 'Solicitudes de Paquetes Pendientes')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Solicitudes de Paquetes Pendientes</h1>
    </div>
    <div class="col-sm-6">
        <a href="{{ route('inventario.paquete.index') }}" class="btn btn-secondary float-right">
            <i class="fas fa-arrow-left"></i> Volver a Paquetes
        </a>
    </div>
</div>
@stop

@section('content')

{{-- Statistics Row --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3 id="totalPendientes">0</h3>
                <p>Solicitudes Pendientes</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="card card-warning card-outline">
    <div class="card-header">
        <h3 class="card-title">Listado de Solicitudes Pendientes</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-primary" onclick="cargarSolicitudes()">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="loading" class="text-center" style="display: none;">
            <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
            <p class="mt-2">Cargando solicitudes...</p>
        </div>
        <div id="error" class="alert alert-danger" style="display: none;"></div>
        <div id="solicitudesContainer" class="row">
            <!-- Las cards se cargarán aquí dinámicamente -->
        </div>
    </div>
</div>

@stop

@section('css')
<style>
    .solicitud-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .solicitud-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .info-row {
        margin-bottom: 10px;
    }
    .info-label {
        font-weight: bold;
        color: #495057;
    }
    .badge-custom {
        font-size: 0.9rem;
        padding: 5px 10px;
    }
</style>
@stop

@section('js')
<script>
    const API_BASE_URL = "{{ env('API_BASE_URL_ADS', 'http://192.168.22.128:8000') }}";
    
    // Cargar solicitudes al inicio
    document.addEventListener('DOMContentLoaded', function() {
        cargarSolicitudes();
    });

    function cargarSolicitudes() {
        const loading = document.getElementById('loading');
        const error = document.getElementById('error');
        const container = document.getElementById('solicitudesContainer');
        
        // Mostrar loading
        loading.style.display = 'block';
        error.style.display = 'none';
        container.innerHTML = '';
        
        fetch(`${API_BASE_URL}/api/gateway/logistica/paquetes/pendientes`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar las solicitudes');
                }
                return response.json();
            })
            .then(data => {
                loading.style.display = 'none';
                
                if (data.success && data.data.length > 0) {
                    document.getElementById('totalPendientes').textContent = data.data.length;
                    mostrarSolicitudes(data.data);
                } else {
                    container.innerHTML = `
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> No hay solicitudes pendientes en este momento.
                            </div>
                        </div>
                    `;
                }
            })
            .catch(err => {
                loading.style.display = 'none';
                error.textContent = 'Error al cargar las solicitudes: ' + err.message;
                error.style.display = 'block';
            });
    }

    function mostrarSolicitudes(solicitudes) {
        const container = document.getElementById('solicitudesContainer');
        
        solicitudes.forEach(paquete => {
            const card = crearCardSolicitud(paquete);
            container.innerHTML += card;
        });
    }

    function crearCardSolicitud(paquete) {
        const solicitud = paquete.solicitud;
        const solicitante = solicitud.solicitante;
        const destino = solicitud.destino;
        const estado = paquete.estado;
        
        const fechaCreacion = new Date(paquete.fecha_creacion).toLocaleDateString('es-ES');
        const fechaSolicitud = new Date(solicitud.fecha_solicitud).toLocaleDateString('es-ES');
        const fechaNecesidad = solicitud.fecha_inicio ? new Date(solicitud.fecha_inicio).toLocaleDateString('es-ES') : 'No especificada';
        
        return `
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="card card-outline card-warning solicitud-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-box"></i> Paquete #${paquete.id_paquete}
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-warning badge-custom">${estado.nombre_estado}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="info-row">
                            <span class="info-label">Código Seguimiento:</span>
                            <span class="badge badge-info">${solicitud.codigo_seguimiento}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Fecha Creación Paquete:</span>
                            <span>${fechaCreacion}</span>
                        </div>

                        <hr>

                        <h5 class="text-primary"><i class="fas fa-file-alt"></i> Información de Solicitud</h5>
                        
                        <div class="info-row">
                            <span class="info-label">Tipo Emergencia:</span>
                            <span class="badge badge-danger">${solicitud.tipo_emergencia || 'No especificado'}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Cantidad de Personas:</span>
                            <span><i class="fas fa-users"></i> ${solicitud.cantidad_personas}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Fecha de Necesidad:</span>
                            <span>${fechaNecesidad}</span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">Insumos Necesarios:</span>
                            <p class="text-muted mb-1">${solicitud.insumos_necesarios || 'No especificado'}</p>
                        </div>

                        <hr>

                        <h5 class="text-success"><i class="fas fa-user"></i> Solicitante</h5>
                        <div class="info-row">
                            <span class="info-label">Nombre:</span>
                            <span>${solicitante.nombre} ${solicitante.apellido}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">CI:</span>
                            <span>${solicitante.ci}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Teléfono:</span>
                            <span><i class="fas fa-phone"></i> ${solicitante.telefono}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span><i class="fas fa-envelope"></i> ${solicitante.email}</span>
                        </div>

                        <hr>

                        <h5 class="text-info"><i class="fas fa-map-marker-alt"></i> Destino</h5>
                        <div class="info-row">
                            <span class="info-label">Comunidad:</span>
                            <span>${destino.comunidad}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Provincia:</span>
                            <span>${destino.provincia}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Dirección:</span>
                            <p class="text-muted mb-1">${destino.direccion}</p>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Coordenadas:</span>
                            <span><small>${destino.latitud}, ${destino.longitud}</small></span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-success" onclick="procesarPaquete(${paquete.id_paquete}, '${solicitud.codigo_seguimiento}')">
                            <i class="fas fa-check"></i> Procesar
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    function procesarPaquete(idPaqueteExterno, codigoSeguimiento) {
        // Guardar datos en sessionStorage para usarlos en el formulario
        sessionStorage.setItem('paquete_externo_id', idPaqueteExterno);
        sessionStorage.setItem('codigo_seguimiento', codigoSeguimiento);
        
        // Redirigir al formulario de creación de paquete
        window.location.href = '/paquete/create';
    }
</script>
@stop





