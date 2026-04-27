<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitar ayuda</title>
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
                <h3 class="card-title mb-0">Solicitar ayuda</h3>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('publico.logistica.solicitud.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Apellido</label>
                            <input type="text" name="apellido" class="form-control" value="{{ old('apellido') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>CI</label>
                            <input type="text" name="ci" class="form-control" value="{{ old('ci') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Telefono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Comunidad</label>
                            <input type="text" name="comunidad" class="form-control" value="{{ old('comunidad') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Provincia</label>
                            <input type="text" name="provincia" class="form-control" value="{{ old('provincia') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Direccion</label>
                            <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tipo de Emergencia</label>
                            <input type="text" name="tipo_emergencia" class="form-control" value="{{ old('tipo_emergencia') }}" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Cantidad Personas</label>
                            <input type="number" name="cantidad_personas" min="1" class="form-control" value="{{ old('cantidad_personas') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Fecha Necesidad</label>
                            <input type="date" name="fecha_necesidad" class="form-control" value="{{ old('fecha_necesidad') }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Insumos Necesarios</label>
                            <textarea name="insumos_necesarios" rows="4" class="form-control">{{ old('insumos_necesarios') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex" style="gap:.5rem;">
                        <a href="{{ route('login') }}" class="btn btn-secondary">Volver</a>
                        <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
