<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Reserva;
use App\Models\Venta;
use App\Models\Producto;

class StatusSystem extends Command
{
    protected $signature = 'system:status';
    protected $description = 'Mostrar el estado actual del sistema de restaurante';

    public function handle()
    {
        $this->info('🏪 Estado del Sistema - Churrascaria Roberto');
        $this->newLine();

        // Estado de usuarios por rol
        $this->info('👥 USUARIOS POR ROL:');
        $usuarios = User::with('rol')->get()->groupBy('rol.nombre_rol');
        foreach ($usuarios as $rol => $users) {
            $activos = $users->where('estado', true)->count();
            $total = $users->count();
            $this->line("   {$rol}: {$activos}/{$total} activos");
        }
        $this->newLine();

        // Estado de mesas
        $this->info('🪑 ESTADO DE MESAS:');
        $mesas = Mesa::all()->groupBy('estado');
        foreach (['disponible', 'ocupada', 'reservada', 'mantenimiento'] as $estado) {
            $cantidad = isset($mesas[$estado]) ? $mesas[$estado]->count() : 0;
            $emoji = $this->getEmojiEstadoMesa($estado);
            $this->line("   {$emoji} " . ucfirst($estado) . ": {$cantidad}");
        }
        $totalMesas = Mesa::count();
        $this->line("   📊 Total: {$totalMesas} mesas");
        $this->newLine();

        // Estado de pedidos
        $this->info('🍽️ PEDIDOS:');
        $pedidos = Pedido::all()->groupBy('estado');
        foreach (['pendiente', 'en_preparacion', 'listo', 'entregado', 'cancelado'] as $estado) {
            $cantidad = isset($pedidos[$estado]) ? $pedidos[$estado]->count() : 0;
            $emoji = $this->getEmojiEstadoPedido($estado);
            $this->line("   {$emoji} " . ucfirst(str_replace('_', ' ', $estado)) . ": {$cantidad}");
        }
        $totalPedidos = Pedido::count();
        $this->line("   📊 Total: {$totalPedidos} pedidos");
        $this->newLine();

        // Reservas
        $this->info('📅 RESERVAS:');
        $reservas = Reserva::all()->groupBy('estado');
        foreach (['pendiente', 'confirmada', 'cancelada', 'completada'] as $estado) {
            $cantidad = isset($reservas[$estado]) ? $reservas[$estado]->count() : 0;
            $this->line("   📝 " . ucfirst($estado) . ": {$cantidad}");
        }
        $totalReservas = Reserva::count();
        $this->line("   📊 Total: {$totalReservas} reservas");
        $this->newLine();

        // Ventas del día
        $this->info('💰 VENTAS DE HOY:');
        $ventasHoy = Venta::whereDate('fecha_venta', today())->get();
        $totalVentasHoy = $ventasHoy->sum('total');
        $cantidadVentasHoy = $ventasHoy->count();
        $promedioVenta = $cantidadVentasHoy > 0 ? $totalVentasHoy / $cantidadVentasHoy : 0;
        
        $this->line("   💵 Total ventas: S/ " . number_format($totalVentasHoy, 2));
        $this->line("   🧾 Cantidad: {$cantidadVentasHoy}");
        $this->line("   📊 Promedio: S/ " . number_format($promedioVenta, 2));
        $this->newLine();

        // Productos activos
        $this->info('🍖 PRODUCTOS:');
        $productosActivos = Producto::where('disponible', true)->count();
        $totalProductos = Producto::count();
        $this->line("   ✅ Disponibles: {$productosActivos}");
        $this->line("   📊 Total: {$totalProductos}");
        $this->newLine();

        // Resumen operacional
        $this->info('📈 RESUMEN OPERACIONAL:');
        $mesasOcupadas = Mesa::where('estado', 'ocupada')->count();
        $pedidosPendientes = Pedido::whereIn('estado', ['pendiente', 'en_preparacion'])->count();
        $reservasHoy = Reserva::whereDate('fecha_reserva', today())->count();
        
        if ($mesasOcupadas > 0) {
            $this->line("   🔴 {$mesasOcupadas} mesa(s) ocupada(s)");
        }
        if ($pedidosPendientes > 0) {
            $this->line("   ⏳ {$pedidosPendientes} pedido(s) en proceso");
        }
        if ($reservasHoy > 0) {
            $this->line("   📅 {$reservasHoy} reserva(s) para hoy");
        }
        
        if ($mesasOcupadas === 0 && $pedidosPendientes === 0) {
            $this->line("   ✅ Sistema sin actividad pendiente");
        }

        $this->newLine();
        $this->info('🎯 Sistema funcionando correctamente!');

        return 0;
    }

    private function getEmojiEstadoMesa($estado)
    {
        return match($estado) {
            'disponible' => '✅',
            'ocupada' => '🔴',
            'reservada' => '🟡',
            'mantenimiento' => '🔧',
            default => '⚪'
        };
    }

    private function getEmojiEstadoPedido($estado)
    {
        return match($estado) {
            'pendiente' => '⏳',
            'en_preparacion' => '🔥',
            'listo' => '✅',
            'entregado' => '📦',
            'cancelado' => '❌',
            default => '⚪'
        };
    }
}