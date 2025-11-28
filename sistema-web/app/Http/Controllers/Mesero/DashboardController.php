<?php

namespace App\Http\Controllers\Mesero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Venta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard del mesero
     */
    public function index()
    {
        // Verificar que el usuario tenga rol de Mesero (id_rol = 3)
        if (auth()->user()->id_rol !== 3) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Estadísticas del mesero
        $hoy = Carbon::today();
        $mesasAsignadas = Mesa::count(); // En un sistema real, estarían asignadas al mesero
        
        $pedidosPendientes = Pedido::where('estado', 'pendiente')
            ->whereHas('mesa') // Solo pedidos con mesa (no para llevar)
            ->count();

        $pedidosCompletados = Venta::where('id_usuario', auth()->id())
            ->whereDate('created_at', $hoy)
            ->count();

        $ventasHoy = Venta::where('id_usuario', auth()->id())
            ->whereDate('created_at', $hoy)
            ->sum('total');

        $estadisticas = [
            'mesasAsignadas' => $mesasAsignadas,
            'pedidosPendientes' => $pedidosPendientes,
            'pedidosCompletados' => $pedidosCompletados,
            'ventasHoy' => number_format($ventasHoy, 2)
        ];

        // Obtener todas las mesas con su estado actual
        $mesas = Mesa::with(['pedidoActual'])
            ->get()
            ->map(function ($mesa) {
                return [
                    'id' => $mesa->id,
                    'numero' => $mesa->numero_mesa,
                    'capacidad' => $mesa->capacidad,
                    'ubicacion' => $mesa->ubicacion,
                    'estado' => $mesa->estado,
                    'area' => strtolower($mesa->ubicacion), // 'terraza' o 'interior'
                    'cliente' => $mesa->cliente_actual,
                    'tiempoOcupada' => $mesa->tiempo_ocupacion_minutos
                ];
            });

        // Pedidos activos del mesero
        $pedidos = Pedido::with(['mesa', 'detalles.producto'])
            ->whereIn('estado', ['pendiente', 'preparando', 'listo'])
            ->latest()
            ->get()
            ->map(function ($pedido) {
                return [
                    'id' => $pedido->id,
                    'mesa' => $pedido->mesa->numero_mesa ?? 0,
                    'hora' => $pedido->created_at->format('H:i'),
                    'estado' => ucfirst($pedido->estado),
                    'estadoColor' => $this->getColorEstadoPedido($pedido->estado),
                    'prioridadColor' => $this->getPrioridadColor($pedido->created_at),
                    'items' => $pedido->detalles->map(function ($detalle) {
                        return [
                            'id' => $detalle->id,
                            'cantidad' => $detalle->cantidad,
                            'nombre' => $detalle->producto->nombre
                        ];
                    })
                ];
            });

        return Inertia::render('Mesero/Dashboard', [
            'estadisticas' => $estadisticas,
            'mesas' => $mesas,
            'pedidos' => $pedidos
        ]);
    }

    /**
     * Ocupar una mesa
     */
    public function ocuparMesa(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesa,id',
            'cliente' => 'required|string|max:100'
        ]);

        $mesa = Mesa::findOrFail($request->mesa_id);
        
        if ($mesa->estado !== 'disponible') {
            return back()->with('error', 'La mesa no está disponible');
        }

        $mesa->update([
            'estado' => 'ocupada',
            'cliente_actual' => $request->cliente,
            'hora_ocupacion' => now(),
            'id_mesero' => auth()->id()
        ]);

        return back()->with('success', "Mesa {$mesa->numero_mesa} ocupada por {$request->cliente}");
    }

    /**
     * Liberar una mesa
     */
    public function liberarMesa(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesa,id'
        ]);

        $mesa = Mesa::findOrFail($request->mesa_id);
        
        $mesa->update([
            'estado' => 'limpieza',
            'cliente_actual' => null,
            'hora_ocupacion' => null,
            'id_mesero' => auth()->id()
        ]);

        return back()->with('success', "Mesa {$mesa->numero_mesa} liberada");
    }

    /**
     * Marcar mesa como limpia
     */
    public function marcarLimpia(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesa,id'
        ]);

        $mesa = Mesa::findOrFail($request->mesa_id);
        
        $mesa->update([
            'estado' => 'disponible'
        ]);

        return back()->with('success', "Mesa {$mesa->numero_mesa} lista para nuevos clientes");
    }

    /**
     * Ir a tomar un nuevo pedido
     */
    public function nuevoPedido(Request $request)
    {
        $mesaNumero = $request->query('mesa');
        
        return Inertia::render('Mesero/NuevoPedido', [
            'mesa_preseleccionada' => $mesaNumero,
            'mesas_disponibles' => Mesa::whereIn('estado', ['disponible', 'ocupada'])->get(),
            'productos' => \App\Models\Producto::with('categoria')->where('disponible', true)->get()
        ]);
    }

    /**
     * Ver detalle de un pedido
     */
    public function verPedido($id)
    {
        $pedido = Pedido::with(['mesa', 'detalles.producto', 'usuario'])
            ->findOrFail($id);

        return Inertia::render('Mesero/DetallePedido', [
            'pedido' => $pedido
        ]);
    }

    /**
     * Marcar pedido como listo para entregar
     */
    public function marcarListo(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedido,id'
        ]);

        $pedido = Pedido::findOrFail($request->pedido_id);
        
        $pedido->update([
            'estado' => 'entregado',
            'hora_entrega' => now()
        ]);

        return back()->with('success', 'Pedido marcado como entregado');
    }

    /**
     * Obtener color según estado del pedido
     */
    private function getColorEstadoPedido($estado)
    {
        $colores = [
            'pendiente' => 'danger',
            'preparando' => 'warning', 
            'listo' => 'success',
            'entregado' => 'info'
        ];

        return $colores[$estado] ?? 'secondary';
    }

    /**
     * Obtener color de prioridad basado en tiempo de espera
     */
    private function getPrioridadColor($createdAt)
    {
        $minutosEspera = $createdAt->diffInMinutes(now());
        
        if ($minutosEspera > 20) return 'danger';
        if ($minutosEspera > 10) return 'warning';
        return 'info';
    }
}