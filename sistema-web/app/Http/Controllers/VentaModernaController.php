<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Pedido;
use App\Models\Mesa;
use App\Models\User;
use App\Models\Producto;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VentaModernaController extends Controller
{
    /**
     * Display a listing of ventas
     */
    public function index(Request $request)
    {
        $query = Venta::with(['usuario', 'mesa', 'metodoPago', 'detalles.producto']);
        
        // Filtros
        if ($request->fecha_desde) {
            $query->whereDate('fecha_venta', '>=', $request->fecha_desde);
        }
        
        if ($request->fecha_hasta) {
            $query->whereDate('fecha_venta', '<=', $request->fecha_hasta);
        }
        
        if ($request->metodo_pago_id) {
            $query->where('id_metodo_pago', $request->metodo_pago_id);
        }
        
        if ($request->cajero_id) {
            $query->where('id_usuario', $request->cajero_id);
        }
        
        $ventas = $query->orderBy('fecha_venta', 'desc')->paginate(15);
        
        // Estadísticas del día
        $estadisticasHoy = $this->obtenerEstadisticasHoy();
        
        return Inertia::render('Ventas/Index', [
            'ventas' => $ventas,
            'metodosPago' => MetodoPago::activos()->get(),
            'cajeros' => User::where('id_rol', 2)->where('estado', true)->get(),
            'estadisticas' => $estadisticasHoy,
            'filtros' => $request->only(['fecha_desde', 'fecha_hasta', 'metodo_pago_id', 'cajero_id'])
        ]);
    }

    /**
     * Show the form for creating a new venta
     */
    public function create(Request $request)
    {
        $productos = Producto::where('disponible', true)
                           ->with('categoria')
                           ->get()
                           ->groupBy('categoria.nombre');
        $metodosPago = MetodoPago::activos()->get();
        $mesasOcupadas = Mesa::ocupadas()->with('pedidoActual.detalles.producto')->get();
        
        // Si viene de un pedido específico
        $pedidoSeleccionado = null;
        if ($request->pedido_id) {
            $pedidoSeleccionado = Pedido::with(['detalles.producto', 'mesa', 'cliente'])
                                       ->find($request->pedido_id);
        }
        
        return Inertia::render('Ventas/Create', [
            'productos' => $productos,
            'metodosPago' => $metodosPago,
            'mesasOcupadas' => $mesasOcupadas,
            'pedidoSeleccionado' => $pedidoSeleccionado
        ]);
    }

    /**
     * Store a newly created venta
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:producto,id_producto',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'id_metodo_pago' => 'required|exists:metodo_pago,id_metodo_pago',
            'monto_recibido' => 'required|numeric|min:0',
            'id_mesa' => 'nullable|exists:mesa,id_mesa',
            'id_pedido' => 'nullable|exists:pedido,id_pedido',
        ]);

        DB::beginTransaction();
        try {
            // Calcular total
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['cantidad'] * $item['precio_unitario'];
            }

            // Calcular vuelto
            $vuelto = $request->monto_recibido - $total;
            if ($vuelto < 0) {
                return back()->with('error', 'El monto recibido es insuficiente');
            }

            // Crear la venta
            $venta = Venta::create([
                'cantidad' => array_sum(array_column($request->items, 'cantidad')),
                'precio' => $total / array_sum(array_column($request->items, 'cantidad')), // Precio promedio
                'total' => $total,
                'receta_id' => null, // Para compatibilidad con el sistema anterior
                'id_usuario' => Auth::user()->id_usuario,
                'id_mesa' => $request->id_mesa,
                'id_metodo_pago' => $request->id_metodo_pago,
                'fecha_venta' => now(),
                'monto_recibido' => $request->monto_recibido,
                'vuelto' => $vuelto,
            ]);

            // Crear los detalles de venta
            foreach ($request->items as $item) {
                VentaDetalle::create([
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['cantidad'] * $item['precio_unitario'],
                    'observaciones' => $item['observaciones'] ?? null,
                    'id_venta' => $venta->id,
                    'id_producto' => $item['id_producto'],
                ]);
            }

            // Si viene de un pedido, marcarlo como entregado
            if ($request->id_pedido) {
                $pedido = Pedido::find($request->id_pedido);
                $pedido->update(['estado' => 'entregado']);
                
                // Liberar la mesa
                if ($pedido->mesa) {
                    $pedido->mesa->update(['estado' => 'disponible']);
                }
            }

            DB::commit();

            return redirect()->route('ventas.show', $venta->id)
                           ->with('success', 'Venta registrada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified venta
     */
    public function show(Venta $venta)
    {
        $venta->load([
            'usuario',
            'mesa',
            'metodoPago',
            'detalles.producto.categoria'
        ]);

        // Verificar si hay pagos pendientes o completados para esta venta
        $pagos = \App\Models\Pago::where('id_venta', $venta->id)
                                ->orderBy('id_pago', 'desc')
                                ->get();

        $pagoPendiente = $pagos->where('estado', 'pendiente')->first();

        $pagoCompletado = $pagos->where('estado', 'completado')->first();

        return Inertia::render('Ventas/Show', [
            'venta' => $venta,
            'pagos' => $pagos,
            'pagoPendiente' => $pagoPendiente,
            'pagoCompletado' => $pagoCompletado
        ]);
    }

    /**
     * Show the form for editing the specified venta
     */
    public function edit(Venta $venta)
    {
        // Solo permitir edición el mismo día
        if ($venta->fecha_venta->format('Y-m-d') !== now()->format('Y-m-d')) {
            return back()->with('error', 'Solo se pueden editar ventas del día actual');
        }

        $venta->load(['detalles.producto']);
        $productos = Producto::where('disponible', true)
                           ->with('categoria')
                           ->get()
                           ->groupBy('categoria.nombre');
        $metodosPago = MetodoPago::activos()->get();

        return Inertia::render('Ventas/Edit', [
            'venta' => $venta,
            'productos' => $productos,
            'metodosPago' => $metodosPago
        ]);
    }

    /**
     * Update the specified venta
     */
    public function update(Request $request, Venta $venta)
    {
        // Solo permitir edición el mismo día
        if ($venta->fecha_venta->format('Y-m-d') !== now()->format('Y-m-d')) {
            return back()->with('error', 'Solo se pueden editar ventas del día actual');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:producto,id_producto',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'id_metodo_pago' => 'required|exists:metodo_pago,id_metodo_pago',
            'monto_recibido' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Eliminar detalles existentes
            $venta->detalles()->delete();

            // Calcular nuevo total
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['cantidad'] * $item['precio_unitario'];
            }

            // Calcular nuevo vuelto
            $vuelto = $request->monto_recibido - $total;
            if ($vuelto < 0) {
                return back()->with('error', 'El monto recibido es insuficiente');
            }

            // Actualizar la venta
            $venta->update([
                'cantidad' => array_sum(array_column($request->items, 'cantidad')),
                'precio' => $total / array_sum(array_column($request->items, 'cantidad')),
                'total' => $total,
                'id_metodo_pago' => $request->id_metodo_pago,
                'monto_recibido' => $request->monto_recibido,
                'vuelto' => $vuelto,
            ]);

            // Crear nuevos detalles
            foreach ($request->items as $item) {
                VentaDetalle::create([
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['cantidad'] * $item['precio_unitario'],
                    'observaciones' => $item['observaciones'] ?? null,
                    'id_venta' => $venta->id,
                    'id_producto' => $item['id_producto'],
                ]);
            }

            DB::commit();

            return redirect()->route('ventas.show', $venta->id)
                           ->with('success', 'Venta actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified venta
     */
    public function destroy(Venta $venta)
    {
        // Solo permitir eliminación el mismo día y con permisos especiales
        if ($venta->fecha_venta->format('Y-m-d') !== now()->format('Y-m-d')) {
            return back()->with('error', 'Solo se pueden eliminar ventas del día actual');
        }

        if (!Auth::user()->isGerente()) {
            return back()->with('error', 'Solo los gerentes pueden eliminar ventas');
        }

        DB::beginTransaction();
        try {
            // Eliminar detalles y venta
            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();

            return redirect()->route('ventas.index')
                           ->with('success', 'Venta eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Generar ticket de venta
     */
    public function ticket(Venta $venta)
    {
        $venta->load([
            'usuario',
            'mesa',
            'metodoPago',
            'detalles.producto'
        ]);

        return Inertia::render('Ventas/Ticket', [
            'venta' => $venta
        ]);
    }

    /**
     * Obtener estadísticas del día
     */
    private function obtenerEstadisticasHoy()
    {
        $hoy = now()->format('Y-m-d');
        
        return [
            'totalVentas' => Venta::whereDate('fecha_venta', $hoy)->sum('total'),
            'cantidadVentas' => Venta::whereDate('fecha_venta', $hoy)->count(),
            'ventaPromedio' => Venta::whereDate('fecha_venta', $hoy)->avg('total'),
            'ventasPorMetodo' => Venta::whereDate('fecha_venta', $hoy)
                                    ->with('metodoPago')
                                    ->get()
                                    ->groupBy('metodoPago.nombre')
                                    ->map(function ($ventas) {
                                        return [
                                            'cantidad' => $ventas->count(),
                                            'total' => $ventas->sum('total')
                                        ];
                                    })
        ];
    }

    /**
     * Obtener ventas del cajero actual (para API)
     */
    public function ventasCajero()
    {
        $ventas = Venta::where('id_usuario', Auth::user()->id_usuario)
                      ->whereDate('fecha_venta', now())
                      ->with(['metodoPago', 'detalles.producto'])
                      ->orderBy('fecha_venta', 'desc')
                      ->get();

        return response()->json($ventas);
    }
}