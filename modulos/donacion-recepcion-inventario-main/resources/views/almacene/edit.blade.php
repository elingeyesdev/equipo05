@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Almacene
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Update') }} Almacene</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.almacene.update', $almacene->id_almacen) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PUT') }}
                            @csrf

                            @include('inventario::almacene.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection






