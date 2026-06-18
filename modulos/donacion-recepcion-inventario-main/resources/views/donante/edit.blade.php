@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Donante
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Donante</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ !empty($esPerfilPropio) ? route('inventario.donante.mi-perfil.update') : route('inventario.donante.update', $donante->id_donante) }}" role="form"
                            enctype="multipart/form-data">
                            {{ !empty($esPerfilPropio) ? method_field('PUT') : method_field('PATCH') }}
                            @csrf

                            @include('inventario::donante.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection





