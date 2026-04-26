@extends('adminlte::page')

@section('template_title')
    {{ __('Crear') }} Biomasa
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-exclamation-triangle"></i> {{ $message }}
                    </div>
                @endif

                <div class="card card-default">
                    <div class="card-header">
                        <span class="card-title">{{ __('Create') }} Biomasa</span>
                    </div>
                    <div class="card-body bg-white">
                        <form method="POST" action="{{ route('biomasas.store') }}"  role="form" enctype="multipart/form-data">
                            @csrf

                            @include('biomasa.form')

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
