@extends('layouts.app')

@section('title', 'Nuevo usuario — BD rescate')
@section('subtitle', 'Cuenta en la base del submódulo.')
@section('content_header_title', 'Usuario (rescate)')
@section('content_header_subtitle', 'Crear')

@section('content_body')
    <div class="container-fluid page-pad">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-outline card-success shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.5rem;">
                        <h3 class="card-title mb-0"><i class="fas fa-user-plus text-success"></i> Nuevo usuario</h3>
                        <a href="{{ route('rescate.users.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-list"></i> Listado</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('rescate.users.store') }}" role="form" enctype="multipart/form-data">
                            @csrf
                            @include('user.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.page-pad')
@endsection