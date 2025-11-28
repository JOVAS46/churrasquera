<?php

namespace App\Http\Controllers\Cajero;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\VentaDetalle;
use App\Models\MetodoPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard del cajero
     */
    public function index()
    {
        // Verificar que el usuario tenga rol de Cajero (id_rol = 2)
        if (auth()->user()->id_rol !== 2) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Estado de la caja del cajero
        $turnoInicio = Carbon::today()->setTime(8, 0); // Asumiendo que inicia a las 8 AM
        $ventasTurno = Venta::where('id_usuario', auth()->id())
            ->where('created_at', '>=', $turnoInicio)
            ->sum('total');

        $transaccionesTurno = Venta::where('id_usuario', auth()->id())
            ->where('created_at', '>=', $turnoInicio)
            ->count();

        $cajaEstado = [
            'efectivo' => number_format(1250.50, 2), // Esto vendría de una tabla de caja
            'ventasTurno' => number_format($ventasTurno, 2),
            'transacciones' => $transaccionesTurno,
            'ultimaApertura' => $turnoInicio->format('H:i')
        ];

        // Productos disponibles para venta
        $productos = Producto::where('disponible', true)
            ->with('categoria')
            ->get()
            ->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio,
                    'categoria' => $producto->categoria->nombre ?? 'Sin categoría'
                ];
            });

        // Ventas recientes del cajero
        $ventasRecientes = Venta::where('id_usuario', auth()->id())
            ->with(['metodoPago', 'mesa'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($venta) {
                return [
                    'id' => $venta->id,
                    'numero' => 'V' . str_pad($venta->id, 3, '0', STR_PAD_LEFT),
                    'hora' => $venta->created_at->format('H:i'),
                    'total' => number_format($venta->total, 2),
                    'metodo' => $venta->metodoPago->nombre ?? 'Efectivo',
                    'metodoColor' => $this->getColorMetodoPago($venta->metodoPago->nombre ?? 'Efectivo'),
                    'estado' => 'Completada',
                    'estadoColor' => 'success'
                ];
            });

        return Inertia::render('Cajero/Dashboard', [
            'cajaEstado' => $cajaEstado,
            'productos' => $productos,
            'ventasRecientes' => $ventasRecientes
        ]);
    }

    /**
     * Procesar una nueva venta
     */
    public function procesarVenta(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:producto,id',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta',
            'monto_recibido' => 'required_if:metodo_pago,efectivo|nullable|numeric|min:0',
            'vuelto' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Obtener o crear método de pago
            $metodoPago = MetodoPago::firstOrCreate([
                'nombre' => $request->metodo_pago === 'efectivo' ? 'Efectivo' : 'Tarjeta de Débito'
            ]);

            // Crear la venta
            $venta = Venta::create([
                'cantidad' => array_sum(array_column($request->items, 'cantidad')),
                'precio' => $request->total / array_sum(array_column($request->items, 'cantidad')),
                'total' => $request->total,
                'id_usuario' => auth()->id(),
                'id_metodo_pago' => $metodoPago->id,
                'fecha_venta' => now(),
                'monto_recibido' => $request->monto_recibido,
                'vuelto' => $request->vuelto ?? 0
            ]);

            // Crear detalles de la venta
            foreach ($request->items as $item) {
                VentaDetalle::create([
                    'id_venta' => $venta->id,
                    'id_producto' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio']
                ]);
            }

            DB::commit();

            return back()->with('success', 'Venta procesada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Abrir caja
     */
    public function abrirCaja(Request $request)
    {
        // Lógica para abrir la caja (registrar apertura, monto inicial, etc.)
        
        return back()->with('success', 'Caja abierta exitosamente');
    }

    /**
     * Ver detalle de una venta
     */
    public function verVenta($id)
    {
        $venta = Venta::with(['detalles.producto', 'metodoPago', 'mesa', 'usuario'])
            ->findOrFail($id);

        // Verificar que la venta pertenezca al cajero actual
        if ($venta->id_usuario !== auth()->id()) {
            abort(403, 'No tienes acceso a esta venta.');
        }

        return Inertia::render('Cajero/DetalleVenta', [
            'venta' => $venta
        ]);
    }

    /**
     * Imprimir recibo
     */
    public function imprimirRecibo($id)
    {
        $venta = Venta::with(['detalles.producto', 'metodoPago', 'mesa'])
            ->findOrFail($id);

        // Verificar que la venta pertenezca al cajero actual
        if ($venta->id_usuario !== auth()->id()) {
            abort(403, 'No tienes acceso a esta venta.');
        }

        // Aquí implementarías la lógica para generar el PDF del recibo
        // Por ahora retornamos una vista simple
        
        return response()->view('cajero.recibo', compact('venta'))
            ->header('Content-Type', 'application/pdf');
    }

    /**
     * Obtener color según método de pago
     */
    private function getColorMetodoPago($metodo)
    {
        $colores = [
            'Efectivo' => 'success',
            'Tarjeta de Débito' => 'primary',
            'Tarjeta de Crédito' => 'info',
            'Transferencia Bancaria' => 'warning',
            'QR/Billetera Digital' => 'secondary'
        ];

        return $colores[$metodo] ?? 'secondary';
    }
}