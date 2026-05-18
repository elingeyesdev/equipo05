@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Espacio
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Espacio</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.espacio.update', $espacio->id_espacio) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('inventario::espacio.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection






