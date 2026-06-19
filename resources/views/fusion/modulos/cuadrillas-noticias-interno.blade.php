@extends('layouts.app')

@section('content_header_title', 'Noticias')
@section('content_header_subtitle', 'Incendios y conservación — fuente opinion.com.bo')

@section('content')
@include('fusion.modulos.partials.cuadrillas-module-nav')
@include('fusion.modulos.partials.cuadrillas-flash')

<div class="card cua-list-card shadow-sm mb-3">
    <div class="card-header">
        <div class="cua-btn-toolbar w-100">
            <button id="actualizarNoticias" class="btn btn-primary btn-sm">
                <i class="fas fa-sync-alt"></i> Actualizar noticias
            </button>
        </div>
    </div>
    <div class="card-body pb-0">
        <div class="alert alert-light border small mb-3">
            <i class="fas fa-info-circle text-info mr-1"></i>
            Noticias sincronizadas desde <strong>opinion.com.bo</strong> para equipos de apoyo y voluntarios.
        </div>
    </div>
</div>

    <!-- Grid de Noticias -->
    <div class="row">
        @forelse($noticias as $noticia)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm hover-shadow border-0 card-outline card-danger">
                    @if($noticia->image)
                        <img src="{{ $noticia->image }}" class="card-img-top" alt="{{ $noticia->titulo }}" 
                             style="height: 200px; object-fit: cover; border-top-left-radius: .25rem; border-top-right-radius: .25rem;">
                    @else
                        <div class="card-img-top bg-gradient-danger d-flex align-items-center justify-content-center" 
                             style="height: 200px; border-top-left-radius: .25rem; border-top-right-radius: .25rem;">
                            <i class="fas fa-fire fa-4x text-white opacity-50"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title font-weight-bold mb-2">
                            <a href="{{ $noticia->url }}" target="_blank" class="text-dark text-decoration-none hover-orange">
                                {{ Str::limit($noticia->titulo, 80) }}
                            </a>
                        </h5>
                        
                        <p class="card-text text-muted small mb-3">
                            <i class="far fa-calendar-alt mr-1"></i> 
                            {{ $noticia->date->format('d/m/Y H:i') }}
                        </p>
                        
                        @if($noticia->descripcion)
                            <p class="card-text flex-grow-1 text-secondary" style="font-size: 0.95rem;">
                                {{ Str::limit($noticia->descripcion, 130) }}
                            </p>
                        @endif
                        
                        <a href="{{ $noticia->url }}" target="_blank" class="btn btn-outline-danger btn-sm btn-block mt-3 font-weight-bold">
                            <i class="fas fa-external-link-alt mr-1"></i> Leer más en Opinión
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0 py-5">
                    <div class="card-body text-center">
                        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted font-weight-bold">No hay noticias disponibles</h4>
                        <p class="text-muted">Haga clic en "Actualizar Noticias" para descargar las últimas novedades.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Paginación -->
    @if ($noticias->hasPages())
        <div class="row justify-content-center mt-3 mb-5">
            <div class="col-auto">
                {{ $noticias->links() }}
            </div>
        </div>
    @endif

@endsection

@push('css')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.12) !important;
    }
    .hover-orange:hover {
        color: #dc3545 !important;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    $('#actualizarNoticias').click(function() {
        const button = $(this);
        const originalHtml = button.html();
        
        button.prop('disabled', true);
        button.html('<i class="fas fa-spinner fa-spin mr-1"></i> Actualizando...');
        
        $.ajax({
            url: '{{ route("cuadrillas.noticias.scrape") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Actualizado!',
                        text: response.message || 'Noticias actualizadas correctamente',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    alert(response.message || 'Noticias actualizadas correctamente.');
                    location.reload();
                }
            },
            error: function(xhr) {
                button.prop('disabled', false);
                button.html(originalHtml);
                
                const msg = xhr.responseJSON?.message || 'Hubo un error al actualizar las noticias.';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                } else {
                    alert('Error: ' + msg);
                }
            }
        });
    });
});
</script>
@endpush
