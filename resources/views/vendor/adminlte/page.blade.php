@extends('layouts.app')

@section('title')
    @hasSection('title')
        @yield('title')
    @elseif(View::hasSection('template_title'))
        @yield('template_title')
    @else
        Sistema de Donaciones
    @endif
@endsection

@section('header')
    @hasSection('content_header')
        @yield('content_header')
    @elseif(View::hasSection('template_title'))
        <h1 class="m-0 text-dark">@yield('template_title')</h1>
    @endif
@endsection

@section('content')
    @yield('content')
@endsection

@push('scripts')
    @yield('js')
@endpush
