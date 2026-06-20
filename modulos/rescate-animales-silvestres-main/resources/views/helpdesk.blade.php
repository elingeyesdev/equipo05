{{--
    Vista del Widget de Helpdesk
    
    Generada automáticamente por: php artisan helpdeskwidget:install
    
    Personaliza esta vista según las necesidades de tu proyecto.
    Compatible con AdminLTE v3.
--}}
@extends('layouts.app')

@section('title', 'Centro de soporte — Rescate')
@section('subtitle', 'Ayuda y ticket de incidencias.')
@section('content_header_title', 'Centro de soporte')
@section('content_header_subtitle', 'Helpdesk')

@section('content_body')
    
        <div class="row">
            <div class="col-12">
                <div id="helpdesk-widget-wrapper" style="width: 100%;">
                    <x-helpdesk-widget width="100%" />
                </div>
            </div>
        </div>
    </div>

    <style>
        #helpdesk-widget-wrapper iframe {
            width: 100% !important;
            border: none !important;
            display: block;
            min-height: 500px;
            transition: height 0.3s ease;
        }
    </style>

    <script>
        (function() {
            'use strict';

            window.addEventListener('message', function(event) {
                if (event.data.type === 'widget-resize') {
                    const iframe = document.querySelector('#helpdesk-widget-wrapper iframe');
                    if (iframe) {
                        const newHeight = event.data.height;
                        iframe.style.height = newHeight + 'px';
                    }
                }
            });
        })();
    </script>
@endsection