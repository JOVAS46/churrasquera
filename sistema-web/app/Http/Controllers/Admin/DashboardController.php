<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Venta;
use App\Models\User;
use App\Models\ContadorVisitas;
use App\Models\Mesa;
use App\Models\Insumo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard del administrador
     */
    public function index()
    {
        // Verificar que el usuario tenga rol de Gerente (id_rol = 1)
        if (auth()->user()->id_rol !== 1) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Obtener métricas del día
        $hoy = Carbon::today();
        $ventasHoy = Venta::whereDate('created_at', $hoy)->sum('total');
        $clientesAtendidos = Venta::whereDate('created_at', $hoy)->distinct('id_usuario')->count();
        
        // Mesas y su ocupación
        $mesasOcupadas = Mesa::where('estado', 'ocupada')->count();
        $totalMesas = Mesa::count();
        
        // Alertas de inventario
        $alertasInventario = Insumo::whereRaw('stock_actual <= stock_minimo')->count();

        $metricas = [
            'ventasHoy' => number_format($ventasHoy, 2),
            'clientesAtendidos' => $clientesAtendidos,
            'mesasOcupadas' => $mesasOcupadas,
            'totalMesas' => $totalMesas,
            'alertas' => $alertasInventario
        ];

        // Personal activo (usuarios con estado true)
        $personalActivo = User::where('estado', true)
            ->where('id_rol', '!=', 5) // Excluir clientes
            ->with('rol')
            ->get()
            ->map(function ($usuario) {
                return [
                    'id' => $usuario->id_usuario,
                    'nombre' => $usuario->nombre . ' ' . $usuario->apellido,
                    'rol' => $usuario->rol->nombre_rol ?? 'N/A',
                    'estado' => 'activo',
                    'icono' => $this->getIconoRol($usuario->id_rol)
                ];
            });

        // Pedidos recientes (últimos 10)
        $pedidosRecientes = Venta::with('mesa')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($venta) {
                return [
                    'id' => $venta->id,
                    'mesa' => $venta->mesa->numero_mesa ?? 'Para llevar',
                    'hora' => $venta->created_at->format('H:i'),
                    'total' => number_format($venta->total, 2),
                    'estado' => 'Entregado',
                    'estadoColor' => 'info'
                ];
            });

        // Stock crítico
        $stockCritico = Insumo::whereRaw('stock_actual <= stock_minimo')
            ->get()
            ->map(function ($insumo) {
                return [
                    'id' => $insumo->id,
                    'nombre' => $insumo->nombre,
                    'stock' => $insumo->stock_actual ?? 0,
                    'unidad' => $insumo->unidadMedida->abreviatura ?? 'und'
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'metricas' => $metricas,
            'personalActivo' => $personalActivo,
            'pedidosRecientes' => $pedidosRecientes,
            'stockCritico' => $stockCritico
        ]);
    }

    /**
     * Generar reportes
     */
    public function generarReporte(Request $request)
    {
        // Redirigir a la sección de reportes
        return redirect()->route('admin.reportes');
    }

    /**
     * Reabastecer inventario
     */
    public function reabastecer(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:insumo,id'
        ]);

        $insumo = Insumo::findOrFail($request->item_id);
        
        // Aquí implementarías la lógica de reabastecimiento
        // Por ahora solo simulamos el proceso
        
        return back()->with('success', "Solicitud de reabastecimiento enviada para {$insumo->nombre}");
    }

    /**
     * Obtener icono según el rol
     */
    private function getIconoRol($idRol)
    {
        $iconos = [
            1 => 'fas fa-user-shield',  // Gerente
            2 => 'fas fa-cash-register', // Cajero
            3 => 'fas fa-user-tie',      // Mesero
            4 => 'fas fa-utensils',      // Cocinero
            5 => 'fas fa-user'           // Cliente
        ];

        return $iconos[$idRol] ?? 'fas fa-user';
    }
}