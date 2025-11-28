<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Orden correcto de seeders según dependencias de foreign keys:

        // 1. Primero las tablas sin dependencias
        $this->call([
            RolSeeder::class,                    // Primero roles
            UsuarioSeeder::class,                // Usuarios (depende de rol)
            MenuNavegacionSeeder::class,         // Menú dinámico (depende de rol)

            CategoriaProductoSeeder::class,      // Categorías de productos
            ProductoSeeder::class,               // Productos (depende de categoría)

            MesaSeeder::class,                   // Mesas
            MetodoPagoSeeder::class,             // Métodos de pago
        ]);

        $this->command->info('');
        $this->command->info('✅ Base de datos poblada exitosamente!');
        $this->command->info('');
        $this->command->info('👥 Usuarios creados:');
        $this->command->info('   - Admin: admin@churrasqueria.com / admin123 (Gerente)');
        $this->command->info('   - Gerente: gerente@churrasqueria.com / gerente123 (Gerente)');
        $this->command->info('   - Cajero: cajero1@churrasqueria.com / cajero123 (Cajero)');
        $this->command->info('   - Mesero: mesero1@churrasqueria.com / mesero123 (Mesero)');
        $this->command->info('   - Cocinero: cocinero1@churrasqueria.com / cocinero123 (Cocinero)');
        $this->command->info('   - Cliente: cliente@gmail.com / cliente123 (Cliente)');
        $this->command->info('');
        $this->command->info('📊 Datos insertados:');
        $this->command->info('   - 5 Roles');
        $this->command->info('   - 10 Usuarios');
        $this->command->info('   - 26 Menús de navegación');
        $this->command->info('   - 8 Categorías de productos');
        $this->command->info('   - 17 Productos');
        $this->command->info('   - 12 Mesas');
        $this->command->info('   - 5 Métodos de pago');
        $this->command->info('');
    }
}
