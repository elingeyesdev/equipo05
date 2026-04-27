<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titulo }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body { background: #eef1f4; }
        .wrapper-box { max-width: 1100px; margin: 2rem auto; }
    </style>
</head>
<body>
    <div class="wrapper-box">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title mb-0">{{ $titulo }}</h3>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">{{ $subtitulo }}</p>
                <p class="mb-3"><strong>Registros:</strong> {{ $total }}</p>
                <div class="mb-3">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt mr-1"></i> Iniciar sesión
                    </a>
                </div>
                @if(count($columnas) === 0)
                    <p class="text-muted mb-0">No hay datos disponibles por ahora.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    @foreach($columnas as $columna)
                                        <th>{{ $columna }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($filas as $fila)
                                    <tr>
                                        @foreach($columnas as $columna)
                                            <td>{{ $fila->$columna }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ count($columnas) }}" class="text-muted">No hay datos para mostrar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
