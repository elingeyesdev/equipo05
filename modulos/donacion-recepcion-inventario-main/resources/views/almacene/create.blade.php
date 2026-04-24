@extends('adminlte::page')

@section('template_title')
    {{ __('Create') }} Almacene
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} Almacene</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('inventario.almacene.store') }}" role="form"
                            enctype="multipart/form-data">
                            @csrf

                            @if(isset($returnUrl) && $returnUrl)
                                <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                            @endif

                            @include('inventario::almacene.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection





