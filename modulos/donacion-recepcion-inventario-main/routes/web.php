<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('inventario.home');
    }
    return redirect()->route('login');
});

Route::get('/home', [Modules\Inventario\Http\Controllers\HomeController::class, 'index'])->name('home');

// Rutas solo para Administrador
Route::middleware(['auth', 'permission.check:admin.usuarios.gestionar'])->group(function () {
    Route::resource('usuario', Modules\Inventario\Http\Controllers\UsuarioController::class);
});

// Rutas para gestionar campañas (Almacenero / Administrador)
Route::middleware(['auth', 'permission.check:inventario.dashboard.ver'])->group(function () {
    Route::get('campana/create', [Modules\Inventario\Http\Controllers\CampanaController::class, 'create'])->name('campana.create');
    Route::post('campana', [Modules\Inventario\Http\Controllers\CampanaController::class, 'store'])->name('campana.store');
    Route::get('campana/{campana}/edit', [Modules\Inventario\Http\Controllers\CampanaController::class, 'edit'])->name('campana.edit');
    Route::put('campana/{campana}', [Modules\Inventario\Http\Controllers\CampanaController::class, 'update'])->name('campana.update');
    Route::delete('campana/{campana}', [Modules\Inventario\Http\Controllers\CampanaController::class, 'destroy'])->name('campana.destroy');
});

// Rutas para ver campañas (Donante: públicas; operativos: dashboard inventario)
Route::middleware(['auth', 'permission.check:donante.campanas.ver|inventario.dashboard.ver'])->group(function () {
    Route::get('campana', [Modules\Inventario\Http\Controllers\CampanaController::class, 'index'])->name('campana.index');
    Route::get('campana/{campana}', [Modules\Inventario\Http\Controllers\CampanaController::class, 'show'])->name('campana.show');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('puntos-recoleccion', Modules\Inventario\Http\Controllers\PuntosRecoleccionController::class);
});

Route::middleware(['auth', 'permission.check:inventario.categorias.gestionar|inventario.dashboard.ver'])->group(function () {
    Route::get('categorias-producto', [Modules\Inventario\Http\Controllers\CategoriasProductoController::class, 'index'])->name('categorias-producto.index');
    Route::get('categorias-producto/create', [Modules\Inventario\Http\Controllers\CategoriasProductoController::class, 'create'])->name('categorias-producto.create');
    Route::post('categorias-producto', [Modules\Inventario\Http\Controllers\CategoriasProductoController::class, 'store'])->name('categorias-producto.store');
    Route::get('categorias-producto/{categorias_producto}/edit', [Modules\Inventario\Http\Controllers\CategoriasProductoController::class, 'edit'])->name('categorias-producto.edit');
    Route::put('categorias-producto/{categorias_producto}', [Modules\Inventario\Http\Controllers\CategoriasProductoController::class, 'update'])->name('categorias-producto.update');
    Route::delete('categorias-producto/{categorias_producto}', [Modules\Inventario\Http\Controllers\CategoriasProductoController::class, 'destroy'])->name('categorias-producto.destroy');
    Route::get('categorias-producto/{categorias_producto}', [Modules\Inventario\Http\Controllers\CategoriasProductoController::class, 'show'])->name('categorias-producto.show');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('producto', Modules\Inventario\Http\Controllers\ProductoController::class);
});

Route::middleware(['auth', 'permission.check:inventario.donaciones.registrar'])->group(function () {
    Route::resource('donante', Modules\Inventario\Http\Controllers\DonanteController::class);
});

// Rutas solo para Administrador (gestión de almacenes) - ANTES de las rutas con parámetros
Route::middleware(['auth'])->group(function () {
    Route::get('almacene/create', [Modules\Inventario\Http\Controllers\AlmaceneController::class, 'create'])->name('almacene.create');
    Route::post('almacene', [Modules\Inventario\Http\Controllers\AlmaceneController::class, 'store'])->name('almacene.store');
    Route::get('almacene/{almacene}/edit', [Modules\Inventario\Http\Controllers\AlmaceneController::class, 'edit'])->name('almacene.edit');
    Route::put('almacene/{almacene}', [Modules\Inventario\Http\Controllers\AlmaceneController::class, 'update'])->name('almacene.update');
    Route::delete('almacene/{almacene}', [Modules\Inventario\Http\Controllers\AlmaceneController::class, 'destroy'])->name('almacene.destroy');
});

// Rutas de solicitudes de recolección - create ANTES de show
Route::middleware(['auth'])->group(function () {
    Route::get('solicitudes-recoleccions', [Modules\Inventario\Http\Controllers\SolicitudesRecoleccionController::class, 'index'])->name('solicitudes-recoleccions.index');
    Route::get('solicitudes-recoleccions/create', [Modules\Inventario\Http\Controllers\SolicitudesRecoleccionController::class, 'create'])->name('solicitudes-recoleccions.create');
    Route::post('solicitudes-recoleccions', [Modules\Inventario\Http\Controllers\SolicitudesRecoleccionController::class, 'store'])->name('solicitudes-recoleccions.store');
    Route::get('solicitudes-recoleccions/{solicitudes_recoleccion}', [Modules\Inventario\Http\Controllers\SolicitudesRecoleccionController::class, 'show'])->name('solicitudes-recoleccions.show');
});

Route::middleware(['auth'])->group(function () {
    // Solo Admin y Almacenista pueden editar/eliminar solicitudes
    Route::get('solicitudes-recoleccions/{solicitudes_recoleccion}/edit', [Modules\Inventario\Http\Controllers\SolicitudesRecoleccionController::class, 'edit'])->name('solicitudes-recoleccions.edit');
    Route::put('solicitudes-recoleccions/{solicitudes_recoleccion}', [Modules\Inventario\Http\Controllers\SolicitudesRecoleccionController::class, 'update'])->name('solicitudes-recoleccions.update');
    Route::delete('solicitudes-recoleccions/{solicitudes_recoleccion}', [Modules\Inventario\Http\Controllers\SolicitudesRecoleccionController::class, 'destroy'])->name('solicitudes-recoleccions.destroy');
});

// Rutas para Administrador, Almacenista y Voluntario - Rutas específicas ANTES que las dinámicas
Route::middleware(['auth'])->group(function () {
    // Rutas específicas primero
    Route::get('paquete/pendientes', [Modules\Inventario\Http\Controllers\PaqueteController::class, 'pendientes'])->name('paquete.pendientes');
    Route::post('donaciones/guardar', [Modules\Inventario\Http\Controllers\DonacioneController::class, 'store'])->name('donaciones.guardar_manual');
    Route::post('espacio/{id}/toggle-status', [Modules\Inventario\Http\Controllers\EspacioController::class, 'toggleStatus'])->name('espacio.toggleStatus');

    // API routes for cascading dropdowns
    Route::get('api/almacenes/{id}/estantes', [Modules\Inventario\Http\Controllers\AlmaceneController::class, 'getEstantes']);
    Route::get('api/estantes/{id}/espacios', [Modules\Inventario\Http\Controllers\EstanteController::class, 'getEspacios']);

    // Rutas de almacenes (solo ver)
    Route::get('almacene', [Modules\Inventario\Http\Controllers\AlmaceneController::class, 'index'])->name('almacene.index');
    Route::get('almacene/{almacene}', [Modules\Inventario\Http\Controllers\AlmaceneController::class, 'show'])->name('almacene.show');

    // Resource routes
    Route::resource('estante', Modules\Inventario\Http\Controllers\EstanteController::class);
    Route::resource('paquete', Modules\Inventario\Http\Controllers\PaqueteController::class);
    Route::resource('registros-salida', Modules\Inventario\Http\Controllers\RegistrosSalidaController::class);
    Route::resource('donaciones', Modules\Inventario\Http\Controllers\DonacioneController::class);
    Route::resource('espacio', Modules\Inventario\Http\Controllers\EspacioController::class);

    // Rutas de reportes
    Route::get('reportes', [Modules\Inventario\Http\Controllers\ReportesController::class, 'index'])->name('reportes.index');
    Route::get('reportes/donaciones-periodo', [Modules\Inventario\Http\Controllers\ReportesController::class, 'donacionesPorPeriodo'])->middleware('permission.check:inventario.reportes.ver')->name('reportes.donaciones.periodo');
    Route::get('reportes/inventario-almacen', [Modules\Inventario\Http\Controllers\ReportesController::class, 'inventarioPorAlmacen'])->name('reportes.inventario.almacen');
    Route::get('reportes/solicitudes-recoleccion', [Modules\Inventario\Http\Controllers\ReportesController::class, 'solicitudesRecoleccion'])->name('reportes.solicitudes');
    Route::get('reportes/salidas-productos', [Modules\Inventario\Http\Controllers\ReportesController::class, 'salidasProductos'])->name('reportes.salidas');
    Route::get('reportes/campanas', [Modules\Inventario\Http\Controllers\ReportesController::class, 'campanasReporte'])->name('reportes.campanas');
    Route::get('reportes/distribucion', [Modules\Inventario\Http\Controllers\ReportesController::class, 'reporteDistribucion'])->name('reportes.distribucion');
});


// ========== HELPDESK WIDGET ==========
// Ruta generada por: php artisan helpdeskwidget:install
Route::get('helpdesk', function () {
    return view('inventario::helpdesk');
})->name('helpdesk')->middleware('auth');




