@extends('layouts.app')

@section('subtitle', 'Editar Simulación')
@section('content_header_title', 'Simulaciones')
@section('content_header_subtitle', 'Editar')

@section('content_body')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <x-adminlte-card title="Editar Simulación: {{ $simulacione->nombre }}" theme="warning" icon="fas fa-edit">
                    <x-slot name="toolsSlot">
                        <a href="{{ route('incendios.simulaciones.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Volver</a>
                    </x-slot>

                    <form method="POST" action="{{ route('incendios.simulaciones.update', $simulacione->id) }}" role="form" enctype="multipart/form-data">
                        @method('PATCH')
                        @csrf
                        @include('simulacione.form')
                    </form>
                </x-adminlte-card>
            </div>
        </div>
    </div>
@endsection
