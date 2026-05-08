@extends('layouts.app')

@section('title', 'Nueva liberación — Rescate')
@section('subtitle', 'Registrar liberación de un animal.')
@section('content_header_title', 'Liberaciones')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-dove text-success"></i> Nueva liberación</h3>
                        <a href="{{ route('rescate.releases.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('rescate.releases.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('release.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection
