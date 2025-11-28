<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Reserva;
use App\Models\Producto;
use App\Models\MetodoPago;
use Carbon\Carbon;

class CreateTestData extends Command
{
    protected $signature = 'test:create-data';
    protected $description = 'Crear datos de prueba para el sistema de roles';

    public function handle()
    {
        $this->info('🎯 Creando datos de prueba para el sistema...');

        try {
            // Obtener usuarios de cada rol
            $mesero = User::where('email', 'mesero1@churrasqueria.com')->first();
            $cocinero = User::where('email', 'cocinero1@churrasqueria.com')->first();
            $cliente = User::where('email', 'cliente@gmail.com')->first();

            // Asignar mesero a algunas mesas
            $mesas = Mesa::take(3)->get();
            foreach ($mesas as $index => $mesa) {
                if ($index < 2) {
                    $mesa->update(['id_mesero' => $mesero->id_usuario]);
                    $this->line("✅ Mesa #{$mesa->numero_mesa} asignada al mesero {$mesero->nombre}");
                }
            }

            // Crear una reserva
            $reserva = Reserva::create([
                'fecha_reserva' => Carbon::tomorrow(),
                'hora_inicio' => '19:00:00',
                'hora_fin' => '21:00:00',
                'numero_personas' => 4,
                'estado' => 'confirmada',
                'observaciones' => 'Mesa preferiblemente cerca de la ventana',
                'id_cliente' => $cliente->id_usuario,
                'id_mesa' => $mesas->first()->id_mesa,
            ]);
            $this->line("✅ Reserva creada para {$cliente->nombre} - Mesa #{$mesas->first()->numero_mesa}");

            // Crear un pedido en proceso
            $productos = Producto::take(3)->get();
            if ($productos->count() > 0) {
                $pedido = Pedido::create([
                    'fecha_pedido' => now(),
                    'estado' => 'en_preparacion',
                    'total' => 0,
                    'observaciones' => 'Sin cebolla en la hamburguesa',
                    'id_cliente' => $cliente->id_usuario,
                    'id_mesero' => $mesero->id_usuario,
                    'id_cocinero' => $cocinero->id_usuario,
                    'id_mesa' => $mesas->skip(1)->first()->id_mesa,
                ]);

                $total = 0;
                foreach ($productos as $producto) {
                    $cantidad = rand(1, 3);
                    $precioUnitario = $producto->precio ?? 25.00;
                    $subtotal = $cantidad * $precioUnitario;

                    PedidoDetalle::create([
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal,
                        'observaciones' => $producto->nombre === 'Hamburguesa Clásica' ? 'Sin cebolla' : null,
                        'id_pedido' => $pedido->id_pedido,
                        'id_producto' => $producto->id_producto,
                    ]);

                    $total += $subtotal;
                    $this->line("  - {$cantidad}x {$producto->nombre} - S/ {$subtotal}");
                }

                $pedido->update(['total' => $total]);
                $this->line("✅ Pedido creado por {$cliente->nombre} - Total: S/ {$total}");
            }

            // Actualizar estados de algunas mesas
            $mesas->first()->update(['estado' => 'reservada']);
            $mesas->skip(1)->first()->update(['estado' => 'ocupada']);

            $this->info('🎉 Datos de prueba creados exitosamente!');
            $this->newLine();
            $this->info('📊 Resumen de datos creados:');
            $this->line('   - ' . Mesa::where('id_mesero', '!=', null)->count() . ' mesas asignadas');
            $this->line('   - ' . Reserva::count() . ' reservas');
            $this->line('   - ' . Pedido::count() . ' pedidos');
            $this->line('   - ' . PedidoDetalle::count() . ' detalles de pedido');

        } catch (\Exception $e) {
            $this->error('❌ Error al crear datos de prueba: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}