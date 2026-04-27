<?php

use App\Http\Controllers\SeguimientoVoluntarios\ModuloController;
use App\Http\Controllers\SeguimientoVoluntarios\SeccionesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ModuloController::class, 'index'])->name('dashboard');
Route::get('/estadisticas', [ModuloController::class, 'index'])->name('estadisticas');

Route::get('/voluntarios', [SeccionesController::class, 'show'])->defaults('seccion', 'voluntarios')->name('voluntarios');
Route::get('/voluntarios-inactivos', [SeccionesController::class, 'show'])->defaults('seccion', 'voluntarios-inactivos')->name('voluntarios-inactivos');
Route::get('/evaluacion', [SeccionesController::class, 'show'])->defaults('seccion', 'evaluacion')->name('evaluacion');
Route::get('/evaluacion-pruebas', [SeccionesController::class, 'show'])->defaults('seccion', 'evaluacion-pruebas')->name('evaluacion-pruebas');
Route::get('/capacitaciones', [SeccionesController::class, 'show'])->defaults('seccion', 'capacitaciones')->name('capacitaciones');
Route::get('/necesidades', [SeccionesController::class, 'show'])->defaults('seccion', 'necesidades')->name('necesidades');
Route::get('/ayudas-solicitadas', [SeccionesController::class, 'show'])->defaults('seccion', 'ayudas-solicitadas')->name('ayudas-solicitadas');
Route::get('/administradores', [SeccionesController::class, 'show'])->defaults('seccion', 'administradores')->name('administradores');
Route::get('/universidades', [SeccionesController::class, 'show'])->defaults('seccion', 'universidades')->name('universidades');
Route::get('/chat-consulta', [SeccionesController::class, 'show'])->defaults('seccion', 'chat-consulta')->name('chat-consulta');
Route::get('/helpdesk', [SeccionesController::class, 'show'])->defaults('seccion', 'helpdesk')->name('helpdesk');
