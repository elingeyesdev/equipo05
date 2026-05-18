@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} Producto
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} Producto</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.producto.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('inventario::producto.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection






