@extends('fusion::layouts.app')

@section('title')
    @hasSection('title')
        @yield('title')
    @elseif(View::hasSection('template_title'))
        @yield('template_title')
    @else
        Sistema de Donaciones
    @endif
@endsection

{{-- El título de página va solo en @section('content_header') dentro del contenido.
     El navbar superior ya muestra "Inicio"; no duplicar el encabezado aquí. --}}

@section('css')
    @yield('css')
    @stack('css')
@endsection

@section('content')
    @yield('content')
@endsection

@section('js')
    @yield('js')
    @stack('js')
@endsection
