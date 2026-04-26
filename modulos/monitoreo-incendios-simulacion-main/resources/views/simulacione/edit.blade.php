@extends('adminlte::page')

@section('template_title')
    {{ __('Update') }} Simulaciones
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Editar') }} Simulaciones</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('simulaciones.update', $simulacione->id) }}"  role="form" enctype="multipart/form-data">
                            {{ method_field('PATCH') }}
                            @csrf

                            @include('simulacione.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
