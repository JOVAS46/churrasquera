<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id_rol' => 1,
                'nombre_rol' => 'Gerente',
                'descripcion' => 'Administrador del restaurante con acceso completo al sistema',
                'created_at' => now(),
            ],
            [
                'id_rol' => 2,
                'nombre_rol' => 'Cajero',
                'descripcion' => 'Encargado de gestión de pagos, facturas y caja',
                'created_at' => now(),
            ],
            [
                'id_rol' => 3,
                'nombre_rol' => 'Mesero',
                'descripcion' => 'Encargado de atención al cliente, mesas, reservas y pedidos',
                'created_at' => now(),
            ],
            [
                'id_rol' => 4,
                'nombre_rol' => 'Cocinero',
                'descripcion' => 'Encargado de preparación de alimentos y gestión de inventario de cocina',
                'created_at' => now(),
            ],
            [
                'id_rol' => 5,
                'nombre_rol' => 'Cliente',
                'descripcion' => 'Cliente del restaurante con acceso limitado',
                'created_at' => now(),
            ],
        ];

        foreach ($roles as $rol) {
            DB::table('rol')->insert($rol);
        }
    }
}
