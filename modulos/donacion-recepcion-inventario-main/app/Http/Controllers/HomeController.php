<?php

namespace Modules\Inventario\Http\Controllers;

use App\Support\AccessControl;
use App\Support\OwnershipScope;
use App\Support\Database\YearMonthSql;
use App\Support\InventarioOperativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Detectar el rol del usuario autenticado
        $user = auth()->user();

        if (AccessControl::userHasRole($user, 'Donante')) {
            OwnershipScope::ensureInventarioDonanteProfile($user);

            return redirect()->route('inventario.donaciones.index');
        }

        if (AccessControl::userHasRole($user, 'Coordinador Logístico')) {
            return redirect()->route('logistica.dashboard');
        }

        if (! AccessControl::userHasAnyRole($user, ['Almacenero', 'Administrador'])) {
            abort(403);
        }

        if (AccessControl::userHasRole($user, 'Almacenero')) {
            return $this->dashboardAlmacenista();
        }

        return $this->dashboardGeneral();
    }

    /**
     * Dashboard general para Administrador
     */
    private function dashboardGeneral()
    {
        // ============================================
        // KPI CARDS - 7 Métricas Principales
        // ============================================
        $totalDonaciones = \Modules\Inventario\Models\Donacione::count();
        $totalPaquetes = \Modules\Inventario\Models\Paquete::count();
        $totalSalidas = \Modules\Inventario\Models\RegistrosSalida::count();
        $solicitudesPendientes = \Modules\Inventario\Models\SolicitudesRecoleccion::where('estado', 'pendiente')->count();
        $totalDonantes = \Modules\Inventario\Models\Donante::count();
        $totalProductos = \Modules\Inventario\Models\Producto::count();
        $totalUsuarios = \Modules\Inventario\Models\Usuario::count();

        // Campañas activas (basado en fechas)
        $campanasActivas = \Modules\Inventario\Models\Campana::where('fecha_inicio', '<=', \Carbon\Carbon::now())
            ->where('fecha_fin', '>=', \Carbon\Carbon::now())
            ->count();

        // Promedio de donaciones por día (últimos 30 días)
        $donacionesUltimos30Dias = \Modules\Inventario\Models\Donacione::where('fecha', '>=', \Carbon\Carbon::now()->subDays(30))->count();
        $promedioDonacionesDia = $donacionesUltimos30Dias > 0 ? round($donacionesUltimos30Dias / 30, 1) : 0;

        // ============================================
        // VISUALIZACIÓN 1: Tendencia de Donaciones (12 meses) - LINE CHART
        // ============================================
        $donacionesPorMes = \Modules\Inventario\Models\Donacione::selectRaw(YearMonthSql::yearMonthSelect('fecha', 'inventario').', COUNT(*) as total')
            ->where('fecha', '>=', \Carbon\Carbon::now()->subMonths(12))
            ->groupByRaw(YearMonthSql::yearMonthGroupByRaw('fecha', 'inventario'))
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        $mesesLabels = [];
        $cantidadesDonaciones = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = \Carbon\Carbon::now()->subMonths($i);
            $mesesLabels[] = $fecha->locale('es')->isoFormat('MMM YYYY');

            $registro = $donacionesPorMes->first(function ($item) use ($fecha) {
                return $item->mes == $fecha->month && $item->anio == $fecha->year;
            });

            $cantidadesDonaciones[] = $registro ? $registro->total : 0;
        }

        // ============================================
        // VISUALIZACIÓN 2: Estado de Paquetes - DOUGHNUT CHART
        // ============================================
        $paquetesPorEstado = \Modules\Inventario\Models\Paquete::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estadosPaquetes = $paquetesPorEstado->keys();
        $cantidadesPaquetes = $paquetesPorEstado->values();

        // ============================================
        // VISUALIZACIÓN 3: Top 5 Categorías - BAR CHART
        // ============================================
        $topCategorias = \Modules\Inventario\Models\DonacionDetalle::join('productos', 'donacion_detalles.id_producto', '=', 'productos.id_producto')
            ->join('categorias_productos', 'productos.id_categoria', '=', 'categorias_productos.id_categoria')
            ->select('categorias_productos.nombre', \Illuminate\Support\Facades\DB::raw('COUNT(donacion_detalles.id_detalle) as total'))
            ->groupBy('categorias_productos.nombre')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $nombresTopCategorias = $topCategorias->pluck('nombre');
        $cantidadesTopCategorias = $topCategorias->pluck('total');


        // ============================================
        // VISUALIZACIÓN 4: Tendencia de Donaciones en Dinero (12 meses) - LINE CHART
        // ============================================
        $donacionesDineroPorMes = \Modules\Inventario\Models\DonacionesDinero::join('donaciones', 'donaciones_dinero.id_donacion', '=', 'donaciones.id_donacion')
            ->selectRaw(YearMonthSql::yearMonthSelect('donaciones.fecha', 'inventario').', SUM(donaciones_dinero.monto) as total_monto')
            ->where('donaciones.fecha', '>=', \Carbon\Carbon::now()->subMonths(12))
            ->groupByRaw(YearMonthSql::yearMonthGroupByRaw('donaciones.fecha', 'inventario'))
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        $mesesDineroLabels = [];
        $montoDonacionesDinero = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = \Carbon\Carbon::now()->subMonths($i);
            $mesesDineroLabels[] = $fecha->locale('es')->isoFormat('MMM YYYY');

            $registro = $donacionesDineroPorMes->first(function ($item) use ($fecha) {
                return $item->mes == $fecha->month && $item->anio == $fecha->year;
            });

            $montoDonacionesDinero[] = $registro ? (float) $registro->total_monto : 0;
        }

        // Total de donaciones en dinero para KPI
        $totalDonacionesDinero = \Modules\Inventario\Models\DonacionesDinero::sum('monto');


        // ============================================
        // VISUALIZACIÓN 5: Top 5 Donantes - HORIZONTAL BAR CHART
        // ============================================
        $topDonantes = \Modules\Inventario\Models\Donante::leftJoin('donaciones', 'donantes.id_donante', '=', 'donaciones.id_donante')
            ->select('donantes.nombre', \Illuminate\Support\Facades\DB::raw('COUNT(donaciones.id_donacion) as total_donaciones'))
            ->groupBy('donantes.id_donante', 'donantes.nombre')
            ->orderByDesc('total_donaciones')
            ->take(5)
            ->get();

        $nombresTopDonantes = $topDonantes->pluck('nombre');
        $cantidadesTopDonantes = $topDonantes->pluck('total_donaciones');

        // ============================================
        // VISUALIZACIÓN 6: Actividad Reciente - TIMELINE
        // ============================================
        $actividadesRecientes = [];

        // Últimas donaciones
        $ultimasDonaciones = \Modules\Inventario\Models\Donacione::with('donante')
            ->orderBy('fecha', 'desc')
            ->take(5)
            ->get()
            ->map(function ($donacion) {
                return [
                    'tipo' => 'donacion',
                    'icono' => 'fas fa-gift',
                    'color' => 'info',
                    'titulo' => 'Nueva Donación',
                    'descripcion' => 'Donación de ' . ($donacion->donante ? $donacion->donante->nombre : 'Anónimo'),
                    'fecha' => \Carbon\Carbon::parse($donacion->fecha)
                ];
            });

        // Últimos paquetes
        $ultimosPaquetes = \Modules\Inventario\Models\Paquete::orderBy('fecha_creacion', 'desc')
            ->take(5)
            ->get()
            ->map(function ($paquete) {
                return [
                    'tipo' => 'paquete',
                    'icono' => 'fas fa-box',
                    'color' => 'success',
                    'titulo' => 'Paquete Creado',
                    'descripcion' => 'Código: ' . $paquete->codigo_paquete . ' - Estado: ' . $paquete->estado,
                    'fecha' => \Carbon\Carbon::parse($paquete->fecha_creacion)
                ];
            });

        // Combinar y ordenar
        $actividadesRecientes = $ultimasDonaciones->concat($ultimosPaquetes)
            ->sortByDesc('fecha')
            ->take(10)
            ->values();

        return view('inventario::home', compact(
            // KPIs
            'totalDonaciones',
            'totalPaquetes',
            'totalSalidas',
            'solicitudesPendientes',
            'totalDonantes',
            'totalProductos',
            'totalUsuarios',
            'campanasActivas',
            'promedioDonacionesDia',
            'totalDonacionesDinero',
            // Viz 1: Tendencia Donaciones
            'mesesLabels',
            'cantidadesDonaciones',
            // Viz 2: Estado Paquetes
            'estadosPaquetes',
            'cantidadesPaquetes',
            // Viz 3: Top Categorías
            'nombresTopCategorias',
            'cantidadesTopCategorias',
            // Viz 4: Tendencia Donaciones en Dinero
            'mesesDineroLabels',
            'montoDonacionesDinero',
            // Viz 5: Top Donantes
            'nombresTopDonantes',
            'cantidadesTopDonantes',
            // Viz 6: Actividades Recientes
            'actividadesRecientes'
        ));
    }

    /**
     * Dashboard específico para Almacenista
     */
    private function dashboardAlmacenista()
    {
        // ============================================
        // KPIs para Almacenista
        // ============================================
        $totalAlmacenes = \Modules\Inventario\Models\Almacene::count();
        $totalEstantes = \Modules\Inventario\Models\Estante::count();
        $totalEspacios = \Modules\Inventario\Models\Espacio::count();

        // Los espacios usan 'lleno' / 'disponible' (minúsculas en el módulo)
        $espaciosLlenos = \Modules\Inventario\Models\Espacio::whereRaw("LOWER(COALESCE(estado, 'disponible')) = 'lleno'")->count();
        $espaciosDisponibles = $totalEspacios - $espaciosLlenos;

        $stockExpr = \Illuminate\Support\Facades\DB::raw(
            'SUM(COALESCE(ubicaciones_donaciones.cantidad_ubicada, donacion_detalles.cantidad)) as total'
        );

        // Productos en inventario (unidades en stock)
        $productosInventario = (int) \Illuminate\Support\Facades\DB::connection('inventario')
            ->table('donacion_detalles')
            ->leftJoin('ubicaciones_donaciones', 'donacion_detalles.id_detalle', '=', 'ubicaciones_donaciones.id_detalle')
            ->sum(\Illuminate\Support\Facades\DB::raw('COALESCE(ubicaciones_donaciones.cantidad_ubicada, donacion_detalles.cantidad)'));

        // ============================================
        // VIZ 1: Utilización por Almacén (Bar Chart Horizontal)
        // ============================================
        $almacenesData = \Illuminate\Support\Facades\DB::connection('inventario')->table('almacenes')
            ->leftJoin('estantes', 'almacenes.id_almacen', '=', 'estantes.id_almacen')
            ->leftJoin('espacios', 'estantes.id_estante', '=', 'espacios.id_estante')
            ->select(
                'almacenes.nombre',
                \Illuminate\Support\Facades\DB::raw('COUNT(espacios.id_espacio) as total_espacios'),
                \Illuminate\Support\Facades\DB::raw("COUNT(CASE WHEN LOWER(COALESCE(espacios.estado, 'disponible')) = 'lleno' THEN 1 END) as espacios_llenos")
            )
            ->groupBy('almacenes.id_almacen', 'almacenes.nombre')
            ->get();

        $nombresAlmacenes = [];
        $porcentajesUtilizacion = [];

        foreach ($almacenesData as $almacen) {
            $nombresAlmacenes[] = $almacen->nombre;

            if ($almacen->total_espacios > 0) {
                $porcentajesUtilizacion[] = round(($almacen->espacios_llenos / $almacen->total_espacios) * 100, 1);
            } else {
                $porcentajesUtilizacion[] = 0;
            }
        }

        // ============================================
        // VIZ 2: Productos por Categoría (Doughnut)
        // ============================================
        $productosPorCategoria = \Illuminate\Support\Facades\DB::connection('inventario')->table('donacion_detalles')
            ->join('productos', 'donacion_detalles.id_producto', '=', 'productos.id_producto')
            ->join('categorias_productos', 'productos.id_categoria', '=', 'categorias_productos.id_categoria')
            ->leftJoin('ubicaciones_donaciones', 'donacion_detalles.id_detalle', '=', 'ubicaciones_donaciones.id_detalle')
            ->select('categorias_productos.nombre', $stockExpr)
            ->groupBy('categorias_productos.id_categoria', 'categorias_productos.nombre')
            ->orderByDesc('total')
            ->get();

        $nombresCategorias = $productosPorCategoria->pluck('nombre');
        $cantidadesCategorias = $productosPorCategoria->pluck('total');

        // ============================================
        // VIZ 3: Estado de Espacios (Doughnut)
        // ============================================
        // Ya tenemos espaciosDisponibles y espaciosLlenos

        // ============================================
        // VIZ 4: Movimientos Recientes (Timeline)
        // ============================================
        $movimientosRecientes = [];

        // Últimas entradas (donaciones ubicadas o registradas)
        $ultimasEntradas = \Modules\Inventario\Models\UbicacionesDonacione::with(['detalle.donacion', 'espacio.estante.almacene'])
            ->orderBy('id_ubicacion', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ubicacion) {
                return [
                    'tipo' => 'entrada',
                    'icono' => 'fas fa-arrow-down',
                    'color' => 'success',
                    'titulo' => 'Ingreso al Almacén',
                    'descripcion' => 'Producto ingresado a '.($ubicacion->espacio->estante->almacene->nombre ?? 'Almacén'),
                    'fecha' => $ubicacion->detalle && $ubicacion->detalle->donacion ? \Carbon\Carbon::parse($ubicacion->detalle->donacion->fecha) : \Carbon\Carbon::now(),
                ];
            });

        if ($ultimasEntradas->isEmpty()) {
            $ultimasEntradas = \Modules\Inventario\Models\Donacione::with('donante')
                ->where('tipo', 'especie')
                ->orderByDesc('fecha')
                ->take(5)
                ->get()
                ->map(function ($donacion) {
                    return [
                        'tipo' => 'entrada',
                        'icono' => 'fas fa-arrow-down',
                        'color' => 'success',
                        'titulo' => 'Donación en especie registrada',
                        'descripcion' => 'Donante: '.($donacion->donante->nombre ?? 'Anónimo'),
                        'fecha' => \Carbon\Carbon::parse($donacion->fecha),
                    ];
                });
        }

        // Últimas salidas
        $ultimasSalidas = \Modules\Inventario\Models\RegistrosSalida::orderBy('fecha_salida', 'desc')
            ->take(5)
            ->get()
            ->map(function ($salida) {
                return [
                    'tipo' => 'salida',
                    'icono' => 'fas fa-arrow-up',
                    'color' => 'warning',
                    'titulo' => 'Salida del Almacén',
                    'descripcion' => 'Productos despachados - Destino: ' . ($salida->destino ?? 'No especificado'),
                    'fecha' => \Carbon\Carbon::parse($salida->fecha_salida)
                ];
            });

        $movimientosRecientes = $ultimasEntradas->concat($ultimasSalidas)
            ->sortByDesc('fecha')
            ->take(10)
            ->values();

        // ============================================
        // VIZ 5: Top 5 Productos Almacenados (Bar Chart)
        // ============================================
        $topProductosAlmacenados = \Illuminate\Support\Facades\DB::connection('inventario')->table('donacion_detalles')
            ->join('productos', 'donacion_detalles.id_producto', '=', 'productos.id_producto')
            ->leftJoin('ubicaciones_donaciones', 'donacion_detalles.id_detalle', '=', 'ubicaciones_donaciones.id_detalle')
            ->select('productos.nombre', $stockExpr)
            ->groupBy('productos.id_producto', 'productos.nombre')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $nombresTopProductos = $topProductosAlmacenados->pluck('nombre');
        $cantidadesTopProductos = $topProductosAlmacenados->pluck('total');

        if ($nombresCategorias->isEmpty()) {
            $nombresCategorias = collect(['Sin inventario']);
            $cantidadesCategorias = collect([0]);
        }

        if ($nombresTopProductos->isEmpty()) {
            $nombresTopProductos = collect(['Sin productos']);
            $cantidadesTopProductos = collect([0]);
        }

        $logisticaAlmacenResumen = InventarioOperativa::resumenAlmacenLogistica();
        $logisticaPendientesArmado = InventarioOperativa::solicitudesPendientesArmado(8);
        $logisticaPaquetesRecientes = InventarioOperativa::paquetesAlmacenRecientes(6);

        $coloresUtilizacion = collect($porcentajesUtilizacion)->map(function ($value) {
            if ($value >= 80) {
                return 'rgba(220, 53, 69, 0.85)';
            }
            if ($value >= 50) {
                return 'rgba(255, 193, 7, 0.85)';
            }

            return 'rgba(40, 167, 69, 0.85)';
        })->values();

        return view('inventario::home-almacenista', compact(
            // KPIs
            'totalAlmacenes',
            'totalEstantes',
            'totalEspacios',
            'espaciosDisponibles',
            'espaciosLlenos',
            'productosInventario',
            // Viz 1: Utilización por Almacén
            'nombresAlmacenes',
            'porcentajesUtilizacion',
            'coloresUtilizacion',
            // Viz 2: Productos por Categoría
            'nombresCategorias',
            'cantidadesCategorias',
            // Viz 3: Estado Espacios (ya están en KPIs)
            // Viz 4: Movimientos Recientes
            'movimientosRecientes',
            // Viz 5: Top Productos
            'nombresTopProductos',
            'cantidadesTopProductos',
            // Cohesión con logística (rol almacenero)
            'logisticaAlmacenResumen',
            'logisticaPendientesArmado',
            'logisticaPaquetesRecientes',
        ));
    }

    /**
     * Dashboard específico para Voluntario
     */
    private function dashboardVoluntario()
    {
        // ============================================
        // KPIs para Voluntario
        // ============================================
        $totalDonaciones = \Modules\Inventario\Models\Donacione::count();

        // Donaciones del mes actual
        $donacionesMesActual = \Modules\Inventario\Models\Donacione::whereYear('fecha', \Carbon\Carbon::now()->year)
            ->whereMonth('fecha', \Carbon\Carbon::now()->month)
            ->count();

        $totalDonantes = \Modules\Inventario\Models\Donante::count();

        // Total donaciones en dinero
        $totalDonacionesDinero = \Modules\Inventario\Models\DonacionesDinero::sum('monto');

        // Promedio de donaciones por día (últimos 30 días)
        $donacionesUltimos30Dias = \Modules\Inventario\Models\Donacione::where('fecha', '>=', \Carbon\Carbon::now()->subDays(30))->count();
        $promedioDonacionesDia = $donacionesUltimos30Dias > 0 ? round($donacionesUltimos30Dias / 30, 1) : 0;

        // Solicitudes de recolección pendientes
        $solicitudesPendientes = \Modules\Inventario\Models\SolicitudesRecoleccion::where('estado', 'pendiente')->count();

        // ============================================
        // VIZ 1: Tendencia de Donaciones (12 meses) - LINE CHART
        // ============================================
        $donacionesPorMes = \Modules\Inventario\Models\Donacione::selectRaw(YearMonthSql::yearMonthSelect('fecha', 'inventario').', COUNT(*) as total')
            ->where('fecha', '>=', \Carbon\Carbon::now()->subMonths(12))
            ->groupByRaw(YearMonthSql::yearMonthGroupByRaw('fecha', 'inventario'))
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        $mesesLabels = [];
        $cantidadesDonaciones = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = \Carbon\Carbon::now()->subMonths($i);
            $mesesLabels[] = $fecha->locale('es')->isoFormat('MMM YYYY');

            $registro = $donacionesPorMes->first(function ($item) use ($fecha) {
                return $item->mes == $fecha->month && $item->anio == $fecha->year;
            });

            $cantidadesDonaciones[] = $registro ? $registro->total : 0;
        }

        // ============================================
        // VIZ 2: Top 5 Categorías de Productos Donados - HORIZONTAL BAR CHART
        // ============================================
        $topCategorias = \Modules\Inventario\Models\DonacionDetalle::join('productos', 'donacion_detalles.id_producto', '=', 'productos.id_producto')
            ->join('categorias_productos', 'productos.id_categoria', '=', 'categorias_productos.id_categoria')
            ->select('categorias_productos.nombre', \Illuminate\Support\Facades\DB::raw('COUNT(donacion_detalles.id_detalle) as total'))
            ->groupBy('categorias_productos.nombre')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $nombresTopCategorias = $topCategorias->pluck('nombre');
        $cantidadesTopCategorias = $topCategorias->pluck('total');

        // ============================================
        // VIZ 3: Estado de Solicitudes de Recolección - DOUGHNUT CHART
        // ============================================
        $solicitudesPorEstado = \Modules\Inventario\Models\SolicitudesRecoleccion::selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $estadosSolicitudes = $solicitudesPorEstado->keys();
        $cantidadesSolicitudes = $solicitudesPorEstado->values();

        // ============================================
        // VIZ 4: Donaciones en Especie vs Dinero (12 meses) - LINE CHART
        // ============================================
        // Para donaciones en especie, contar las donaciones que tienen detalles (productos)
        $donacionesEspeciePorMes = \Modules\Inventario\Models\Donacione::join('donacion_detalles', 'donaciones.id_donacion', '=', 'donacion_detalles.id_donacion')
            ->selectRaw(YearMonthSql::yearMonthSelect('donaciones.fecha', 'inventario').', COUNT(DISTINCT donaciones.id_donacion) as total')
            ->where('donaciones.fecha', '>=', \Carbon\Carbon::now()->subMonths(12))
            ->groupByRaw(YearMonthSql::yearMonthGroupByRaw('donaciones.fecha', 'inventario'))
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        // Para donaciones en dinero
        $donacionesDineroPorMes = \Modules\Inventario\Models\DonacionesDinero::join('donaciones', 'donaciones_dinero.id_donacion', '=', 'donaciones.id_donacion')
            ->selectRaw(YearMonthSql::yearMonthSelect('donaciones.fecha', 'inventario').', COUNT(*) as total')
            ->where('donaciones.fecha', '>=', \Carbon\Carbon::now()->subMonths(12))
            ->groupByRaw(YearMonthSql::yearMonthGroupByRaw('donaciones.fecha', 'inventario'))
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();

        $mesesComparacionLabels = [];
        $cantidadesDonacionesEspecie = [];
        $cantidadesDonacionesDinero = [];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = \Carbon\Carbon::now()->subMonths($i);
            $mesesComparacionLabels[] = $fecha->locale('es')->isoFormat('MMM YYYY');

            $registroEspecie = $donacionesEspeciePorMes->first(function ($item) use ($fecha) {
                return $item->mes == $fecha->month && $item->anio == $fecha->year;
            });
            $cantidadesDonacionesEspecie[] = $registroEspecie ? $registroEspecie->total : 0;

            $registroDinero = $donacionesDineroPorMes->first(function ($item) use ($fecha) {
                return $item->mes == $fecha->month && $item->anio == $fecha->year;
            });
            $cantidadesDonacionesDinero[] = $registroDinero ? $registroDinero->total : 0;
        }

        // ============================================
        // VIZ 5: Top 5 Donantes Más Activos - BAR CHART
        // ============================================
        $topDonantes = \Modules\Inventario\Models\Donante::leftJoin('donaciones', 'donantes.id_donante', '=', 'donaciones.id_donante')
            ->select('donantes.nombre', \Illuminate\Support\Facades\DB::raw('COUNT(donaciones.id_donacion) as total_donaciones'))
            ->groupBy('donantes.id_donante', 'donantes.nombre')
            ->orderByDesc('total_donaciones')
            ->take(5)
            ->get();

        $nombresTopDonantes = $topDonantes->pluck('nombre');
        $cantidadesTopDonantes = $topDonantes->pluck('total_donaciones');

        // ============================================
        // VIZ 6: Actividad Reciente - TIMELINE
        // ============================================
        $actividadesRecientes = [];

        // Últimas donaciones
        $ultimasDonaciones = \Modules\Inventario\Models\Donacione::with('donante')
            ->orderBy('fecha', 'desc')
            ->take(5)
            ->get()
            ->map(function ($donacion) {
                return [
                    'tipo' => 'donacion',
                    'icono' => 'fas fa-gift',
                    'color' => 'success',
                    'titulo' => 'Nueva Donación',
                    'descripcion' => 'Donación de ' . ($donacion->donante ? $donacion->donante->nombre : 'Anónimo'),
                    'fecha' => \Carbon\Carbon::parse($donacion->fecha)
                ];
            });

        // Últimas solicitudes de recolección
        $ultimasSolicitudes = \Modules\Inventario\Models\SolicitudesRecoleccion::orderBy('fecha_creacion', 'desc')
            ->take(5)
            ->get()
            ->map(function ($solicitud) {
                return [
                    'tipo' => 'solicitud',
                    'icono' => 'fas fa-truck',
                    'color' => 'info',
                    'titulo' => 'Solicitud de Recolección',
                    'descripcion' => 'Estado: ' . $solicitud->estado . ' - ' . ($solicitud->direccion_recoleccion ?? 'Sin dirección'),
                    'fecha' => \Carbon\Carbon::parse($solicitud->fecha_creacion)
                ];
            });

        $actividadesRecientes = $ultimasDonaciones->concat($ultimasSolicitudes)
            ->sortByDesc('fecha')
            ->take(10)
            ->values();

        return view('inventario::home-voluntario', compact(
            // KPIs
            'totalDonaciones',
            'donacionesMesActual',
            'totalDonantes',
            'totalDonacionesDinero',
            'promedioDonacionesDia',
            'solicitudesPendientes',
            // Viz 1: Tendencia Donaciones
            'mesesLabels',
            'cantidadesDonaciones',
            // Viz 2: Top Categorías
            'nombresTopCategorias',
            'cantidadesTopCategorias',
            // Viz 3: Estado Solicitudes
            'estadosSolicitudes',
            'cantidadesSolicitudes',
            // Viz 4: Donaciones Especie vs Dinero
            'mesesComparacionLabels',
            'cantidadesDonacionesEspecie',
            'cantidadesDonacionesDinero',
            // Viz 5: Top Donantes
            'nombresTopDonantes',
            'cantidadesTopDonantes',
            // Viz 6: Actividades Recientes
            'actividadesRecientes'
        ));
    }
}







