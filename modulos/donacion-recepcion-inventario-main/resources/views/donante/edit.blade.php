@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Donante
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Donante</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.donante.update', $donante->id_donante) }}" role="form"
                            enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('inventario::donante.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection





