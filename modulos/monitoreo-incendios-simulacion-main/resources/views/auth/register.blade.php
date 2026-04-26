@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
    }
@endphp

@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
<form action="{{ $registerUrl }}" method="post">
    @csrf

    {{-- Name field --}}
    <div class="input-group mb-3">
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name') }}" placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-user"></span>
            </div>
        </div>
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Email field --}}
    <div class="input-group mb-3">
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
        </div>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Teléfono field --}}
    <div class="input-group mb-3">
        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono') }}" placeholder="Teléfono">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-phone"></span>
            </div>
        </div>
        @error('telefono')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Cédula de Identidad field --}}
    <div class="input-group mb-3">
        <input type="text" name="cedula_identidad" class="form-control @error('cedula_identidad') is-invalid @enderror"
               value="{{ old('cedula_identidad') }}" placeholder="Cédula de Identidad (C.I.)">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-id-card"></span>
            </div>
        </div>
        @error('cedula_identidad')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Dirección field --}}
    <div class="input-group mb-3">
        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror"
               value="{{ old('direccion') }}" placeholder="Dirección">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-map-marker-alt"></span>
            </div>
        </div>
        @error('direccion')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Ciudad field --}}
    <div class="input-group mb-3">
        <input type="text" name="ciudad" class="form-control @error('ciudad') is-invalid @enderror"
               value="{{ old('ciudad') }}" placeholder="Ciudad">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-city"></span>
            </div>
        </div>
        @error('ciudad')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Zona field --}}
    <div class="input-group mb-3">
        <input type="text" name="zona" class="form-control @error('zona') is-invalid @enderror"
               value="{{ old('zona') }}" placeholder="Zona">
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-map-pin"></span>
            </div>
        </div>
        @error('zona')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Password field --}}
    <div class="input-group mb-3">
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               placeholder="{{ __('adminlte::adminlte.password') }}" required>
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

    {{-- Confirm password field --}}
    <div class="input-group mb-3">
        <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror"
               placeholder="{{ __('adminlte::adminlte.retype_password') }}" required>
        <div class="input-group-append">
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
        </div>
        @error('password_confirmation')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    {{-- Register button --}}
    <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
        <span class="fas fa-user-plus"></span>
        {{ __('adminlte::adminlte.register') }}
    </button>

</form>

{{-- Google OAuth Button --}}
<div class="social-auth-links text-center mt-3">
    <p class="text-muted">- O -</p>
    <a href="{{ route('incendios.google.redirect') }}" class="btn btn-danger btn-block">
        <i class="fab fa-google mr-2"></i> Registrarse con Google
    </a>
</div>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $loginUrl }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>
@stop
