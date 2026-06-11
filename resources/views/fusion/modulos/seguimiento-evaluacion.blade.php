@extends('layouts.app')

@section('title', 'Evaluación Post-Incendio')

@section('css')
<style>
  :root {
    --color-amarillo: #FFA726;
    --color-amarillo-hover: #FB8C00;
    --color-card: #ffffff;
    --color-texto-principal: #333333;
    --color-blanco: #f8f9fa;
    --color-azul: #007bff;
    --color-gris :#6c757d;
  }

  /* Contenedor general tipo React */
  .form-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 20px 0;
    width: 100%;
    min-height: 100vh;
  }

  .form-content {
    padding: 0;
    width: 100%;
    max-width: 1200px;
  }

  .form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 15px;
  }

  .form-titulo {
    font-size: 2.5rem;
    padding-bottom: 0.5rem;
    position: relative;
    margin: 0;
    font-weight: bold;
  }

  .form-titulo::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 30%;
    height: 3px;
    background: var(--color-azul);
  }

  .btns {
    display: flex;
    gap: 12px;
  }

  .btn-agregar-pregunta {
    background-color: var(--color-azul);
    color: white;
    font-weight: 600;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform .2s, box-shadow .2s;
  }

  .btn-agregar-pregunta:hover {
    background-color: var(--color-azul);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
  }

  .step-hidden { display:none; }

  /* Tarjetas suaves como en React */
  .card-soft {
    border: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    border-radius: 12px;
    overflow: hidden;
    background: var(--color-card);
  }

  .card-soft .card-header {
    border-bottom: 0;
    padding: 1rem 1.5rem;
  }

  .card-soft .card-body {
    padding: 1.5rem 1.8rem 1.8rem;
  }

  /* Etiquetas de pregunta */
  .q-row {
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    background-color: var(--color-card);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background-color .3s;
  }

  .q-row:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 14px rgba(0,0,0,0.12);
    background-color: var(--color-blanco);
  }

  .q-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--color-texto-principal);
    line-height: 1.6;
    margin-bottom: .5rem;
    border-bottom: 2px solid rgba(0,0,0,0.08);
    padding-bottom: .35rem;
  }

  /* Radios en fila tipo “chips” */
  .icheck-inline {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
  }

  .icheck-inline .icheck-primary,
  .icheck-inline .icheck-success,
  .icheck-inline .icheck-info,
  .icheck-inline .icheck-danger,
  .icheck-inline .icheck-warning {
    margin-right: 0;
    flex: 1;
    min-width: 130px;
  }

  .icheck-inline label {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px 18px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #fff;
    font-weight: 500;
    font-size: 14px;
    text-align: center;
    width: 100%;
    position: relative;
    user-select: none;
  }


  .icheck-inline input[type="radio"] {
    transform: scale(1.1);
    cursor: pointer;
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
  }

  .icheck-inline label:hover {
    border-color: #28a745;
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.15);
  }

  /* Estado seleccionado - Success (verde) por defecto */
  .icheck-success input[type="radio"]:checked + label {
    border-color: #28a745;
    background-color: #d4edda;
    color: #155724;
    font-weight: 600;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
  }

  .icheck-success input[type="radio"]:checked + label::before {
    content: '✓';
    position: absolute;
    top: 5px;
    right: 8px;
    width: 22px;
    height: 22px;
    background-color: #28a745;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 13px;
  }

  /* Variantes de colores */
  .icheck-info input[type="radio"]:checked + label {
    border-color: #17a2b8;
    background-color: #d1ecf1;
    color: #0c5460;
  }

  .icheck-info input[type="radio"]:checked + label::before {
    background-color: #17a2b8;
  }

  .icheck-info label:hover {
    border-color: #17a2b8;
    box-shadow: 0 4px 8px rgba(23, 162, 184, 0.15);
  }

  .icheck-primary input[type="radio"]:checked + label {
    border-color: var(--color-azul);
    background-color: #e7f3ff;
    color: #004085;
  }

  .icheck-primary input[type="radio"]:checked + label::before {
    background-color: var(--color-azul);
  }

  .icheck-primary label:hover {
    border-color: var(--color-azul);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.15);
  }

  .icheck-warning input[type="radio"]:checked + label {
    border-color: #ffc107;
    background-color: #fff3cd;
    color: #856404;
  }

  .icheck-warning input[type="radio"]:checked + label::before {
    background-color: #ffc107;
  }

  .icheck-warning label:hover {
    border-color: #ffc107;
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.15);
  }

  .icheck-danger input[type="radio"]:checked + label {
    border-color: #dc3545;
    background-color: #f8d7da;
    color: #721c24;
  }

  .icheck-danger input[type="radio"]:checked + label::before {
    background-color: #dc3545;
  }

  .icheck-danger label:hover {
    border-color: #dc3545;
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.15);
  }

  /* Botones de acciones en cada pregunta */
  .acciones {
    margin-top: 10px;
    display: flex;
    gap: 12px;
  }

  .acciones .btn-editar,
  .acciones .btn-eliminar {
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .acciones .btn-editar {
    background-color: var(--color-gris);
    color: #fff;
  }

  .acciones .btn-editar:hover {
    background-color: #5a6268;
  }

  .acciones .btn-eliminar {
    background-color: var(--color-azul);
    color: #fff;
  }

  .acciones .btn-eliminar:hover {
    background-color: #0056b3;
  }

  /* Botones navegación */
  .btn-formulario-nav,
  .btn-enviar {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-formulario-nav {
    background-color: var(--color-azul);
    color: white;
  }

  .btn-enviar {
    background-color: var(--color-azul);
    color: white;
  }

  .btn-formulario-nav:hover,
  .btn-enviar:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    background-color: #0056b3;
  }

  /* ================= NO TOCAR: SECCIÓN CUERPO HUMANO ================= */

  .seleccion-cuerpo-box {
      padding: 1.5rem;
      border-radius: 12px;
      height: auto;
      min-height: 600px;
      transition: transform 0.3s, box-shadow 0.3s;
      margin-bottom: 2rem;
      background-color: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border: 1px solid #e9ecef;
      margin-top: 2rem;
  }

  .seleccion-cuerpo-label {
      font-size: 1.2rem;
      font-weight: bold;
      color: var(--color-texto-principal);
      display: block;
      margin-bottom: 1.5rem;
  }

  .human-body {
      width: 207px;
      position: relative;
      height: 550px;
      display: block;
      margin: 40px auto;
      padding: 0;
  }

  .human-body svg:hover {
      cursor: pointer;
      opacity: 0.8;
  }

  .human-body svg {
      position: absolute;
      left: 50%;
      fill: #424242;
      transition: fill 0.2s ease;
  }

  .human-body svg#head {
      margin-left: -28.297px;
      top: 0;
  }

  .human-body svg#left-shoulder {
      margin-left: -54.766px;
      top: 75px;
  }

  .human-body svg#right-shoulder {
      margin-left: 13.297px;
      top: 75px;
  }

  .human-body svg#left-arm {
      margin-left: -78.172px;
      top: 118px;
  }

  .human-body svg#right-arm {
      margin-left: 40px;
      top: 118px;
      z-index: 10001;
  }

  .human-body svg#chest {
      margin-left: -43.297px;
      top: 94px;
  }

  .human-body svg#stomach {
      margin-left: -37.625px;
      top: 136px;
  }

  .human-body svg#left-leg {
      margin-left: -46.813px;
      top: 211px;
      z-index: 9999;
  }

  .human-body svg#right-leg {
      margin-left: 0;
      top: 211px;
      z-index: 9999;
  }

  .human-body svg#left-hand {
      margin-left: -105px;
      top: 230px;
  }

  .human-body svg#right-hand {
      margin-left: 70px;
      top: 230px;
      z-index: 10000;
  }

  .human-body svg#left-foot {
      margin-left: -35px;
      top: 461px;
  }

  .human-body svg#right-foot {
      margin-left: 0;
      top: 461px;
  }

  @media (max-width: 768px) {
    .form-content {
      padding: 0;
    }

    .form-titulo {
      font-size: 2rem;
    }

    .icheck-inline {
      flex-direction: column;
      gap: 8px;
    }

    .icheck-inline .icheck-success,
    .icheck-inline .icheck-info,
    .icheck-inline .icheck-primary,
    .icheck-inline .icheck-warning,
    .icheck-inline .icheck-danger {
      min-width: 100%;
    }

    .icheck-inline label {
      padding: 12px 15px;
      font-size: 13px;
    }
  }
</style>
@endsection

@section('content')
<div class="form-container">
  <div class="form-content">

    {{-- HEADER --}}
    <div class="form-header">
      <h1 class="form-titulo">Evaluación Post-Incendio</h1>
      <div class="btns">
        <a href="{{ route('seguimiento.dashboard') }}" class="btn btn-outline-primary mr-2 d-flex align-items-center">
          <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
        <button type="button" class="btn-agregar-pregunta" id="btn-open-modal-pregunta">
          + Agregar Pregunta
        </button>
      </div>
    </div>

    {{-- ===================== PASO 1: FÍSICO ===================== --}}
    <div id="step-fisico">
      <div class="card card-soft card-primary mb-4">
        <div class="card-header bg-primary text-white">
          <h3 class="card-title m-0">Evaluación Física</h3>
        </div>
        <div class="card-body">

          @php
            $opciones = ['Nunca','Raramente','A veces','Frecuentemente','Siempre'];
            $pregF = [
              ['id'=>'f1','t'=>'¿Te sientes más cansado o agotado de lo habitual después de las intervenciones?'],
              ['id'=>'f2','t'=>'¿Has notado quemaduras, irritación o enrojecimiento en la piel después de las intervenciones?'],
              ['id'=>'f3','t'=>'¿Has tenido dificultades para respirar o tos después de las intervenciones?'],
              ['id'=>'f4','t'=>'¿Tienes dolor o molestias en el pecho desde el incendio?'],
              ['id'=>'f5','t'=>'¿Has experimentado palpitaciones o un ritmo cardíaco irregular después de la intervención?'],
              ['id'=>'f6','t'=>'¿Tus ojos han estado irritados, con ardor o picazón desde la intervención?'],
              ['id'=>'f7','t'=>'¿Tienes dificultad para respirar profundamente desde la intervención?'],
              ['id'=>'f8','t'=>'¿Has notado que tu nariz está congestionada o bloqueada más de lo normal?'],
            ];
          @endphp

          <div id="preguntas-fisico">
            @foreach($pregF as $pf)
              <div class="q-row" data-tipo="fisico">
                <div class="q-label">{{ $pf['t'] }}</div>
                <div class="icheck-inline">
                  @foreach($opciones as $idx => $opt)
                    @php $rid = $pf['id'].'_'.$idx; @endphp
                    <div class="icheck-success d-inline">
                      <input type="radio" id="{{ $rid }}" name="{{ $pf['id'] }}">
                      <label for="{{ $rid }}">{{ $opt }}</label>
                    </div>
                  @endforeach
                </div>
                <div class="acciones">
                  <button type="button" class="btn-editar">Editar</button>
                  <button type="button" class="btn-eliminar">Eliminar</button>
                </div>
              </div>
            @endforeach
          </div>

          {{-- ============ SELECTOR DE CONDICIÓN DEL CUERPO ============ --}}
          <div class="seleccion-cuerpo-box">
            <label class="seleccion-cuerpo-label">Selección de condición del cuerpo</label>
            <div class="human-body">
              <svg data-position="head" id="head" class="head" xmlns="http://www.w3.org/2000/svg" width="56.594" height="95.031" viewBox="0 0 56.594 95.031"><path d="M15.92 68.5l8.8 12.546 3.97 13.984-9.254-7.38-4.622-15.848zm27.1 0l-8.8 12.546-3.976 13.988 9.254-7.38 4.622-15.848zm6.11-27.775l.108-11.775-21.16-14.742L8.123 26.133 8.09 40.19l-3.24.215 1.462 9.732 5.208 1.81 2.36 11.63 9.72 11.018 10.856-.324 9.56-10.37 1.918-11.952 5.207-1.81 1.342-9.517zm-43.085-1.84l-.257-13.82L28.226 11.9l23.618 15.755-.216 10.37 4.976-17.085L42.556 2.376 25.49 0 10.803 3.673.002 24.415z" style="fill: #424242;"></path></svg>
              <svg data-position="left-shoulder" id="left-shoulder" class="left-shoulder" xmlns="http://www.w3.org/2000/svg" width="109.532" height="46.594" viewBox="0 0 109.532 46.594"><path d="m 38.244,-0.004 1.98,9.232 -11.653,2.857 -7.474,-2.637 z M 17.005,10.536 12.962,8.35 0.306,22.35 0.244,27.675 c 0,0 16.52,-17.015 16.764,-17.14 z m 1.285,0.58 C 18.3,11.396 0.528,30.038 0.528,30.038 L -0.01,46.595 6.147,36.045 18.017,30.989 26.374,15.6 Z" style="fill: #424242;"></path></svg>
              <svg data-position="right-shoulder" id="right-shoulder" class="right-shoulder" xmlns="http://www.w3.org/2000/svg" width="109.532" height="46.594" viewBox="0 0 109.532 46.594"><path d="m 3.2759972,-0.004 -1.98,9.232 11.6529998,2.857 7.473999,-2.637 z m 21.2379988,10.54 4.044,-2.187 12.656,14 0.07,5.33 c 0,0 -16.524,-17.019 -16.769,-17.144 z m -1.285,0.58 c -0.008,0.28 17.762,18.922 17.762,18.922 l 0.537,16.557 -6.157,-10.55 -11.871,-5.057 L 15.147997,15.6 Z" style="fill: #424242;"></path></svg>
              <svg data-position="left-arm" id="left-arm" class="left-arm" xmlns="http://www.w3.org/2000/svg" width="156.344" height="119.25" viewBox="0 0 156.344 119.25"><path d="m21.12,56.5a1.678,1.678 0 0 1 -0.427,0.33l0.935,8.224l12.977,-13.89l1.2,-8.958a168.2,168.2 0 0 0 -14.685,14.294zm1.387,12.522l-18.07,48.91l5.757,1.333l19.125,-39.44l3.518,-22.047l-10.33,11.244zm-5.278,-18.96l2.638,18.74l-17.2,46.023l-2.657,-1.775l6.644,-35.518l10.575,-27.47zm18.805,-12.323a1.78,1.78 0 0 1 0.407,-0.24l3.666,-27.345l-7.037,-10.139l-7.258,10.58l-6.16,37.04l0.566,4.973a151.447,151.447 0 0 1 15.808,-14.87l0.008,0.001zm-13.742,-28.906l-3.3,35.276l-2.2,-26.238l5.5,-9.038z" style="fill: #424242;"></path></svg>
              <svg data-position="right-arm" id="right-arm" class="right-arm" xmlns="http://www.w3.org/2000/svg" width="156.344" height="119.25" viewBox="0 0 156.344 119.25"><path d="m 18.997,56.5 a 1.678,1.678 0 0 0 0.427,0.33 L 18.489,65.054 5.512,51.164 4.312,42.206 A 168.2,168.2 0 0 1 18.997,56.5 Z m -1.387,12.522 18.07,48.91 -5.757,1.333 L 10.798,79.825 7.28,57.778 17.61,69.022 Z m 5.278,-18.96 -2.638,18.74 17.2,46.023 2.657,-1.775 L 33.463,77.532 22.888,50.062 Z M 4.083,37.739 A 1.78,1.78 0 0 0 3.676,37.499 L 0.01,10.154 7.047,0.015 l 7.258,10.58 6.16,37.04 -0.566,4.973 A 151.447,151.447 0 0 0 4.091,37.738 l -0.008,10e-4 z m 13.742,-28.906 3.3,35.276 2.2,-26.238 -5.5,-9.038 z" style="fill: #424242;"></path></svg>
              <svg data-position="chest" id="chest" class="chest" xmlns="http://www.w3.org/2000/svg" width="86.594" height="45.063" viewBox="0 0 86.594 45.063"><path d="M19.32 0l-9.225 16.488-10.1 5.056 6.15 4.836 4.832 14.07 11.2 4.616 17.85-8.828-4.452-34.7zm47.934 0l9.225 16.488 10.1 5.056-6.15 4.836-4.833 14.07-11.2 4.616-17.844-8.828 4.45-34.7z" style="fill: #424242;"></path></svg>
              <svg data-position="stomach" id="stomach" class="stomach" xmlns="http://www.w3.org/2000/svg" width="75.25" height="107.594" viewBox="0 0 75.25 107.594"><path d="M19.25 7.49l16.6-7.5-.5 12.16-14.943 7.662zm-10.322 8.9l6.9 3.848-.8-9.116zm5.617-8.732L1.32 2.15 6.3 15.6zm-8.17 9.267l9.015 5.514 1.54 11.028-8.795-5.735zm15.53 5.89l.332 8.662 12.286-2.665.664-11.826zm14.61 84.783L33.28 76.062l-.08-20.53-11.654-5.736-1.32 37.5zM22.735 35.64L22.57 46.3l11.787 3.166.166-16.657zm-14.16-5.255L16.49 35.9l1.1 11.25-8.8-7.06zm8.79 22.74l-9.673-7.28-.84 9.78L-.006 68.29l10.564 14.594 5.5.883 1.98-20.735zM56 7.488l-16.6-7.5.5 12.16 14.942 7.66zm10.32 8.9l-6.9 3.847.8-9.116zm-5.617-8.733L73.93 2.148l-4.98 13.447zm8.17 9.267l-9.015 5.514-1.54 11.03 8.8-5.736zm-15.53 5.89l-.332 8.662-12.285-2.665-.664-11.827zm-14.61 84.783l3.234-31.536.082-20.532 11.65-5.735 1.32 37.5zm13.78-71.957l.166 10.66-11.786 3.168-.166-16.657zm14.16-5.256l-7.915 5.514-1.1 11.25 8.794-7.06zm-8.79 22.743l9.673-7.28.84 9.78 6.862 12.66-10.564 14.597-5.5.883-1.975-20.74z" style="fill: #424242;"></path></svg>
              <svg data-position="left-leg" id="left-leg" class="left-leg" xmlns="http://www.w3.org/2000/svg" width="93.626" height="250.625" viewBox="0 0 93.626 250.625"><path d="m 18.00179,139.99461 -0.664,5.99 4.647,5.77 1.55,9.1 3.1,1.33 2.655,-13.755 1.77,-4.88 -1.55,-3.107 z m 20.582,0.444 -3.32,9.318 -7.082,13.755 1.77,12.647 5.09,-14.2 4.205,-7.982 z m -26.557,-12.645 5.09,27.29 -3.32,-1.777 -2.656,8.875 z m 22.795,42.374 -1.55,4.88 -3.32,20.634 -0.442,27.51 4.65,26.847 -0.223,-34.39 4.87,-13.754 0.663,-15.087 z m -10.623,12.424 1.106,41.267 c 14.157565,64.57987 -5.846437,10.46082 -16.8199998,-29.07 l 5.5329998,-36.384 z m -9.71,-178.164003 0,22.476 15.71,31.073 9.923,30.850003 -1.033,-21.375 z m 25.49,30.248 0.118,-0.148 -0.793,-2.024 -16.545,-18.16 -1.242,-0.44 10.984,28.378 z m -6.255,10.766 6.812,17.6 2.274,-21.596 -1.344,-3.43 z m -26.4699998,17.82 0.827,25.340003 12.8159998,35.257 -3.928,10.136 -12.6099998,-44.51 z M 31.81879,76.04161 l 0.345,0.826 6.47,15.48 -4.177,38.342 -6.594,-3.526 5.715,-35.7 z m -21.465,-74.697003 0.827,21.373 L 4.1527902,65.02561 0.84679017,30.870607 Z m 2.068,27.323 14.677,32.391 3.307,26.000003 -6.2,36.58 -13.437,-37.241 -0.8269998,-38.342003 z" style="fill: #424242;"></path></svg>
              <svg data-position="right-leg" id="right-leg" class="right-leg" xmlns="http://www.w3.org/2000/svg" width="80" height="250.625" viewBox="0 0 80 250.625"><path d="m 26.664979,139.7913 0.663,5.99 -4.647,5.77 -1.55,9.1 -3.1,1.33 -2.655,-13.755 -1.77,-4.88 1.55,-3.107 z m -20.5820002,0.444 3.3200005,9.318 7.0799997,13.755 -1.77,12.647 -5.0899997,-14.2 -4.2000005,-7.987 z m 3.7620005,29.73 1.5499997,4.88 3.32,20.633 0.442,27.51 -4.648,26.847 0.22,-34.39 -4.8670002,-13.754 -0.67,-15.087 z m 10.6229997,12.424 -1.107,41.267 -8.852,33.28 9.627,-4.55 16.046,-57.8 -5.533,-36.384 z m -13.9460002,74.991 c -5.157661,19.45233 -2.5788305,9.72616 0,0 z M 30.177979,4.225305 l 0,22.476 -15.713,31.072 -9.9230002,30.850005 1.033,-21.375005 z m -25.4930002,30.249 -0.118,-0.15 0.793,-2.023 16.5450002,-18.16 1.24,-0.44 -10.98,28.377 z m 6.2550002,10.764 -6.8120002,17.6 -2.274,-21.595 1.344,-3.43 z m 26.47,17.82 -0.827,25.342005 -12.816,35.25599 3.927,10.136 12.61,-44.50999 z m -24.565,12.783005 -0.346,0.825 -6.4700002,15.48 4.1780002,38.34199 6.594,-3.527 -5.715,-35.69999 z m 19.792,51.74999 -5.09,27.29 3.32,-1.776 2.655,8.875 z m 1.671,-126.452995 -0.826,21.375 7.03,42.308 3.306,-34.155 z m -2.066,27.325 -14.677,32.392 -3.308,26.000005 6.2,36.57999 13.436,-37.23999 0.827,-38.340005 z" style="fill: #424242;"></path></svg>
              <svg data-position="left-hand" id="left-hand" class="left-hand" xmlns="http://www.w3.org/2000/svg" width="90" height="38.938" viewBox="0 0 90 38.938"><path d="m 21.255,-0.00198191 2.88,6.90000201 8.412,1.335 0.664,12.4579799 -4.427,17.8 -2.878,-0.22 2.8,-11.847 -2.99,-0.084 -4.676,12.6 -3.544,-0.446 4.4,-12.736 -3.072,-0.584 -5.978,13.543 -4.428,-0.445 6.088,-14.1 -2.1,-1.25 L 4.878,34.934 1.114,34.489 12.4,12.9 11.293,11.12 0.665,15.57 0,13.124 8.635,5.3380201 Z" style="fill: #424242;"></path></svg>
              <svg data-position="right-hand" id="right-hand" class="right-hand" xmlns="http://www.w3.org/2000/svg" width="90" height="38.938" viewBox="0 0 90 38.938"><path d="m 13.793386,-0.00198533 -2.88,6.90000163 -8.4120002,1.335 -0.664,12.4579837 4.427,17.8 2.878,-0.22 -2.8,-11.847 2.99,-0.084 4.6760002,12.6 3.544,-0.446 -4.4,-12.736 3.072,-0.584 5.978,13.543 4.428,-0.445 -6.088,-14.1 2.1,-1.25 7.528,12.012 3.764,-0.445 -11.286,-21.589 1.107,-1.78 10.628,4.45 0.665,-2.447 -8.635,-7.7859837 z" style="fill: #424242;"></path></svg>
              <svg data-position="left-foot" id="left-foot" class="left-foot" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path d="m 19.558357,1.92821 c -22.1993328,20.55867 -11.0996668,10.27933 0,0 z m 5.975,5.989 -0.664,18.415 -1.55,6.435 -4.647,0 -1.327,-4.437 -1.55,-0.222 0.332,4.437 -5.864,-1.778 -1.5499998,-0.887 -6.64,-1.442 -0.22,-5.214 6.418,-10.87 4.4259998,-5.548 c 9.991542,-3.26362 9.41586,-8.41457 12.836,1.111 z" style="fill: #424242;"></path></svg>
              <svg data-position="right-foot" id="right-foot" class="right-foot" xmlns="http://www.w3.org/2000/svg" width="90" height="38.938" viewBox="0 0 90 38.938"><path d="m 11.723492,2.35897 c -40.202667,20.558 -20.1013335,10.279 0,0 z m -5.9740005,5.989 0.663,18.415 1.546,6.435 4.6480005,0 1.328,-4.437 1.55,-0.222 -0.333,4.437 5.863,-1.778 1.55,-0.887 6.638,-1.442 0.222,-5.214 -6.418,-10.868 -4.426,-5.547 -10.8440005,-4.437 z" style="fill: #424242;"></path></svg>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-3">
            <button id="btn-next" type="button" class="btn-formulario-nav">
              Siguiente
            </button>
          </div>

        </div>
      </div>
    </div>

    {{-- ===================== PASO 2: PSICOLÓGICO ===================== --}}
    <div id="step-psico" class="step-hidden">
      <div class="card card-soft card-info mb-4">
        <div class="card-header bg-info text-white">
          <h3 class="card-title m-0">Evaluación Psicológica</h3>
        </div>
        <div class="card-body">

          @php
            $pregP = [
              ['id'=>'p1','t'=>'¿Con qué frecuencia has tenido pensamientos no deseados relacionados al incendio?'],
              ['id'=>'p2','t'=>'¿Sientes que últimamente piensas en qué pudiste hacer diferente durante la intervención?'],
              ['id'=>'p3','t'=>'¿Has notado disminución de apetito desde la intervención?'],
              ['id'=>'p4','t'=>'¿Te resulta difícil relajarte o desconectar mentalmente después de las intervenciones?'],
              ['id'=>'p5','t'=>'¿Has tenido dificultades para concentrarte en tus tareas diarias debido al estrés?'],
              ['id'=>'p6','t'=>'¿Has sufrido de insomnio recientemente?'],
              ['id'=>'p7','t'=>'¿Te has sentido emocionalmente más inestable o irritable desde el incendio?'],
              ['id'=>'p8','t'=>'¿Te sientes preocupado o ansioso constantemente desde el incendio?'],
            ];
          @endphp

          <div id="preguntas-psico">
            @foreach($pregP as $pp)
              <div class="q-row" data-tipo="psicologico">
                <div class="q-label">{{ $pp['t'] }}</div>
                <div class="icheck-inline">
                  @foreach($opciones as $idx => $opt)
                    @php $rid = $pp['id'].'_'.$idx; @endphp
                    <div class="icheck-info d-inline">
                      <input type="radio" id="{{ $rid }}" name="{{ $pp['id'] }}">
                      <label for="{{ $rid }}">{{ $opt }}</label>
                    </div>
                  @endforeach
                </div>
                <div class="acciones">
                  <button type="button" class="btn-editar">Editar</button>
                  <button type="button" class="btn-eliminar">Eliminar</button>
                </div>
              </div>
            @endforeach
          </div>

          <div class="d-flex justify-content-between mt-3">
            <button id="btn-prev" type="button" class="btn btn-secondary">
              Anterior
            </button>
            <button id="btn-send" type="button" class="btn-enviar">
              Enviar
            </button>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

{{-- ================= MODALES ================= --}}

{{-- Modal Agregar / Editar Pregunta --}}
<div class="modal fade" id="modalPregunta" tabindex="-1" role="dialog" aria-labelledby="modalPreguntaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalPreguntaLabel">Agregar Pregunta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="modalTipo">Tipo</label>
          <select id="modalTipo" class="form-control">
            <option value="fisico">Físico</option>
            <option value="psicologico">Psicológico</option>
          </select>
        </div>

        <div class="form-group mt-3">
          <label for="modalTexto">
            Pregunta
            <span class="text-muted ml-2" style="font-size: .85em;">
              (<span id="modalContador">0</span>/250)
            </span>
          </label>
          <textarea id="modalTexto" class="form-control" rows="3" maxlength="250" placeholder="Escriba la pregunta..."></textarea>
          <small id="modalAvisoLimite" class="form-text text-warning d-none">
            Has alcanzado el límite máximo de caracteres.
          </small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="modalGuardarBtn" disabled>Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Confirmar Eliminación --}}
<div class="modal fade" id="modalEliminarPregunta" tabindex="-1" role="dialog" aria-labelledby="modalEliminarPreguntaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEliminarPreguntaLabel">Confirmar Eliminación</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        ¿Estás seguro que quieres eliminar esta pregunta?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="modalEliminarConfirmBtn">Eliminar</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Error --}}
<div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="modalErrorLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalErrorLabel">Error</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Debes responder todas las preguntas antes de enviar.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>

{{-- Modal Éxito --}}
<div class="modal fade" id="modalSuccess" tabindex="-1" role="dialog" aria-labelledby="modalSuccessLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalSuccessLabel">¡Enviado con éxito!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body py-4">
        <h1 style="font-size: 70px; color: #28a745;">✔️</h1>
        <p class="mt-3" style="font-size: 1.2rem; font-weight: 500;">Su respuesta ha sido enviada exitosamente.</p>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-center">
        <button type="button" class="btn btn-primary px-4" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const stepFisico = document.getElementById('step-fisico');
    const stepPsico  = document.getElementById('step-psico');
    const nextBtn    = document.getElementById('btn-next');
    const prevBtn    = document.getElementById('btn-prev');
    const sendBtn    = document.getElementById('btn-send');

    const btnOpenModalPregunta = document.getElementById('btn-open-modal-pregunta');
    const modalTitulo          = document.getElementById('modalPreguntaLabel');
    const modalTipo            = document.getElementById('modalTipo');
    const modalTexto           = document.getElementById('modalTexto');
    const modalContador        = document.getElementById('modalContador');
    const modalAvisoLimite     = document.getElementById('modalAvisoLimite');
    const modalGuardarBtn      = document.getElementById('modalGuardarBtn');

    const modalEliminarConfirmBtn = document.getElementById('modalEliminarConfirmBtn');

    let modalMode = 'agregar';   // 'agregar' | 'editar'
    let currentQuestionElement = null;

    const MAX_PREGUNTA_LENGTH = 250;

    /* Navegación entre pasos */
    if (nextBtn) nextBtn.addEventListener('click', (e) => {
      e.preventDefault();
      stepFisico.classList.add('step-hidden');
      stepPsico.classList.remove('step-hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    if (prevBtn) prevBtn.addEventListener('click', (e) => {
      e.preventDefault();
      stepPsico.classList.add('step-hidden');
      stepFisico.classList.remove('step-hidden');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    /* Abrir modal en modo Agregar */
    if (btnOpenModalPregunta) {
      btnOpenModalPregunta.addEventListener('click', function () {
        modalMode = 'agregar';
        currentQuestionElement = null;

        modalTitulo.textContent = 'Agregar Pregunta';
        modalTipo.value = 'fisico';
        modalTexto.value = '';
        modalContador.textContent = '0';
        modalAvisoLimite.classList.add('d-none');
        modalGuardarBtn.disabled = true;

        $('#modalPregunta').modal('show');
      });
    }

    /* Contador de caracteres */
    if (modalTexto) {
      modalTexto.addEventListener('input', function () {
        const length = modalTexto.value.length;
        modalContador.textContent = length.toString();
        modalGuardarBtn.disabled = (length === 0);

        if (length >= MAX_PREGUNTA_LENGTH) {
          modalAvisoLimite.classList.remove('d-none');
        } else {
          modalAvisoLimite.classList.add('d-none');
        }
      });
    }

    /* Delegación: botones Editar / Eliminar */
    document.addEventListener('click', function (e) {
      const target = e.target;

      // Editar
      if (target.classList.contains('btn-editar')) {
        const qRow = target.closest('.q-row');
        if (!qRow) return;

        currentQuestionElement = qRow;
        modalMode = 'editar';

        const tipo = qRow.getAttribute('data-tipo') || 'fisico';
        const label = qRow.querySelector('.q-label');
        const texto = label ? label.textContent.trim() : '';

        modalTitulo.textContent = 'Editar Pregunta';
        modalTipo.value = tipo;
        modalTexto.value = texto;
        modalContador.textContent = texto.length.toString();
        modalGuardarBtn.disabled = (texto.length === 0);
        modalAvisoLimite.classList.add('d-none');

        $('#modalPregunta').modal('show');
      }

      // Eliminar
      if (target.classList.contains('btn-eliminar')) {
        const qRow = target.closest('.q-row');
        if (!qRow) return;
        currentQuestionElement = qRow;
        $('#modalEliminarPregunta').modal('show');
      }
    });

    /* Confirmar eliminación */
    if (modalEliminarConfirmBtn) {
      modalEliminarConfirmBtn.addEventListener('click', function () {
        if (currentQuestionElement) {
          currentQuestionElement.remove();
          currentQuestionElement = null;
        }
        $('#modalEliminarPregunta').modal('hide');
      });
    }

    /* Guardar en modal (Agregar / Editar) */
    if (modalGuardarBtn) {
      modalGuardarBtn.addEventListener('click', function () {
        const texto = modalTexto.value.trim();
        if (!texto) return;

        const tipo = modalTipo.value === 'psicologico' ? 'psicologico' : 'fisico';

        if (modalMode === 'agregar') {
          // contenedor destino
          const contenedorId = tipo === 'fisico' ? 'preguntas-fisico' : 'preguntas-psico';
          const contenedor = document.getElementById(contenedorId);
          if (!contenedor) return;

          // clonar plantilla existente (primera pregunta de ese tipo)
          const plantilla = contenedor.querySelector('.q-row');
          let nuevo;

          if (plantilla) {
            nuevo = plantilla.cloneNode(true);
            // limpiar radios
            nuevo.querySelectorAll('input[type="radio"]').forEach(r => {
              r.checked = false;
              // Cambiar id y for para evitar colisiones
              const randomSuffix = Math.floor(Math.random() * 100000);
              const oldId = r.id;
              const newId = oldId + '_' + randomSuffix;
              r.id = newId;
              const lbl = r.parentElement.querySelector('label');
              if (lbl) lbl.setAttribute('for', newId);
            });
          } else {
            // si no hay plantilla, creamos algo muy básico
            const randomName = 'q_new_' + Math.floor(Math.random() * 100000);
            nuevo = document.createElement('div');
            nuevo.classList.add('q-row');
            nuevo.setAttribute('data-tipo', tipo);
            nuevo.innerHTML = `
              <div class="q-label"></div>
              <div class="icheck-inline">
                <div class="${tipo === 'fisico' ? 'icheck-success' : 'icheck-info'} d-inline">
                  <input type="radio" id="${randomName}_0" name="${randomName}">
                  <label for="${randomName}_0">Nunca</label>
                </div>
                <div class="${tipo === 'fisico' ? 'icheck-success' : 'icheck-info'} d-inline">
                  <input type="radio" id="${randomName}_1" name="${randomName}">
                  <label for="${randomName}_1">Raramente</label>
                </div>
                <div class="${tipo === 'fisico' ? 'icheck-success' : 'icheck-info'} d-inline">
                  <input type="radio" id="${randomName}_2" name="${randomName}">
                  <label for="${randomName}_2">A veces</label>
                </div>
                <div class="${tipo === 'fisico' ? 'icheck-success' : 'icheck-info'} d-inline">
                  <input type="radio" id="${randomName}_3" name="${randomName}">
                  <label for="${randomName}_3">Frecuentemente</label>
                </div>
                <div class="${tipo === 'fisico' ? 'icheck-success' : 'icheck-info'} d-inline">
                  <input type="radio" id="${randomName}_4" name="${randomName}">
                  <label for="${randomName}_4">Siempre</label>
                </div>
              </div>
              <div class="acciones">
                <button type="button" class="btn-editar">Editar</button>
                <button type="button" class="btn-eliminar">Eliminar</button>
              </div>
            `;
          }

          const lbl = nuevo.querySelector('.q-label');
          if (lbl) lbl.textContent = texto;
          nuevo.setAttribute('data-tipo', tipo);

          contenedor.appendChild(nuevo);

        } else if (modalMode === 'editar' && currentQuestionElement) {
          const lbl = currentQuestionElement.querySelector('.q-label');
          if (lbl) lbl.textContent = texto;

          // si cambió de tipo, mover la tarjeta al otro contenedor
          const oldTipo = currentQuestionElement.getAttribute('data-tipo') || 'fisico';
          if (oldTipo !== tipo) {
            const contenedorId = tipo === 'fisico' ? 'preguntas-fisico' : 'preguntas-psico';
            const contenedor = document.getElementById(contenedorId);
            if (contenedor) {
              currentQuestionElement.setAttribute('data-tipo', tipo);
              contenedor.appendChild(currentQuestionElement);
              
              // Ajustar clases de los radios
              currentQuestionElement.querySelectorAll('.icheck-success, .icheck-info').forEach(div => {
                if (tipo === 'fisico') {
                  div.className = 'icheck-success d-inline';
                } else {
                  div.className = 'icheck-info d-inline';
                }
              });
            }
          }
        }

        $('#modalPregunta').modal('hide');
      });
    }

    /* Validación simple al enviar: todas las radios respondidas */
    function todasLasPreguntasRespondidas () {
      const todas = document.querySelectorAll('.q-row');
      return Array.from(todas).every(q => q.querySelector('input[type="radio"]:checked'));
    }

    if (sendBtn) {
      sendBtn.addEventListener('click', function () {
        const completo = todasLasPreguntasRespondidas();
        const cuerpoSeleccionado = true; // placeholder para no bloquear

        if (!completo || !cuerpoSeleccionado) {
          $('#modalError').modal('show');
          return;
        }

        // Todo ok -> mostrar éxito
        $('#modalSuccess').modal('show');
      });
    }

    /* Interacción con los SVGs del cuerpo humano */
    const bodyParts = document.querySelectorAll('.human-body svg');
    bodyParts.forEach(part => {
      part.addEventListener('click', function() {
        // Alternar color al hacer clic
        const currentFill = this.style.fill || getComputedStyle(this).fill;
        // Si ya está seleccionado (color rojo), vuelve al color gris original (#424242)
        if (currentFill === 'rgb(220, 53, 69)' || currentFill === '#dc3545') {
          this.style.fill = '#424242';
          this.classList.remove('body-part-selected');
        } else {
          this.style.fill = '#dc3545'; // Color de selección (Rojo)
          this.classList.add('body-part-selected');
        }
      });
    });

  });
</script>
@endsection
