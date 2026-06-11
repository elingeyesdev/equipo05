@extends('adminlte::page')

@section('template_title')
    Editar categoría de donación
@endsection

@section('content')
@include('inventario::partials.flash-messages')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title"><i class="fas fa-edit"></i> Editar categoría de donación</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.categorias-producto.update', $categoriasProducto->id_categoria) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('inventario::categorias-producto.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection






