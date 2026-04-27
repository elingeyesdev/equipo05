<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galería de paquetes entregados</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body { background: #eef1f4; }
        .wrapper-box { max-width: 1200px; margin: 2rem auto; }
        .card-img-top { height: 200px; object-fit: cover; }
    </style>
</head>
<body>
    <div class="wrapper-box">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Galería de paquetes entregados</h3>
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt mr-1"></i> Iniciar sesión
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($paquetes as $paquete)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                @if(!empty($paquete->imagen))
                                    <img src="data:image/jpeg;base64,{{ base64_encode($paquete->imagen) }}" class="card-img-top" alt="Paquete entregado">
                                @else
                                    <img src="https://via.placeholder.com/400x250?text=Paquete+Entregado" class="card-img-top" alt="Imagen no disponible">
                                @endif
                                <div class="card-body">
                                    <h6 class="mb-1">{{ $paquete->codigo ?? ('PKG-'.$paquete->id_paquete) }}</h6>
                                    <p class="mb-1 text-muted">
                                        Comunidad: {{ $paquete->comunidad ?? 'Sin comunidad' }}
                                    </p>
                                    <p class="mb-0">
                                        Fecha entrega:
                                        {{ $paquete->fecha_entrega ? \Carbon\Carbon::parse($paquete->fecha_entrega)->format('d/m/Y') : 'Pendiente' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted mb-0">No hay paquetes entregados disponibles.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>
