@extends('adminlte::page')

@section('template_title')
    {{ __('Crear') }} Simulaciones
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Crear') }} Simulaciones</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('simulaciones.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('simulacione.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
