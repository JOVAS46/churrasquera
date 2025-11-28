<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Mesa;
use App\Models\User;
use App\Models\Producto;
use App\Models\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PedidoController extends Controller
{
    /**
     * Display a listing of pedidos
     */
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente', 'mesero', 'cocinero', 'mesa', 'detalles.producto']);
        
        // Filtros
        if ($request->estado) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->fecha) {
            $query->whereDate('fecha_pedido', $request->fecha);
        }
        
        if ($request->mesa_id) {
            $query->where('id_mesa', $request->mesa_id);
        }
        
        $pedidos = $query->orderBy('fecha_pedido', 'desc')->paginate(10);
        
        return Inertia::render('Pedidos/Index', [
            'pedidos' => $pedidos,
            'mesas' => Mesa::all(),
            'filtros' => $request->only(['estado', 'fecha', 'mesa_id'])
        ]);
    }

    /**
     * Show the form for creating a new pedido
     */
    public function create(Request $request)
    {
        $mesasDisponibles = Mesa::disponibles()->with('mesero')->get();
        $productos = Producto::where('disponible', true)
                           ->with('categoria')
                           ->get()
                           ->groupBy('categoria.nombre');
        $meseros = User::where('id_rol', 3)->where('estado', true)->get();
        $cocineros = User::where('id_rol', 4)->where('estado', true)->get();
        
        // Si viene una mesa específica, la preseleccionamos
        $mesaSeleccionada = null;
        if ($request->mesa_id) {
            $mesaSeleccionada = Mesa::find($request->mesa_id);
        }
        
        return Inertia::render('Pedidos/Create', [
            'mesas' => $mesasDisponibles,
            'productos' => $productos,
            'meseros' => $meseros,
            'cocineros' => $cocineros,
            'mesaSeleccionada' => $mesaSeleccionada
        ]);
    }

    /**
     * Store a newly created pedido
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_mesa' => 'required|exists:mesa,id_mesa',
            'id_mesero' => 'required|exists:usuario,id_usuario',
            'observaciones' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:producto,id_producto',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.observaciones' => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            // Crear el pedido
            $pedido = Pedido::create([
                'fecha_pedido' => now(),
                'estado' => 'pendiente',
                'total' => 0,
                'observaciones' => $request->observaciones,
                'id_cliente' => Auth::user()->id_usuario ?? null,
                'id_mesero' => $request->id_mesero,
                'id_mesa' => $request->id_mesa,
            ]);

            // Crear los detalles y calcular total
            $total = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                
                PedidoDetalle::create([
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal,
                    'observaciones' => $item['observaciones'] ?? null,
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $item['id_producto'],
                ]);
                
                $total += $subtotal;
            }

            // Actualizar el total del pedido
            $pedido->update(['total' => $total]);

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id_pedido)
                           ->with('success', 'Pedido creado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified pedido
     */
    public function show(Pedido $pedido)
    {
        $pedido->load([
            'cliente',
            'mesero', 
            'cocinero',
            'mesa',
            'detalles.producto.categoria'
        ]);

        // Obtener pagos relacionados al pedido
        $pagosExistentes = \App\Models\Pago::where('id_pedido', $pedido->id_pedido)
                                         ->orderBy('id_pago', 'desc')
                                         ->get();

        return Inertia::render('Pedidos/Show', [
            'pedido' => $pedido,
            'pagosExistentes' => $pagosExistentes
        ]);
    }

    /**
     * Show the form for editing the specified pedido
     */
    public function edit(Pedido $pedido)
    {
        if ($pedido->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar pedidos pendientes');
        }

        $pedido->load(['detalles.producto']);
        $productos = Producto::where('disponible', true)
                           ->with('categoria')
                           ->get()
                           ->groupBy('categoria.nombre');
        $meseros = User::where('id_rol', 3)->where('estado', true)->get();
        $cocineros = User::where('id_rol', 4)->where('estado', true)->get();

        return Inertia::render('Pedidos/Edit', [
            'pedido' => $pedido,
            'productos' => $productos,
            'meseros' => $meseros,
            'cocineros' => $cocineros
        ]);
    }

    /**
     * Update the specified pedido
     */
    public function update(Request $request, Pedido $pedido)
    {
        if ($pedido->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar pedidos pendientes');
        }

        $request->validate([
            'id_mesero' => 'required|exists:usuario,id_usuario',
            'observaciones' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:producto,id_producto',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.observaciones' => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            // Eliminar detalles existentes
            $pedido->detalles()->delete();

            // Crear nuevos detalles y calcular total
            $total = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['cantidad'] * $item['precio_unitario'];
                
                PedidoDetalle::create([
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $subtotal,
                    'observaciones' => $item['observaciones'] ?? null,
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $item['id_producto'],
                ]);
                
                $total += $subtotal;
            }

            // Actualizar el pedido
            $pedido->update([
                'total' => $total,
                'observaciones' => $request->observaciones,
                'id_mesero' => $request->id_mesero,
            ]);

            DB::commit();

            return redirect()->route('pedidos.show', $pedido->id_pedido)
                           ->with('success', 'Pedido actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified pedido
     */
    public function destroy(Pedido $pedido)
    {
        if (!in_array($pedido->estado, ['pendiente', 'cancelado'])) {
            return back()->with('error', 'Solo se pueden eliminar pedidos pendientes o cancelados');
        }

        DB::beginTransaction();
        try {
            // Eliminar detalles y pedido
            $pedido->detalles()->delete();
            $pedido->delete();

            DB::commit();

            return redirect()->route('pedidos.index')
                           ->with('success', 'Pedido eliminado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del pedido
     */
    public function cambiarEstado(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_preparacion,listo,entregado,cancelado',
            'id_cocinero' => 'nullable|exists:usuario,id_usuario'
        ]);

        $estado_anterior = $pedido->estado;

        DB::beginTransaction();
        try {
            $updateData = ['estado' => $request->estado];

            // Si pasa a en_preparacion, asignar cocinero
            if ($request->estado === 'en_preparacion' && $request->id_cocinero) {
                $updateData['id_cocinero'] = $request->id_cocinero;
            }

            $pedido->update($updateData);

            DB::commit();

            return back()->with('success', "Estado cambiado de {$estado_anterior} a {$request->estado}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener pedidos por estado para APIs
     */
    public function porEstado($estado)
    {
        $pedidos = Pedido::where('estado', $estado)
                        ->with(['cliente', 'mesero', 'cocinero', 'mesa', 'detalles.producto'])
                        ->orderBy('fecha_pedido')
                        ->get();

        return response()->json($pedidos);
    }

    /**
     * Endpoint de prueba - Crear pedido de ejemplo
     */
    public function crearPedidoPrueba()
    {
        try {
            // Verificar datos necesarios
            $mesero = User::where('id_rol', 3)->where('estado', true)->first();
            $mesa = Mesa::where('estado', 'disponible')->first();
            $productos = Producto::where('disponible', true)->take(3)->get();

            if (!$mesero) {
                return response()->json([
                    'error' => 'No hay meseros disponibles. Ejecuta: php artisan db:seed --class=UsuarioSeeder'
                ], 400);
            }

            if (!$mesa) {
                return response()->json([
                    'error' => 'No hay mesas disponibles. Ejecuta: php artisan db:seed --class=MesaSeeder'
                ], 400);
            }

            if ($productos->count() === 0) {
                return response()->json([
                    'error' => 'No hay productos disponibles. Ejecuta: php artisan db:seed --class=ProductoSeeder'
                ], 400);
            }

            DB::beginTransaction();

            // Crear el pedido
            $pedido = Pedido::create([
                'fecha_pedido' => now(),
                'estado' => 'pendiente',
                'total' => 0,
                'observaciones' => 'Pedido de prueba - Cliente con prisa',
                'id_cliente' => null,
                'id_mesero' => $mesero->id_usuario,
                'id_mesa' => $mesa->id_mesa,
            ]);

            // Crear detalles
            $total = 0;
            foreach ($productos as $index => $producto) {
                $cantidad = rand(1, 3);
                $subtotal = $cantidad * $producto->precio;

                PedidoDetalle::create([
                    'cantidad' => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                    'observaciones' => $index === 0 ? 'Término medio' : null,
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $producto->id_producto,
                ]);

                $total += $subtotal;
            }

            // Actualizar total
            $pedido->update(['total' => $total]);

            DB::commit();

            // Cargar relaciones
            $pedido->load(['mesero', 'mesa', 'detalles.producto']);

            return response()->json([
                'success' => true,
                'message' => '¡Pedido de prueba creado exitosamente!',
                'pedido' => $pedido,
                'urls' => [
                    'ver_pedido' => route('pedidos.show', $pedido->id_pedido),
                    'lista_pedidos' => route('pedidos.index'),
                ],
                'datos' => [
                    'id_pedido' => $pedido->id_pedido,
                    'mesa' => "Mesa #{$mesa->numero}",
                    'mesero' => $mesero->nombre,
                    'total' => "Bs. {$pedido->total}",
                    'productos' => $pedido->detalles->count(),
                    'estado' => $pedido->estado,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al crear pedido de prueba',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint de prueba - Obtener estadísticas
     */
    public function estadisticas()
    {
        $hoy = now()->startOfDay();

        return response()->json([
            'total_pedidos' => Pedido::count(),
            'pedidos_hoy' => Pedido::whereDate('fecha_pedido', $hoy)->count(),
            'por_estado' => [
                'pendientes' => Pedido::where('estado', 'pendiente')->count(),
                'en_preparacion' => Pedido::where('estado', 'en_preparacion')->count(),
                'listos' => Pedido::where('estado', 'listo')->count(),
                'entregados' => Pedido::where('estado', 'entregado')->count(),
                'cancelados' => Pedido::where('estado', 'cancelado')->count(),
            ],
            'total_ventas_hoy' => Pedido::whereDate('fecha_pedido', $hoy)
                                       ->where('estado', '!=', 'cancelado')
                                       ->sum('total'),
            'mesas_ocupadas' => Mesa::where('estado', 'ocupada')->count(),
            'mesas_disponibles' => Mesa::where('estado', 'disponible')->count(),
            'ultimo_pedido' => Pedido::with(['mesero', 'mesa'])
                                    ->orderBy('id_pedido', 'desc')
                                    ->first(),
        ]);
    }
}