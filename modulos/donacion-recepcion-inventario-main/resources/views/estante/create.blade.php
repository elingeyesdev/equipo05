@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} Estante
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} Estante</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.estante.store') }}" role="form" enctype="multipart/form-data">
                            @csrf

                            @if(isset($returnUrl) && $returnUrl)
                                <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                            @endif

                            @include('inventario::estante.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection





