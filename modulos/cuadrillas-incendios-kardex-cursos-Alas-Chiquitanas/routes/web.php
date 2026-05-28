<?php

use App\Http\Controllers\CuadrillasIncendios\ModuloController;
use App\Http\Controllers\CuadrillasIncendios\SeccionesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ModuloController::class, 'index'])->name('dashboard');
Route::get('/estadisticas', [ModuloController::class, 'index'])->name('estadisticas');

Route::get('/reportes', [SeccionesController::class, 'show'])->defaults('seccion', 'reportes')->name('reportes');
Route::get('/reportes-incendio', [SeccionesController::class, 'show'])->defaults('seccion', 'reportes-incendio')->name('reportes-incendio');
Route::get('/focos-calor', [SeccionesController::class, 'show'])->defaults('seccion', 'focos-calor')->name('focos-calor');
Route::get('/equipos', [SeccionesController::class, 'show'])->defaults('seccion', 'equipos')->name('equipos');
Route::get('/recursos', [SeccionesController::class, 'show'])->defaults('seccion', 'recursos')->name('recursos');
Route::get('/noticias', [SeccionesController::class, 'show'])->defaults('seccion', 'noticias')->name('noticias');
Route::get('/cursos', [SeccionesController::class, 'show'])->defaults('seccion', 'cursos')->name('cursos');
Route::get('/inscritos', [SeccionesController::class, 'show'])->defaults('seccion', 'inscritos')->name('inscritos');
Route::get('/comunarios', [SeccionesController::class, 'show'])->defaults('seccion', 'comunarios')->name('comunarios');
Route::get('/usuarios', [SeccionesController::class, 'show'])->defaults('seccion', 'usuarios')->name('usuarios');
Route::get('/roles', [SeccionesController::class, 'show'])->defaults('seccion', 'roles')->name('roles');
Route::get('/generos', [SeccionesController::class, 'show'])->defaults('seccion', 'generos')->name('generos');
Route::get('/tipos-sangre', [SeccionesController::class, 'show'])->defaults('seccion', 'tipos-sangre')->name('tipos-sangre');
Route::get('/niveles-entrenamiento', [SeccionesController::class, 'show'])->defaults('seccion', 'niveles-entrenamiento')->name('niveles-entrenamiento');
Route::get('/niveles-gravedad', [SeccionesController::class, 'show'])->defaults('seccion', 'niveles-gravedad')->name('niveles-gravedad');
Route::get('/tipos-incidente', [SeccionesController::class, 'show'])->defaults('seccion', 'tipos-incidente')->name('tipos-incidente');
Route::get('/tipos-recurso', [SeccionesController::class, 'show'])->defaults('seccion', 'tipos-recurso')->name('tipos-recurso');
Route::get('/condiciones-climaticas', [SeccionesController::class, 'show'])->defaults('seccion', 'condiciones-climaticas')->name('condiciones-climaticas');
Route::get('/estados-sistema', [SeccionesController::class, 'show'])->defaults('seccion', 'estados-sistema')->name('estados-sistema');
Route::get('/kardex', [SeccionesController::class, 'show'])->defaults('seccion', 'kardex')->name('kardex');
Route::get('/helpdesk', [SeccionesController::class, 'show'])->defaults('seccion', 'helpdesk')->name('helpdesk');

Route::prefix('crud/{seccion}')->group(function () {
    Route::get('/create', [SeccionesController::class, 'crudCreate'])->name('crud.create');
    Route::post('/', [SeccionesController::class, 'crudStore'])->name('crud.store');
    Route::get('/{id}/edit', [SeccionesController::class, 'crudEdit'])->name('crud.edit');
    Route::put('/{id}', [SeccionesController::class, 'crudUpdate'])->name('crud.update');
    Route::delete('/{id}', [SeccionesController::class, 'crudDestroy'])->name('crud.destroy');
});
