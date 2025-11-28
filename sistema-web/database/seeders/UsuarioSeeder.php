<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            // Gerente
            [
                'nombre' => 'Admin',
                'apellido' => 'Sistema',
                'email' => 'admin@churrasqueria.com',
                'telefono' => '71234567',
                'password' => Hash::make('admin123'),
                'estado' => true,
                'id_rol' => 1, // Gerente
                'fecha_registro' => now(),
            ],
            [
                'nombre' => 'Roberto',
                'apellido' => 'Pérez',
                'email' => 'gerente@churrasqueria.com',
                'telefono' => '72345678',
                'password' => Hash::make('gerente123'),
                'estado' => true,
                'id_rol' => 1, // Gerente
                'fecha_registro' => now(),
            ],

            // Cajeros
            [
                'nombre' => 'María',
                'apellido' => 'González',
                'email' => 'cajero1@churrasqueria.com',
                'telefono' => '73456789',
                'password' => Hash::make('cajero123'),
                'estado' => true,
                'id_rol' => 2, // Cajero
                'fecha_registro' => now(),
            ],
            [
                'nombre' => 'Ana',
                'apellido' => 'Ramírez',
                'email' => 'cajero2@churrasqueria.com',
                'telefono' => '74567890',
                'password' => Hash::make('cajero123'),
                'estado' => true,
                'id_rol' => 2, // Cajero
                'fecha_registro' => now(),
            ],

            // Meseros
            [
                'nombre' => 'Juan',
                'apellido' => 'Mamani',
                'email' => 'mesero1@churrasqueria.com',
                'telefono' => '75678901',
                'password' => Hash::make('mesero123'),
                'estado' => true,
                'id_rol' => 3, // Mesero
                'fecha_registro' => now(),
            ],
            [
                'nombre' => 'Pedro',
                'apellido' => 'Quispe',
                'email' => 'mesero2@churrasqueria.com',
                'telefono' => '76789012',
                'password' => Hash::make('mesero123'),
                'estado' => true,
                'id_rol' => 3, // Mesero
                'fecha_registro' => now(),
            ],
            [
                'nombre' => 'Lucía',
                'apellido' => 'Flores',
                'email' => 'mesero3@churrasqueria.com',
                'telefono' => '77890123',
                'password' => Hash::make('mesero123'),
                'estado' => true,
                'id_rol' => 3, // Mesero
                'fecha_registro' => now(),
            ],

            // Cocineros
            [
                'nombre' => 'Carlos',
                'apellido' => 'Rojas',
                'email' => 'cocinero1@churrasqueria.com',
                'telefono' => '78901234',
                'password' => Hash::make('cocinero123'),
                'estado' => true,
                'id_rol' => 4, // Cocinero
                'fecha_registro' => now(),
            ],
            [
                'nombre' => 'José',
                'apellido' => 'Vargas',
                'email' => 'cocinero2@churrasqueria.com',
                'telefono' => '79012345',
                'password' => Hash::make('cocinero123'),
                'estado' => true,
                'id_rol' => 4, // Cocinero
                'fecha_registro' => now(),
            ],

            // Cliente de prueba
            [
                'nombre' => 'Cliente',
                'apellido' => 'Ejemplo',
                'email' => 'cliente@gmail.com',
                'telefono' => '70123456',
                'password' => Hash::make('cliente123'),
                'estado' => true,
                'id_rol' => 5, // Cliente
                'fecha_registro' => now(),
            ],
        ];

        foreach ($usuarios as $usuario) {
            DB::table('usuario')->insert($usuario);
        }
    }
}
