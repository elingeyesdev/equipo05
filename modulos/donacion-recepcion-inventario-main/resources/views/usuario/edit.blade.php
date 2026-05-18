@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Usuario
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Usuario</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.usuario.update', $usuario->id_usuario) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('inventario::usuario.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection






