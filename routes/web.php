<?php

use Illuminate\Support\Facades\Route;

// =========================================================================
// IMPORTACIÓN DE CONTROLADORES
// =========================================================================

use App\Http\Controllers\Auth\LoginController;

// Controladores Principales
use App\Http\Controllers\{
    UsuarioController,
    RoleController,
    CampaniaController,
    EstadoController,
    DonacionController,
    AsignacionController,
    DetallesAsignacionController,
    MensajeController,
    RespuestaMensajeController,
    DonacionesAsignacionController,
    SaldosDonacionController,
    DashboardController
};

// Controladores de Reportes y Procesos
use App\Http\Controllers\{
    CampaniaReporteController,
    UsuarioReporteController,
    CampaniaCierreController,
    CentroMensajesController,
    ReporteCierreCajaController,
    ReporteTrazabilidadController
};

// Controladores de Sincronización Interna
use App\Http\Controllers\{
    ApiCampaniaSyncController,
    ApiDonacionSyncController
};

// Controladores de Integración Externa (Namespace: Ext)
use App\Http\Controllers\Ext\{
    IntegracionExternaController,
    AlmacenesEstructuraController,
    TrazabilidadSyncController,
    TrazabilidadController // Alias para el nuevo módulo
};
use App\Http\Controllers\Fusion\Fase1IntegracionController;
use App\Http\Controllers\Fusion\ModulosIntegradosController;

/*
|--------------------------------------------------------------------------
| 1. RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

/*
|--------------------------------------------------------------------------
| 2. RUTAS PROTEGIDAS (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // --- Logout & Home ---
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/fusion/fase1', [Fase1IntegracionController::class, 'index'])->name('fusion.fase1.index');
    Route::get('/incendios', [ModulosIntegradosController::class, 'incendios'])->name('fusion.modulos.incendios');
    Route::get('/rescate', [ModulosIntegradosController::class, 'rescate'])->name('fusion.modulos.rescate');

    // ====================================================
    // SINCRONIZACIONES (ACCESO GENERAL)
    // ====================================================


    Route::get('/sincronizar/campanias', [ApiCampaniaSyncController::class, 'sync'])->name('api.campanias.sync');
    Route::get('/sincronizar/donaciones-dinero', [ApiDonacionSyncController::class, 'syncDinero'])->name('api.donaciones.dinero.sync');
    
    Route::prefix('integracion-externa')->group(function () {
        Route::get('/sync/categorias', [IntegracionExternaController::class, 'syncCategoriasProductos'])->name('integracion.sync.categorias');
        Route::get('/sync/almacenes', [IntegracionExternaController::class, 'syncAlmacenes'])->name('integracion.sync.almacenes');
        Route::get('/sync/all', [IntegracionExternaController::class, 'syncAll'])->name('integracion.sync.all');
        Route::get('/sync/trazabilidad-especie', [TrazabilidadSyncController::class, 'syncEspecie'])->name('integracion.sync.trazabilidad.especie');
    });

    // ====================================================
    // GRUPO A: SOLO ADMINISTRADOR
    // ====================================================
    Route::group(['middleware' => ['role:Administrador']], function () {
        Route::resource('roles', RoleController::class);
        Route::resource('usuarios', UsuarioController::class);
        Route::resource('estados', EstadoController::class);
        Route::resource('campanias', CampaniaController::class)->whereNumber('campania');
    });

    // ====================================================
    // GRUPO B: ADMINISTRADOR | ALMACENERO
    // ====================================================
    Route::group(['middleware' => ['role:Administrador|Almacenero']], function () {
        Route::get('/almacenes/estructura', [AlmacenesEstructuraController::class, 'index'])->name('almacenes.estructura');
    });

    // ====================================================
    // GRUPO C: ADMINISTRADOR | REPORTES
    // ====================================================
    Route::group(['middleware' => ['role:Administrador|Reportes']], function () {
        
        // --- DONACIONES & ASIGNACIONES ---
        Route::resource('donaciones', DonacionController::class);
        Route::get('/donaciones/{id}/reasignar', [DonacionController::class, 'reasignarForm'])->name('donaciones.reasignarForm');
        Route::post('/donaciones/{id}/reasignar', [DonacionController::class, 'reasignar'])->name('donaciones.reasignar');

        Route::resource('asignaciones', AsignacionController::class);
        Route::resource('detallesasignacion', DetallesAsignacionController::class);
        Route::resource('donacionesasignaciones', DonacionesAsignacionController::class);
        
        Route::get('asignaciones/{id}/detalles', [AsignacionController::class, 'detalles'])->name('asignaciones.detalles');
        Route::post('asignaciones/{id}/detalles', [AsignacionController::class, 'guardarDetalle'])->name('asignaciones.detalles.store');
        Route::get('asignaciones/{id}/asignar', [AsignacionController::class, 'asignar'])->name('asignaciones.asignar');
        Route::post('asignaciones/{id}/asignar', [AsignacionController::class, 'guardarAsignacion'])->name('asignaciones.asignar.store');
        Route::get('/asignaciones/{id}/asignar-donacion', [AsignacionController::class, 'asignarDonacionForm'])->name('asignaciones.asignarDonacionForm');
        Route::post('/asignaciones/{id}/asignar-donacion', [AsignacionController::class, 'asignarDonacionStore'])->name('asignaciones.asignarDonacionStore');

        // --- REPORTES & ESTADOS DE CUENTA ---
        Route::get('/estado-cuenta', [UsuarioReporteController::class, 'seleccionarUsuario'])->name('usuarios.estadoCuentaSeleccion');
        Route::get('/estado-cuenta/mostrar', [UsuarioReporteController::class, 'mostrarDesdeSeleccion'])->name('usuarios.estadoCuentaMostrar');
        Route::get('/usuarios/{usuario}/estado-cuenta', [UsuarioReporteController::class, 'estadoCuenta'])->name('usuarios.estadoCuenta');

        Route::get('/campanias/reporte-general', [CampaniaReporteController::class, 'general'])->name('campanias.reporteGeneral');
        Route::get('/campanias/cierre', [CampaniaCierreController::class, 'seleccionarCampania'])->name('campanias.cierreSeleccion');
        Route::get('/campanias/cierre/mostrar', [CampaniaCierreController::class, 'mostrarResumen'])->name('campanias.cierreMostrar');
        Route::post('/campanias/{campania}/cerrar', [CampaniaCierreController::class, 'cerrarCampania'])->name('campanias.cerrar');

        Route::get('/reporte/cierre-caja', [ReporteCierreCajaController::class, 'index'])->name('reporte.cierreCaja');
        Route::get('/reporte/cierre-caja/pdf', [ReporteCierreCajaController::class, 'exportarPDF'])->name('reporte.cierreCaja.pdf');
        Route::get('/reporte/cierre-caja/excel', [ReporteCierreCajaController::class, 'exportarExcel'])->name('reporte.cierreCaja.excel');
        
    });

    // ====================================================
    // GRUPO D: COMPARTIDO (ADMIN | ALMACENERO | REPORTES)
    // ====================================================
    Route::group(['middleware' => ['role:Administrador|Almacenero|Reportes']], function () {
        
        // --- REPORTES INTERNOS TRAZABILIDAD ---
        // Aquí es donde faltaban las rutas 'paquete' y 'paquete.ajax'
        Route::prefix('reportes/trazabilidad')->name('reportes.trazabilidad.')->group(function () {
            Route::get('/', [ReporteTrazabilidadController::class, 'index'])->name('index');
            Route::get('/pdf', [ReporteTrazabilidadController::class, 'exportarPDF'])->name('pdf');
            Route::get('/excel', [ReporteTrazabilidadController::class, 'exportarExcel'])->name('excel');
            
            // **RUTAS RESTAURADAS**
            Route::get('/paquete/{codigo}', [ReporteTrazabilidadController::class, 'verPaquete'])->name('paquete');
            Route::get('/paquete/{codigo}/ajax', [ReporteTrazabilidadController::class, 'verPaqueteAjax'])->name('paquete.ajax');

        });

        // --------------------------------------------------------
        // NUEVO: INTEGRACIÓN GATEWAY (SITUACIONES)
        // --------------------------------------------------------
        Route::prefix('gateway-trazabilidad')->name('gateway.trazabilidad.')->group(function () {
            Route::get('/', [TrazabilidadController::class, 'index'])->name('index');
            Route::get('/paquete/{codigo}', [TrazabilidadController::class, 'showPaquete'])->name('paquete');
            Route::get('/vehiculo/{placa}', [TrazabilidadController::class, 'showVehiculo'])->name('vehiculo');
            Route::get('/especie/{nombre}', [TrazabilidadController::class, 'showEspecie'])->name('especie');

            // opcional: botón "Actualizar" que fuerce sync
            Route::get('/sync', [TrazabilidadController::class, 'sync'])->name('sync');
        });


        // --- SALDOS ---
        Route::resource('saldosdonaciones', SaldosDonacionController::class);

        // --- MENSAJERÍA ---
        Route::get('/centro-mensajes', [CentroMensajesController::class, 'seleccionarUsuario'])->name('mensajes.centroSeleccion');
        Route::get('/centro-mensajes/usuario', [CentroMensajesController::class, 'centroPorUsuario'])->name('mensajes.centroUsuario');
        Route::resource('mensajes', MensajeController::class)->parameters(['mensajes' => 'id']);
        Route::resource('respuestasmensajes', RespuestaMensajeController::class)->parameters(['respuestasmensajes' => 'id']);

        Route::get('/chat', [MensajeController::class, 'inbox'])->name('chat.inbox');
        Route::get('/chat/{usuario}', [MensajeController::class, 'conversacion'])->name('chat.conversacion');
        Route::post('/chat/{usuario}/enviar', [MensajeController::class, 'enviar'])->name('chat.enviar');
    });

});

Route::prefix('inventario')
    ->as('inventario.')
    ->group(function () {
        require base_path('modulos/donacion-recepcion-inventario-main/routes/web.php');
    });

Route::prefix('incendios/modulo')
    ->as('incendios.')
    ->middleware(['auth'])
    ->group(function () {
        require base_path('modulos/monitoreo-incendios-simulacion-main/routes/web.php');
    });

Route::prefix('rescate/modulo')
    ->as('rescate.')
    ->middleware(['auth'])
    ->group(function () {
        require base_path('modulos/rescate-animales-silvestres-main/routes/web.php');
    });