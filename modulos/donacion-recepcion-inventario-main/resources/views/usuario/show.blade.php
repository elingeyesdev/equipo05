@extends('adminlte::page')

@section('template_title')
    {{ $usuario->name ?? __('Show') . " " . __('Usuario') }}
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Usuario</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('inventario.usuario.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">

                        <div class="form-group mb-2 mb20">
                            <strong>Id Usuario:</strong>
                            {{ $usuario->id_usuario }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Nombres:</strong>
                            {{ $usuario->nombres }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Apellidos:</strong>
                            {{ $usuario->apellidos }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Ci:</strong>
                            {{ $usuario->ci }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Licencia Conducir:</strong>
                            {{ $usuario->licencia_conducir }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Genero:</strong>
                            {{ $usuario->genero }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Correo:</strong>
                            {{ $usuario->correo }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Teléfono:</strong>
                            {{ $usuario->telefono }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Direccion Domicilio:</strong>
                            {{ $usuario->direccion_domicilio }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Estado:</strong>
                            {{ $usuario->estado }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Rol:</strong>
                            {{ $usuario->primary_role_name ?? 'Sin rol' }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Es Recolector:</strong>
                            {{ $usuario->is_recolector ? 'Sí' : 'No' }}
                        </div>
                        <div class="form-group mb-2 mb20">
                            <strong>Fecha Registro:</strong>
                            {{ $usuario->fecha_registro }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection




