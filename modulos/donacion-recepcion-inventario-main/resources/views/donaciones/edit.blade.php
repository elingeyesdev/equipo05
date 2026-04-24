@extends('adminlte::page')

@section('template_title')
    Edit Donacione
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <span class="card-title">{{ __('Edit Donación') }}</span>
                    </div>
                    <form method="POST" action="{{ route('inventario.donaciones.update', $donacion->id_donacion) }}" role="form"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            @include('inventario::donaciones.form')
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>
                                {{ __('Actualizar Donación') }}</button>
                            <a href="{{ route('inventario.donaciones.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i>
                                {{ __('Cancelar') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @include('inventario::donaciones.modals')
@endsection





