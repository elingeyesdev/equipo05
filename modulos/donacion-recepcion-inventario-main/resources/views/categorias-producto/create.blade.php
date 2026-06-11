@extends('adminlte::page')

@section('template_title')
    Registrar categoría de donación
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">Registrar categoría de donación</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.categorias-producto.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('inventario::categorias-producto.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection






