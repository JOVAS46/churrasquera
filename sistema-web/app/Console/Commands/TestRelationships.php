<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Reserva;
use App\Models\Venta;
use App\Models\MetodoPago;

class TestRelationships extends Command
{
    protected $signature = 'test:relationships';
    protected $description = 'Verificar que todas las relaciones de modelos funcionen correctamente';

    public function handle()
    {
        $this->info('🧪 Probando relaciones de modelos...');

        try {
            // Probar User
            $this->info('📱 Probando modelo User...');
            $user = User::with(['rol', 'reservas', 'pedidosComoCliente', 'pedidosComoMesero'])->first();
            if ($user) {
                $this->line("✅ Usuario: {$user->nombre} {$user->apellido}");
                $this->line("   Rol: " . ($user->rol->nombre_rol ?? 'Sin rol'));
                $this->line("   Reservas: " . $user->reservas->count());
                $this->line("   Pedidos como cliente: " . $user->pedidosComoCliente->count());
                $this->line("   Pedidos como mesero: " . $user->pedidosComoMesero->count());
            }

            // Probar Mesa
            $this->info('🪑 Probando modelo Mesa...');
            $mesa = Mesa::with(['mesero', 'reservas', 'pedidos'])->first();
            if ($mesa) {
                $this->line("✅ Mesa #{$mesa->numero_mesa}");
                $this->line("   Capacidad: {$mesa->capacidad}");
                $this->line("   Estado: {$mesa->estado}");
                $this->line("   Mesero: " . ($mesa->mesero->nombre ?? 'Sin asignar'));
                $this->line("   Reservas: " . $mesa->reservas->count());
                $this->line("   Pedidos: " . $mesa->pedidos->count());
            }

            // Probar MetodoPago
            $this->info('💳 Probando modelo MetodoPago...');
            $metodosPago = MetodoPago::activos()->get();
            $this->line("✅ Métodos de pago activos: " . $metodosPago->count());
            foreach ($metodosPago as $metodo) {
                $this->line("   - {$metodo->nombre}");
            }

            // Probar Reserva
            $this->info('📅 Probando modelo Reserva...');
            $reserva = Reserva::with(['cliente', 'mesa'])->first();
            if ($reserva) {
                $this->line("✅ Reserva para {$reserva->numero_personas} personas");
                $this->line("   Cliente: " . ($reserva->cliente->nombre ?? 'Cliente eliminado'));
                $this->line("   Mesa: #" . ($reserva->mesa->numero_mesa ?? 'Mesa eliminada'));
                $this->line("   Estado: {$reserva->estado}");
            }

            // Probar Pedido
            $this->info('🍽️ Probando modelo Pedido...');
            $pedido = Pedido::with(['cliente', 'mesero', 'cocinero', 'mesa', 'detalles'])->first();
            if ($pedido) {
                $this->line("✅ Pedido #{$pedido->id_pedido}");
                $this->line("   Cliente: " . ($pedido->cliente->nombre ?? 'Sin cliente'));
                $this->line("   Mesero: " . ($pedido->mesero->nombre ?? 'Sin mesero'));
                $this->line("   Cocinero: " . ($pedido->cocinero->nombre ?? 'Sin cocinero'));
                $this->line("   Mesa: #" . ($pedido->mesa->numero_mesa ?? 'Sin mesa'));
                $this->line("   Estado: {$pedido->estado}");
                $this->line("   Detalles: " . $pedido->detalles->count());
            }

            // Probar Venta
            $this->info('💰 Probando modelo Venta...');
            $venta = Venta::with(['usuario', 'mesa', 'metodoPago', 'detalles'])->first();
            if ($venta) {
                $this->line("✅ Venta #{$venta->id}");
                $this->line("   Usuario: " . ($venta->usuario->nombre ?? 'Sin usuario'));
                $this->line("   Mesa: #" . ($venta->mesa->numero_mesa ?? 'Sin mesa'));
                $this->line("   Método de pago: " . ($venta->metodoPago->nombre ?? 'Sin método'));
                $this->line("   Total: S/ {$venta->total}");
                $this->line("   Detalles: " . $venta->detalles->count());
            }

            $this->info('✅ Todas las relaciones funcionan correctamente!');

        } catch (\Exception $e) {
            $this->error('❌ Error al probar relaciones: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}