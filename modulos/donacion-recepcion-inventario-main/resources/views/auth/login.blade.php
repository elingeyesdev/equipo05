@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php( $login_url = View::hasSection('login_url') ? View::getSection('login_url') : config('adminlte.login_url', 'login') )
@php( $register_url = View::hasSection('register_url') ? View::getSection('register_url') : config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::hasSection('password_reset_url') ? View::getSection('password_reset_url') : config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
@php(    $login_url = $login_url ? route($login_url) : '' )
@php(    $register_url = $register_url ? route($register_url) : '' )
@php(    $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
@php(    $login_url = $login_url ? url($login_url) : '' )
@php(    $register_url = $register_url ? url($register_url) : '' )
@php(    $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', 'Iniciar Sesión')

@section('auth_body')
<form action="{{ $login_url }}" method="post">
    @csrf

    {{-- Email field --}}
    <div class="input-group mb-3">
        <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror"
            value="{{ old('correo') }}" placeholder="Correo Electrónico" autofocus>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
        @error('correo')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Password field --}}
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
            placeholder="Contraseña">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Login field --}}
    <div class="row">
        <div class="col-7">
            <div class="icheck-primary" title="Recuérdame">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">
                    Recuérdame
                </label>
            </div>
        </div>

        <div class="col-5">
            <button type=submit class="btn btn-block btn-primary">
                <span class="fas fa-sign-in-alt"></span>
                Entrar
            </button>
        </div>
    </div>

</form>
@stop

@section('auth_footer')
{{-- Password reset link --}}
@if($password_reset_url)
    <p class="my-0">
        <a href="{{ $password_reset_url }}">
            ¿Olvidaste tu contraseña?
        </a>
    </p>
@endif

{{-- Register link --}}
@if($register_url)
    <p class="my-0">
        <a href="{{ $register_url }}">
            Registrar una nueva cuenta
        </a>
    </p>
@endif
@stop




