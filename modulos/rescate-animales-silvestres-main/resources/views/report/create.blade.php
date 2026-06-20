@extends('layouts.app')

@section('title', 'Registrar hallazgo — Rescate')
@section('subtitle', 'Reporte de animal en situación de riesgo.')
@section('content_header_title', 'Hallazgos / reportes')
@section('content_header_subtitle', 'Nuevo reporte')

@if (!Auth::check())
@push('css')
<style>
    .main-sidebar { display: none !important; }
    .content-wrapper { margin-left: 0 !important; }
    .main-header { display: none !important; }
    .navbar { display: none !important; }
</style>
@endpush
@endif

@section('content_body')
    
        <div class="row">
            @if(isset($useFullFormat) && $useFullFormat)
                {{-- Formato completo para usuarios autenticados desde /reports --}}
                <div class="col-md-12">
                    <div class="card card-outline card-success shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                            <h3 class="card-title mb-0"><i class="fas fa-clipboard-list text-success"></i> {{ __('Registrar Hallazgo de Animal en Peligro') }}</h3>
                            <a href="{{ route('rescate.reports.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                        </div>
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <div class="font-weight-bold mb-1">{{ __('No se pudo registrar el hallazgo. Revisa los errores:') }}</div>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('rescate.reports.store') }}"  role="form" enctype="multipart/form-data">
                                @csrf
                                @include('report.form')
                            </form>
                        </div>
                    </div>
                </div>
            @else
                {{-- Formato simple centrado para usuarios desde /landing --}}
                <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                    <div class="card card-outline card-warning shadow-sm">
                        <div class="card-header bg-warning">
                            <span class="card-title">
                                <i class="fas fa-bolt mr-2"></i>
                                {{ __('Registro Rápido de Hallazgo') }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if (!Auth::check())
                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> {{ __('¿Quieres hacer seguimiento?') }}</h5>
                                    <p class="mb-0">
                                        {{ __('Si gustas hacer seguimiento al animalito,') }} 
                                        <a href="{{ route('login') }}" class="alert-link">{{ __('inicia sesión') }}</a> 
                                        {{ __('o') }} 
                                        <a href="{{ route('rescate.register') }}" class="alert-link">{{ __('regístrate') }}</a> 
                                        {{ __('luego de enviar el registro.') }}
                                    </p>
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <div class="font-weight-bold mb-1">{{ __('No se pudo registrar el hallazgo. Revisa los errores:') }}</div>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('rescate.reports.store') }}"  role="form" enctype="multipart/form-data">
                                @csrf
                                @include('report.form')
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
@endsection
