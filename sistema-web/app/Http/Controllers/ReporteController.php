<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\MovimientoInventario;
use App\Models\ContadorVisitas;
use App\Models\Pedido;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Producto;
use App\Models\Mesa;
use App\Models\Insumo;
use App\Models\Categoria;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }

    /**
     * Vista de Reportes de Ventas - Estadísticas de Visitas
     */
    public function ventas()
    {
        // Estadísticas de visitas
        $hoy = Carbon::today();
        $estaSemana = Carbon::now()->startOfWeek();
        $esteMes = Carbon::now()->startOfMonth();

        // Total de visitas
        $visitasTotales = ContadorVisitas::sum('visitas');
        $visitasHoy = ContadorVisitas::where('fecha', $hoy)->sum('visitas');
        $visitasSemana = ContadorVisitas::where('fecha', '>=', $estaSemana)->sum('visitas');
        $visitasMes = ContadorVisitas::where('fecha', '>=', $esteMes)->sum('visitas');

        // Páginas únicas
        $paginasUnicas = ContadorVisitas::distinct('pagina')->count('pagina');

        // Páginas más visitadas
        $paginasMasVisitadas = ContadorVisitas::select('pagina', DB::raw('SUM(visitas) as total_visitas'))
            ->groupBy('pagina')
            ->orderByDesc('total_visitas')
            ->limit(10)
            ->get();

        // Visitas por día (últimos 7 días)
        $visitasPorDia = ContadorVisitas::select(
                'fecha',
                DB::raw('SUM(visitas) as total_visitas')
            )
            ->where('fecha', '>=', Carbon::now()->subDays(6))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Visitas por mes (últimos 12 meses)
        $visitasPorMes = ContadorVisitas::select(
                DB::raw('DATE_TRUNC(\'month\', fecha) as mes'),
                DB::raw('SUM(visitas) as total_visitas')
            )
            ->where('fecha', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return view('reportes.ventas', compact(
            'visitasTotales',
            'visitasHoy',
            'visitasSemana',
            'visitasMes',
            'paginasUnicas',
            'paginasMasVisitadas',
            'visitasPorDia',
            'visitasPorMes'
        ));
    }

    /**
     * Vista de Reportes de Inventario
     */
    public function inventario()
    {
        // Obtener estadísticas del inventario
        $totalInsumos = Insumo::count();

        // Insumos con stock bajo
        $insumosStockBajoDetalle = Insumo::with(['categoria', 'unidadMedida'])
            ->whereRaw('stock_actual <= stock_minimo')
            ->orderBy('stock_actual')
            ->get();

        $insumosStockBajo = $insumosStockBajoDetalle->count();

        // Valor total del inventario
        $valorTotal = Insumo::sum(DB::raw('stock_actual * precio_unitario'));

        // Total de categorías
        $totalCategorias = Categoria::where('tipo', 'insumo')->count();

        // Movimientos recientes
        $movimientosRecientes = MovimientoInventario::with(['insumo.unidadMedida', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Insumos por categoría
        $insumosPorCategoria = Categoria::where('tipo', 'insumo')
            ->withCount('insumos as total_insumos')
            ->having('total_insumos', '>', 0)
            ->orderByDesc('total_insumos')
            ->get();

        return view('reportes.inventario', compact(
            'totalInsumos',
            'insumosStockBajo',
            'insumosStockBajoDetalle',
            'valorTotal',
            'totalCategorias',
            'movimientosRecientes',
            'insumosPorCategoria'
        ));
    }

    /**
     * Vista de Bitácora del Sistema
     */
    public function bitacora(Request $request)
    {
        $hoy = Carbon::today();
        $estaSemana = Carbon::now()->startOfWeek();
        $esteMes = Carbon::now()->startOfMonth();

        // Estadísticas básicas
        $usuariosActivos = User::whereHas('bitacora', function($q) use ($hoy) {
            $q->whereDate('created_at', $hoy);
        })->count();

        // Eventos (simulados por ahora, necesitarías una tabla bitacora)
        $eventosHoy = 0;
        $eventosSemana = 0;
        $eventosMes = 0;

        // Actividades (simulado con movimientos de inventario)
        $actividades = collect();

        // Usuarios más activos (simulado)
        $usuariosMasActivos = collect();

        // Eventos por tipo (simulado)
        $eventosPorTipo = collect();

        return view('reportes.bitacora', compact(
            'usuariosActivos',
            'eventosHoy',
            'eventosSemana',
            'eventosMes',
            'actividades',
            'usuariosMasActivos',
            'eventosPorTipo'
        ));
    }

    public function dashboard()
    {
        // Estadísticas generales del negocio
        $estadisticas = $this->obtenerEstadisticasGenerales();

        // Visitas por página (Top 10)
        $visitasPorPagina = $this->obtenerVisitasPorPagina();

        // Productos más vendidos
        $productosMasVendidos = $this->obtenerProductosMasVendidos();

        // Reservas por estado
        $reservasPorEstado = $this->obtenerReservasPorEstado();

        // Ingresos mensuales del año actual
        $ingresosMensuales = $this->obtenerIngresosMensuales();

        return Inertia::render('Reportes/Index', [
            'estadisticas' => $estadisticas,
            'visitasPorPagina' => $visitasPorPagina,
            'productosMasVendidos' => $productosMasVendidos,
            'reservasPorEstado' => $reservasPorEstado,
            'ingresosMensuales' => $ingresosMensuales
        ]);
    }

    private function obtenerEstadisticasGenerales()
    {
        $hoy = Carbon::today();
        $esteMes = Carbon::now()->startOfMonth();

        return [
            // Visitas
            'visitas_hoy' => ContadorVisitas::where('fecha', $hoy)->sum('visitas'),
            'visitas_mes' => ContadorVisitas::where('fecha', '>=', $esteMes)->sum('visitas'),
            'visitas_totales' => ContadorVisitas::sum('visitas'),
            'paginas_unicas' => ContadorVisitas::distinct('pagina')->count('pagina'),

            // Ventas
            'ventas_hoy' => Venta::whereDate('created_at', $hoy)->count(),
            'ventas_mes' => Venta::where('created_at', '>=', $esteMes)->count(),
            'ingresos_hoy' => Venta::whereDate('created_at', $hoy)->sum('total'),
            'ingresos_mes' => Venta::where('created_at', '>=', $esteMes)->sum('total'),

            // Pedidos
            'pedidos_pendientes' => Pedido::where('estado', 'pendiente')->count(),
            'pedidos_hoy' => Pedido::whereDate('created_at', $hoy)->count(),
            'pedidos_mes' => Pedido::where('created_at', '>=', $esteMes)->count(),

            // Reservas
            'reservas_pendientes' => Reserva::where('estado', 'pendiente')->count(),
            'reservas_hoy' => Reserva::whereDate('fecha_reserva', $hoy)->count(),
            'reservas_mes' => Reserva::where('fecha_reserva', '>=', $esteMes)->count(),

            // Usuarios
            'usuarios_totales' => User::count(),
            'usuarios_activos' => User::where('estado', true)->count(),

            // Productos
            'productos_totales' => Producto::count(),
            'productos_disponibles' => Producto::where('disponible', true)->count(),

            // Mesas
            'mesas_totales' => Mesa::count(),
            'mesas_disponibles' => Mesa::where('estado', 'disponible')->count(),
            'mesas_ocupadas' => Mesa::where('estado', 'ocupada')->count(),
        ];
    }

    private function obtenerVisitasPorPagina()
    {
        return ContadorVisitas::select('pagina', DB::raw('SUM(visitas) as total_visitas'))
            ->groupBy('pagina')
            ->orderByDesc('total_visitas')
            ->limit(10)
            ->get();
    }

    private function obtenerProductosMasVendidos()
    {
        // Aquí deberías ajustar según tu estructura de ventas
        // Asumiendo que tienes una tabla venta_detalle o similar
        return Producto::select('producto.nombre', 'producto.precio', DB::raw('COUNT(*) as veces_vendido'))
            ->join('venta_detalle', 'producto.id_producto', '=', 'venta_detalle.id_producto')
            ->groupBy('producto.id_producto', 'producto.nombre', 'producto.precio')
            ->orderByDesc('veces_vendido')
            ->limit(10)
            ->get();
    }

    private function obtenerReservasPorEstado()
    {
        return Reserva::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->estado => $item->total];
            });
    }

    private function obtenerIngresosMensuales()
    {
        $año = Carbon::now()->year;

        return Venta::select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('SUM(total) as total')
            )
            ->whereYear('created_at', $año)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->mes => $item->total];
            });
    }

    /**
     * Obtener estadísticas de visitas por período
     */
    public function estadisticasVisitas(Request $request)
    {
        $periodo = $request->get('periodo', 'semana');
        $fechaInicio = Carbon::now()->subDays(7);

        switch ($periodo) {
            case 'mes':
                $fechaInicio = Carbon::now()->subDays(30);
                break;
            case 'año':
                $fechaInicio = Carbon::now()->subYear();
                break;
        }

        $visitas = ContadorVisitas::select('fecha', 'pagina', DB::raw('SUM(visitas) as total'))
            ->where('fecha', '>=', $fechaInicio)
            ->groupBy('fecha', 'pagina')
            ->orderBy('fecha')
            ->get();

        return response()->json($visitas);
    }

    public function getVentasData(Request $request)
    {
        $periodo = $request->get('periodo', 'semana');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        
        switch ($periodo) {
            case 'semana':
                $datos = $this->getVentasSemanales($fechaInicio, $fechaFin);
                break;
            case 'mes':
                $datos = $this->getVentasMensuales($fechaInicio, $fechaFin);
                break;
            case 'año':
                $datos = $this->getVentasAnuales($fechaInicio, $fechaFin);
                break;
            default:
                $datos = $this->getVentasSemanales($fechaInicio, $fechaFin);
        }

        return response()->json($datos);
    }

    private function getVentasSemanales($fechaInicio = null, $fechaFin = null)
    {
        if ($fechaInicio) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfDay();
        } else {
            $fechaInicio = Carbon::now()->subDays(6)->startOfDay();
        }
        if ($fechaFin) {
            $fechaFin = Carbon::parse($fechaFin)->endOfDay();
        } else {
            $fechaFin = Carbon::now()->endOfDay();
        }
        // Obtener datos de ventas de los últimos 7 días o rango seleccionado
        $ventas = Venta::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('SUM(total) as total_ventas'),
            DB::raw('COUNT(*) as cantidad_ventas'),
            DB::raw('SUM(cantidad) as platos_vendidos')
        )
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        // Calcular estadísticas
        $totalVentas = $ventas->sum('cantidad_ventas');
        $totalIngresos = $ventas->sum('total_ventas');
        $totalPlatosVendidos = $ventas->sum('platos_vendidos');
        $promedioVenta = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;
        // Venta más alta y más baja (por ticket individual)
        $ventaMax = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderByDesc('total')
            ->first();
        $ventaMin = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('total')
            ->first();
        return [
            'datos' => $ventas,
            'estadisticas' => [
                'total_ventas' => $totalVentas,
                'ingresos_totales' => $totalIngresos,
                'promedio_venta' => $promedioVenta,
                'platos_vendidos' => $totalPlatosVendidos
            ],
            'venta_maxima' => $ventaMax ? [
                'plato' => $ventaMax->receta->nombre ?? 'N/A',
                'total' => $ventaMax->total
            ] : null,
            'venta_minima' => $ventaMin ? [
                'plato' => $ventaMin->receta->nombre ?? 'N/A',
                'total' => $ventaMin->total
            ] : null
        ];
    }

    private function getVentasMensuales($fechaInicio = null, $fechaFin = null)
    {
        if ($fechaInicio) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfMonth();
        } else {
            $fechaInicio = Carbon::now()->subMonths(11)->startOfMonth();
        }
        if ($fechaFin) {
            $fechaFin = Carbon::parse($fechaFin)->endOfMonth();
        } else {
            $fechaFin = Carbon::now()->endOfMonth();
        }
        // Obtener datos de ventas
        $ventas = Venta::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as fecha'),
            DB::raw('SUM(total) as total_ventas'),
            DB::raw('COUNT(*) as cantidad_ventas'),
            DB::raw('SUM(cantidad) as platos_vendidos')
        )
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        // Calcular estadísticas
        $totalVentas = $ventas->sum('cantidad_ventas');
        $totalIngresos = $ventas->sum('total_ventas');
        $totalPlatosVendidos = $ventas->sum('platos_vendidos');
        $promedioVenta = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;
        // Venta más alta y más baja (por ticket individual)
        $ventaMax = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderByDesc('total')
            ->first();
        $ventaMin = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('total')
            ->first();
        return [
            'datos' => $ventas,
            'estadisticas' => [
                'total_ventas' => $totalVentas,
                'ingresos_totales' => $totalIngresos,
                'promedio_venta' => $promedioVenta,
                'platos_vendidos' => $totalPlatosVendidos
            ],
            'venta_maxima' => $ventaMax ? [
                'plato' => $ventaMax->receta->nombre ?? 'N/A',
                'total' => $ventaMax->total
            ] : null,
            'venta_minima' => $ventaMin ? [
                'plato' => $ventaMin->receta->nombre ?? 'N/A',
                'total' => $ventaMin->total
            ] : null
        ];
    }

    private function getVentasAnuales($fechaInicio = null, $fechaFin = null)
    {
        if ($fechaInicio) {
            $fechaInicio = Carbon::parse($fechaInicio)->startOfYear();
        } else {
            $fechaInicio = Carbon::now()->subYears(4)->startOfYear();
        }
        if ($fechaFin) {
            $fechaFin = Carbon::parse($fechaFin)->endOfYear();
        } else {
            $fechaFin = Carbon::now()->endOfYear();
        }
        // Obtener datos de ventas
        $ventas = Venta::select(
            DB::raw('YEAR(created_at) as fecha'),
            DB::raw('SUM(total) as total_ventas'),
            DB::raw('COUNT(*) as cantidad_ventas'),
            DB::raw('SUM(cantidad) as platos_vendidos')
        )
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
        // Calcular estadísticas
        $totalVentas = $ventas->sum('cantidad_ventas');
        $totalIngresos = $ventas->sum('total_ventas');
        $totalPlatosVendidos = $ventas->sum('platos_vendidos');
        $promedioVenta = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;
        // Venta más alta y más baja (por ticket individual)
        $ventaMax = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderByDesc('total')
            ->first();
        $ventaMin = Venta::with('receta')
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('total')
            ->first();
        return [
            'datos' => $ventas,
            'estadisticas' => [
                'total_ventas' => $totalVentas,
                'ingresos_totales' => $totalIngresos,
                'promedio_venta' => $promedioVenta,
                'platos_vendidos' => $totalPlatosVendidos
            ],
            'venta_maxima' => $ventaMax ? [
                'plato' => $ventaMax->receta->nombre ?? 'N/A',
                'total' => $ventaMax->total
            ] : null,
            'venta_minima' => $ventaMin ? [
                'plato' => $ventaMin->receta->nombre ?? 'N/A',
                'total' => $ventaMin->total
            ] : null
        ];
    }

    private function getEstadisticasSemanales()
    {
        $fechaInicio = Carbon::now()->subDays(6);
        
        // Estadísticas de ventas
        $ventasStats = Venta::where('created_at', '>=', $fechaInicio)
            ->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as total_ventas_monto,
                AVG(total) as promedio_venta
            ')->first();

        // Estadísticas de ingresos
        $ingresosStats = MovimientoInventario::where('created_at', '>=', $fechaInicio)
            ->where('tipo', 'entrada')
            ->count();

        return [
            'total_ventas' => $ventasStats->total_ventas ?? 0,
            'ingresos_totales' => $ingresosStats ?? 0,
            'promedio_venta' => $ventasStats->promedio_venta ?? 0
        ];
    }

    private function getEstadisticasMensuales()
    {
        $fechaInicio = Carbon::now()->startOfMonth();
        
        // Estadísticas de ventas
        $ventasStats = Venta::where('created_at', '>=', $fechaInicio)
            ->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as total_ventas_monto,
                AVG(total) as promedio_venta
            ')->first();

        // Estadísticas de ingresos
        $ingresosStats = MovimientoInventario::where('created_at', '>=', $fechaInicio)
            ->where('tipo', 'entrada')
            ->count();

        return [
            'total_ventas' => $ventasStats->total_ventas ?? 0,
            'ingresos_totales' => $ingresosStats ?? 0,
            'promedio_venta' => $ventasStats->promedio_venta ?? 0
        ];
    }

    private function getEstadisticasAnuales()
    {
        $fechaInicio = Carbon::now()->startOfYear();
        
        // Estadísticas de ventas
        $ventasStats = Venta::where('created_at', '>=', $fechaInicio)
            ->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as total_ventas_monto,
                AVG(total) as promedio_venta
            ')->first();

        // Estadísticas de ingresos
        $ingresosStats = MovimientoInventario::where('created_at', '>=', $fechaInicio)
            ->where('tipo', 'entrada')
            ->count();

        return [
            'total_ventas' => $ventasStats->total_ventas ?? 0,
            'ingresos_totales' => $ingresosStats ?? 0,
            'promedio_venta' => $ventasStats->promedio_venta ?? 0
        ];
    }

    public function parcialSemanal() {
        return view('reportes.parcial_semanal');
    }
    public function parcialMensual() {
        return view('reportes.parcial_mensual');
    }
    public function parcialAnual() {
        return view('reportes.parcial_anual');
    }

    /**
     * API: Obtener estadísticas del contador de visitas
     */
    public function apiContadorVisitas(Request $request)
    {
        $pagina = $request->get('pagina', '/');

        return response()->json([
            'visitas_pagina' => ContadorVisitas::obtenerVisitasTotal($pagina),
            'visitas_totales' => ContadorVisitas::sum('visitas'),
            'paginas_unicas' => ContadorVisitas::distinct('pagina')->count('pagina')
        ]);
    }
} 