<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Producto;
use App\Models\CategoriaProducto;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Reserva;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard del cliente
     */
    public function index()
    {
        // Verificar que el usuario tenga rol de Cliente (id_rol = 5)
        if (Auth::user()->id_rol !== 5) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Productos disponibles con categorías
        $productos = Producto::with('categoria')
            ->where('disponible', true)
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio,
                    'tiempo_preparacion' => $producto->tiempo_preparacion ?? 15,
                    'rating' => 4.5, // Valor fijo por ahora
                    'categoria' => $producto->id_categoria,
                    'imagen' => $producto->imagen ?? '/images/placeholder-food.jpg'
                ];
            });

        // Mesas disponibles para reservas
        $mesasDisponibles = Mesa::where('estado', 'disponible')
            ->where('activa', true)
            ->orderBy('numero_mesa')
            ->get()
            ->map(function ($mesa) {
                return [
                    'id' => $mesa->id_mesa,
                    'numero' => $mesa->numero_mesa,
                    'capacidad' => $mesa->capacidad,
                    'ubicacion' => $mesa->ubicacion ?? 'Salón principal',
                    'precio_base' => $mesa->precio_base ?? 0
                ];
            });

        // Reservas del cliente
        $reservas = Reserva::where('cliente_id', Auth::user()->id_usuario)
            ->with(['mesa', 'cliente'])
            ->latest('fecha_reserva')
            ->take(5)
            ->get()
            ->map(function ($reserva) {
                return [
                    'id' => $reserva->id_reserva,
                    'fecha' => Carbon::parse($reserva->fecha_reserva)->format('d/m/Y'),
                    'hora_inicio' => $reserva->hora_inicio,
                    'hora_fin' => $reserva->hora_fin,
                    'mesa' => $reserva->mesa ? 'Mesa #' . $reserva->mesa->numero_mesa : 'Por asignar',
                    'mesa_ubicacion' => $reserva->mesa->ubicacion ?? null,
                    'personas' => $reserva->numero_personas,
                    'observaciones' => $reserva->observaciones,
                    'estado' => $reserva->estado,
                    'estadoColor' => $this->getColorEstadoReserva($reserva->estado),
                    'puede_cancelar' => in_array($reserva->estado, ['pendiente', 'confirmada']) && 
                                       Carbon::parse($reserva->fecha_reserva)->isFuture(),
                    'fecha_creacion' => $reserva->created_at->format('d/m/Y H:i')
                ];
            });

        // Historial de pedidos
        $historialPedidos = Pedido::where('cliente_id', Auth::user()->id_usuario)
            ->with(['detalles.producto', 'mesa'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($pedido) {
                return [
                    'id' => $pedido->id_pedido,
                    'fecha' => $pedido->created_at->format('d/m/Y H:i'),
                    'descripcion' => $this->getDescripcionPedido($pedido->detalles),
                    'total' => number_format($pedido->total ?? 0, 2),
                    'estado' => ucfirst($pedido->estado),
                    'estadoColor' => $this->getColorEstadoPedido($pedido->estado),
                    'mesa' => $pedido->mesa ? 'Mesa #' . $pedido->mesa->numero_mesa : 'Para llevar',
                    'es_para_llevar' => $pedido->es_para_llevar ?? false,
                    'items' => $pedido->detalles->map(function ($detalle) {
                        return [
                            'nombre' => $detalle->producto->nombre,
                            'cantidad' => $detalle->cantidad,
                            'precio_unitario' => $detalle->precio_unitario
                        ];
                    })
                ];
            });

        // Estadísticas del cliente
        $estadisticas = [
            'pedidos_totales' => Pedido::where('cliente_id', Auth::user()->id_usuario)->count(),
            'pedidos_este_mes' => Pedido::where('cliente_id', Auth::user()->id_usuario)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'gasto_total' => Pedido::where('cliente_id', Auth::user()->id_usuario)
                ->where('estado', '!=', 'cancelado')
                ->sum('total'),
            'reservas_activas' => Reserva::where('cliente_id', Auth::user()->id_usuario)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->where('fecha_reserva', '>=', Carbon::today())
                ->count(),
            'plato_favorito' => $this->getPlatoFavorito(Auth::user()->id_usuario)
        ];

        return Inertia::render('Cliente/Dashboard', [
            'productos' => $productos,
            'mesasDisponibles' => $mesasDisponibles,
            'reservas' => $reservas,
            'historialPedidos' => $historialPedidos,
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * Enviar un nuevo pedido
     */
    public function enviarPedido(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:producto,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'es_para_llevar' => 'required|boolean',
            'mesa_id' => 'nullable|exists:mesa,id_mesa',
            'observaciones_generales' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Crear el pedido
            $pedido = Pedido::create([
                'cliente_id' => Auth::id(),
                'mesa_id' => $request->es_para_llevar ? null : $request->mesa_id,
                'total' => $request->total,
                'estado' => 'pendiente',
                'es_para_llevar' => $request->es_para_llevar,
                'observaciones' => $request->observaciones_generales,
                'fecha_pedido' => now()
            ]);

            // Crear detalles del pedido
            foreach ($request->items as $item) {
                PedidoDetalle::create([
                    'pedido_id' => $pedido->id_pedido,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio'],
                    'observaciones' => $item['observaciones'] ?? null
                ]);
            }

            // Si es para mesa específica, marcar mesa como ocupada
            if (!$request->es_para_llevar && $request->mesa_id) {
                Mesa::where('id_mesa', $request->mesa_id)->update([
                    'estado' => 'reservada'
                ]);
            }

            DB::commit();

            return back()->with('success', 'Pedido enviado exitosamente. Recibirás una notificación cuando esté listo.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al enviar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Crear una nueva reserva
     */
    public function nuevaReserva()
    {
        $mesasDisponibles = Mesa::where('estado', 'disponible')
            ->where('activa', true)
            ->orderBy('numero_mesa')
            ->get();
        
        return Inertia::render('Cliente/NuevaReserva', [
            'mesas' => $mesasDisponibles
        ]);
    }

    /**
     * Guardar nueva reserva
     */
    public function guardarReserva(Request $request)
    {
        $request->validate([
            'fecha_reserva' => 'required|date|after:now',
            'numero_personas' => 'required|integer|min:1|max:12',
            'mesa_id' => 'nullable|exists:mesa,id_mesa',
            'observaciones' => 'nullable|string|max:500'
        ]);

        Reserva::create([
            'cliente_id' => Auth::id(),
            'fecha_reserva' => $request->fecha_reserva,
            'numero_personas' => $request->numero_personas,
            'mesa_id' => $request->mesa_id,
            'observaciones' => $request->observaciones,
            'estado' => 'pendiente'
        ]);

        return redirect()->route('cliente.dashboard')
            ->with('success', 'Reserva creada exitosamente. Te confirmaremos la disponibilidad pronto.');
    }

    /**
     * Cancelar una reserva
     */
    public function cancelarReserva($id)
    {
        $reserva = Reserva::where('id_reserva', $id)
            ->where('cliente_id', Auth::id())
            ->firstOrFail();

        if (Carbon::parse($reserva->fecha_reserva)->isPast()) {
            return back()->with('error', 'No puedes cancelar una reserva pasada');
        }

        $reserva->update(['estado' => 'cancelada']);

        return back()->with('success', 'Reserva cancelada exitosamente');
    }

    /**
     * Repetir un pedido anterior
     */
    public function repetirPedido(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedido,id_pedido'
        ]);

        $pedidoOriginal = Pedido::with('detalles')
            ->where('id_pedido', $request->pedido_id)
            ->where('cliente_id', Auth::id())
            ->firstOrFail();

        // Aquí redirigirías al dashboard con el carrito precargado
        // Por simplicidad, solo mostramos mensaje de éxito
        
        return back()->with('success', 'Pedido agregado al carrito. Revisa tu pedido actual.');
    }

    /**
     * Calificar un pedido
     */
    public function calificarPedido($id)
    {
        $pedido = Pedido::where('id_pedido', $id)
            ->where('cliente_id', Auth::id())
            ->firstOrFail();

        return Inertia::render('Cliente/CalificarPedido', [
            'pedido' => $pedido
        ]);
    }

    /**
     * Obtener estado de reserva
     */
    private function getEstadoReserva($reserva)
    {
        if ($reserva->estado === 'cancelada') return 'Cancelada';
        if (Carbon::parse($reserva->fecha_reserva)->isPast()) return 'Completada';
        if ($reserva->estado === 'confirmada') return 'Confirmada';
        return 'Pendiente';
    }

    /**
     * Obtener color según estado de reserva
     */
    private function getColorEstadoReserva($reserva)
    {
        $estado = $this->getEstadoReserva($reserva);
        
        $colores = [
            'Confirmada' => 'success',
            'Pendiente' => 'warning',
            'Completada' => 'info',
            'Cancelada' => 'danger'
        ];

        return $colores[$estado] ?? 'secondary';
    }

    /**
     * Obtener color según estado del pedido
     */
    private function getColorEstadoPedido($estado)
    {
        $colores = [
            'pendiente' => 'warning',
            'confirmado' => 'info',
            'preparando' => 'primary',
            'listo' => 'success',
            'entregado' => 'success',
            'cancelado' => 'danger'
        ];

        return $colores[$estado] ?? 'secondary';
    }

    /**
     * Obtener descripción resumida del pedido
     */
    private function getDescripcionPedido($detalles)
    {
        if ($detalles->count() === 0) return 'Pedido vacío';
        
        $primer = $detalles->first()->producto->nombre;
        $cantidad = $detalles->count();
        
        if ($cantidad === 1) {
            return $primer;
        } else {
            return $primer . ' + ' . ($cantidad - 1) . ' más';
        }
    }

    /**
     * Obtener el plato favorito del cliente
     */
    private function getPlatoFavorito($clienteId)
    {
        $platoFavorito = PedidoDetalle::whereHas('pedido', function($query) use ($clienteId) {
                $query->where('cliente_id', $clienteId)
                      ->where('estado', '!=', 'cancelado');
            })
            ->with('producto')
            ->select('producto_id', DB::raw('SUM(cantidad) as total_cantidad'))
            ->groupBy('producto_id')
            ->orderBy('total_cantidad', 'desc')
            ->first();

        return $platoFavorito ? $platoFavorito->producto->nombre : 'Ninguno aún';
    }
}