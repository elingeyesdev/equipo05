<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear cuenta | {{ config('app.name', 'Alas chiquitanas') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('css/platform-login.css') }}?v={{ file_exists(public_path('css/platform-login.css')) ? filemtime(public_path('css/platform-login.css')) : time() }}">
</head>
<body class="hold-transition login-page platform-login">
    <div class="login-page-shell">
        <main class="login-main">
            <div class="login-box">
                <div class="card card-outline card-primary">
                    <div class="card-header text-center">
                        <a href="{{ route('login') }}" class="h1"><b>Alas</b> chiquitanas</a>
                    </div>
                    <div class="card-body">
                        <p class="login-box-msg">Crea tu cuenta de usuario común</p>
                        <p class="small text-muted text-center mb-3">
                            Podrás consultar el mapa general, reportar incendios, ver alertas, registrar donaciones y consultar almacenes y tus propios datos.
                        </p>

                        <form action="{{ route('register') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text"
                                           name="nombre"
                                           class="form-control @error('nombre') is-invalid @enderror"
                                           placeholder="Nombre"
                                           value="{{ old('nombre') }}"
                                           required
                                           autofocus>
                                    @error('nombre')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="text"
                                           name="apellido"
                                           class="form-control @error('apellido') is-invalid @enderror"
                                           placeholder="Apellido"
                                           value="{{ old('apellido') }}"
                                           required>
                                    @error('apellido')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="input-group mb-3">
                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Correo electrónico"
                                       value="{{ old('email') }}"
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                </div>
                                @error('email')
                                    <span class="error invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="input-group mb-3">
                                <input type="text"
                                       name="telefono"
                                       class="form-control @error('telefono') is-invalid @enderror"
                                       placeholder="Teléfono (opcional)"
                                       value="{{ old('telefono') }}">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-phone"></span></div>
                                </div>
                            </div>

                            <div class="input-group mb-3">
                                <input type="password"
                                       name="contrasena"
                                       class="form-control @error('contrasena') is-invalid @enderror"
                                       placeholder="Contraseña (mín. 8 caracteres)"
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                </div>
                                @error('contrasena')
                                    <span class="error invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="input-group mb-3">
                                <input type="password"
                                       name="contrasena_confirmation"
                                       class="form-control"
                                       placeholder="Confirmar contraseña"
                                       required>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus mr-1"></i> Crear cuenta
                            </button>
                        </form>

                        <p class="text-center mb-0 mt-3">
                            <a href="{{ route('login') }}">¿Ya tienes cuenta? Iniciar sesión</a>
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
