<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión | {{ config('app.name', 'Alas chiquitanas') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/icheck-bootstrap/3.0.1/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('css/platform-login.css') }}?v={{ file_exists(public_path('css/platform-login.css')) ? filemtime(public_path('css/platform-login.css')) : time() }}">
</head>
<body class="hold-transition login-page platform-login">
    <div class="login-page-shell">
        <main class="login-main">
            <div class="login-box">
                <div class="card card-outline card-primary">
                    <div class="card-header text-center">
                        <a href="#" class="h1"><b>Alas</b> chiquitanas</a>
                    </div>
                    <div class="card-body">
                        <p class="login-box-msg">Ingresa tus credenciales para iniciar sesión</p>

                        <form action="{{ route('login') }}" method="post">
                            @csrf
                            <div class="input-group mb-3">
                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Correo electrónico"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                                @error('email')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="input-group mb-3">
                                <input type="password"
                                       name="contrasena"
                                       class="form-control @error('contrasena') is-invalid @enderror"
                                       placeholder="Contraseña"
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                                @error('contrasena')
                                    <span class="error invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-8">
                                    <div class="icheck-primary">
                                        <input type="checkbox" id="remember" name="remember">
                                        <label for="remember">Recuérdame</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                                </div>
                            </div>
                        </form>

                        <div class="login-register-cta mt-3 pt-3 border-top">
                            <p class="small text-muted text-center mb-2">¿Eres ciudadano y aún no tienes cuenta?</p>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-user-plus mr-1"></i> Crear cuenta
                            </a>
                            <p class="small text-muted text-center mb-0 mt-2">
                                Reportar incendios, donar y ver solo tus datos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="login-public-footer">
            <p class="footer-title text-muted">Accesos públicos</p>
            <div class="footer-links">
                <a href="{{ route('publico.logistica.solicitud') }}" class="btn landing-access-btn">
                    <i class="fas fa-hands-helping mr-2"></i>Solicitar ayuda
                </a>
                <a href="{{ route('publico.logistica.galeria') }}" class="btn landing-access-btn">
                    <i class="fas fa-images mr-2"></i>Galería de paquetes entregados
                </a>
                <a href="{{ route('publico.cuadrillas.reporte') }}" class="btn landing-access-btn">
                    <i class="fas fa-bullhorn mr-2"></i>Reporte Público
                </a>
                <a href="{{ route('publico.cuadrillas.mapa') }}" class="btn landing-access-btn">
                    <i class="fas fa-fire mr-2"></i>Mapa en Tiempo Real
                </a>
                <a href="{{ route('publico.seguimiento.info') }}" class="btn landing-access-btn">
                    <i class="fas fa-user-friends mr-2"></i>Seguimiento Voluntarios
                </a>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
