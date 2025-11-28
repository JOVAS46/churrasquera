<?php

namespace App\Http\Controllers\Cocinero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Insumo;
use App\Models\Producto;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard del cocinero
     */
    public function index()
    {
        // Verificar que el usuario tenga rol de Cocinero (id_rol = 4)
        if (auth()->user()->id_rol !== 4) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Estadísticas de cocina
        $hoy = Carbon::today();
        
        $pedidosPendientes = Pedido::whereIn('estado', ['pendiente', 'confirmado'])->count();
        $enPreparacion = Pedido::where('estado', 'preparando')->count();
        $completadosHoy = Pedido::where('estado', 'completado')
            ->whereDate('updated_at', $hoy)
            ->count();

        // Calcular tiempo promedio de preparación
        $tiempoPromedio = Pedido::where('estado', 'completado')
            ->whereDate('updated_at', $hoy)
            ->whereNotNull('hora_inicio_preparacion')
            ->whereNotNull('hora_completado')
            ->get()
            ->map(function ($pedido) {
                return Carbon::parse($pedido->hora_inicio_preparacion)
                    ->diffInMinutes(Carbon::parse($pedido->hora_completado));
            })
            ->avg() ?? 15;

        $estadisticas = [
            'pedidosPendientes' => $pedidosPendientes,
            'enPreparacion' => $enPreparacion,
            'completadosHoy' => $completadosHoy,
            'tiempoPromedio' => round($tiempoPromedio)
        ];

        // Obtener pedidos para la cola de cocina
        $pedidos = Pedido::with(['mesa', 'detalles.producto'])
            ->whereIn('estado', ['pendiente', 'confirmado', 'preparando'])
            ->orderByRaw("FIELD(estado, 'preparando', 'confirmado', 'pendiente')")
            ->orderBy('created_at')
            ->get()
            ->map(function ($pedido) {
                $tiempoEspera = $pedido->created_at->diffInMinutes(now());
                $prioridad = $tiempoEspera > 15 ? 'urgente' : 'normal';

                return [
                    'id' => $pedido->id,
                    'mesa' => $pedido->mesa->numero_mesa ?? 0,
                    'hora' => $pedido->created_at->format('H:i'),
                    'tiempoEspera' => $tiempoEspera,
                    'estado' => $pedido->estado === 'confirmado' ? 'pendiente' : $pedido->estado,
                    'estadoColor' => $this->getColorEstadoPedido($pedido->estado),
                    'prioridad' => $prioridad,
                    'esLlevar' => !$pedido->mesa, // Si no tiene mesa, es para llevar
                    'tiempoEstimadoTotal' => $this->calcularTiempoEstimado($pedido->detalles),
                    'items' => $pedido->detalles->map(function ($detalle) {
                        return [
                            'id' => $detalle->id,
                            'cantidad' => $detalle->cantidad,
                            'nombre' => $detalle->producto->nombre,
                            'observaciones' => $detalle->observaciones,
                            'listo' => $detalle->estado === 'completado',
                            'tiempoEstimado' => $detalle->producto->tiempo_preparacion ?? 15
                        ];
                    })
                ];
            });

        // Inventario crítico
        $inventarioCritico = Insumo::whereRaw('stock_actual <= stock_minimo')
            ->with('unidadMedida')
            ->get()
            ->map(function ($insumo) {
                return [
                    'id' => $insumo->id,
                    'nombre' => $insumo->nombre,
                    'stock' => $insumo->stock_actual ?? 0,
                    'unidad' => $insumo->unidadMedida->abreviatura ?? 'und'
                ];
            });

        // Tiempos de preparación estándar
        $tiemposPreparacion = Producto::where('disponible', true)
            ->whereNotNull('tiempo_preparacion')
            ->orderBy('tiempo_preparacion', 'desc')
            ->take(8)
            ->get()
            ->map(function ($producto) {
                return [
                    'plato' => $producto->nombre,
                    'minutos' => $producto->tiempo_preparacion
                ];
            });

        // Rendimiento del día
        $platosPreparados = PedidoDetalle::whereHas('pedido', function ($query) use ($hoy) {
                $query->where('estado', 'completado')
                      ->whereDate('updated_at', $hoy);
            })
            ->sum('cantidad');

        $rendimiento = [
            'platosPreparados' => $platosPreparados,
            'tiempoPromedio' => round($tiempoPromedio, 1),
            'eficiencia' => min(95, max(60, 100 - ($tiempoPromedio - 15) * 2)) // Fórmula simple de eficiencia
        ];

        return Inertia::render('Cocinero/Dashboard', [
            'estadisticas' => $estadisticas,
            'pedidos' => $pedidos,
            'inventarioCritico' => $inventarioCritico,
            'tiemposPreparacion' => $tiemposPreparacion,
            'rendimiento' => $rendimiento
        ]);
    }

    /**
     * Iniciar preparación de un pedido
     */
    public function iniciarPreparacion(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedido,id'
        ]);

        $pedido = Pedido::findOrFail($request->pedido_id);
        
        $pedido->update([
            'estado' => 'preparando',
            'hora_inicio_preparacion' => now(),
            'id_cocinero' => auth()->id()
        ]);

        return back()->with('success', 'Preparación iniciada');
    }

    /**
     * Marcar un item del pedido como listo
     */
    public function marcarItemListo(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedido,id',
            'item_id' => 'required|exists:pedido_detalle,id'
        ]);

        $detalle = PedidoDetalle::where('id', $request->item_id)
            ->where('id_pedido', $request->pedido_id)
            ->firstOrFail();
        
        $detalle->update([
            'estado' => 'completado',
            'hora_completado' => now()
        ]);

        return back()->with('success', 'Item marcado como listo');
    }

    /**
     * Completar un pedido completamente
     */
    public function completarPedido(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedido,id'
        ]);

        $pedido = Pedido::with('detalles')->findOrFail($request->pedido_id);
        
        // Verificar que todos los items estén listos
        $todosListos = $pedido->detalles->every(function ($detalle) {
            return $detalle->estado === 'completado';
        });

        if (!$todosListos) {
            return back()->with('error', 'No se pueden completar pedidos con items pendientes');
        }

        $pedido->update([
            'estado' => 'listo',
            'hora_completado' => now()
        ]);

        return back()->with('success', 'Pedido completado y listo para entregar');
    }

    /**
     * Solicitar reposición de inventario
     */
    public function solicitarReposicion(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:insumo,id'
        ]);

        $insumo = Insumo::findOrFail($request->item_id);
        
        // Aquí implementarías la lógica para solicitar reposición
        // Por ejemplo, crear una notificación para el administrador
        
        return back()->with('success', "Reposición solicitada para {$insumo->nombre}");
    }

    /**
     * Ver detalle completo de un pedido
     */
    public function verPedido($id)
    {
        $pedido = Pedido::with([
            'mesa', 
            'detalles.producto', 
            'usuario'
        ])->findOrFail($id);

        return Inertia::render('Cocinero/DetallePedido', [
            'pedido' => $pedido
        ]);
    }

    /**
     * Obtener color según estado del pedido
     */
    private function getColorEstadoPedido($estado)
    {
        $colores = [
            'pendiente' => 'danger',
            'confirmado' => 'danger',
            'preparando' => 'warning',
            'listo' => 'success',
            'completado' => 'info'
        ];

        return $colores[$estado] ?? 'secondary';
    }

    /**
     * Calcular tiempo estimado total del pedido
     */
    private function calcularTiempoEstimado($detalles)
    {
        $tiempoMaximo = 0;
        
        foreach ($detalles as $detalle) {
            $tiempoItem = ($detalle->producto->tiempo_preparacion ?? 15);
            $tiempoMaximo = max($tiempoMaximo, $tiempoItem);
        }

        return $tiempoMaximo;
    }
}