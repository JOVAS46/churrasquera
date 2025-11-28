<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            // Carnes a la Parrilla (id_categoria = 1)
            [
                'nombre' => 'Asado de Tira',
                'descripcion' => 'Corte de costillas de res asado a la parrilla',
                'precio' => 85.00,
                'tiempo_preparacion' => 25,
                'disponible' => true,
                'id_categoria' => 1,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Picaña',
                'descripcion' => 'Corte premium de carne de res',
                'precio' => 95.00,
                'tiempo_preparacion' => 20,
                'disponible' => true,
                'id_categoria' => 1,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Chorizo Parrillero',
                'descripcion' => 'Chorizo artesanal a la parrilla',
                'precio' => 45.00,
                'tiempo_preparacion' => 15,
                'disponible' => true,
                'id_categoria' => 1,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Costillas BBQ',
                'descripcion' => 'Costillas de cerdo con salsa barbacoa',
                'precio' => 75.00,
                'tiempo_preparacion' => 30,
                'disponible' => true,
                'id_categoria' => 1,
                'created_at' => now(),
            ],

            // Pollo (id_categoria = 2)
            [
                'nombre' => 'Pollo a la Brasa',
                'descripcion' => 'Pollo entero marinado y asado',
                'precio' => 65.00,
                'tiempo_preparacion' => 35,
                'disponible' => true,
                'id_categoria' => 2,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Alitas Picantes',
                'descripcion' => 'Alitas de pollo con salsa picante',
                'precio' => 40.00,
                'tiempo_preparacion' => 20,
                'disponible' => true,
                'id_categoria' => 2,
                'created_at' => now(),
            ],

            // Pescados y Mariscos (id_categoria = 3)
            [
                'nombre' => 'Trucha a la Parrilla',
                'descripcion' => 'Trucha fresca asada con hierbas',
                'precio' => 70.00,
                'tiempo_preparacion' => 25,
                'disponible' => true,
                'id_categoria' => 3,
                'created_at' => now(),
            ],

            // Acompañamientos (id_categoria = 4)
            [
                'nombre' => 'Papas Fritas',
                'descripcion' => 'Papas fritas crocantes',
                'precio' => 20.00,
                'tiempo_preparacion' => 10,
                'disponible' => true,
                'id_categoria' => 4,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Arroz',
                'descripcion' => 'Arroz blanco cocido',
                'precio' => 15.00,
                'tiempo_preparacion' => 5,
                'disponible' => true,
                'id_categoria' => 4,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Yuca Frita',
                'descripcion' => 'Yuca frita dorada',
                'precio' => 18.00,
                'tiempo_preparacion' => 10,
                'disponible' => true,
                'id_categoria' => 4,
                'created_at' => now(),
            ],

            // Ensaladas (id_categoria = 5)
            [
                'nombre' => 'Ensalada Mixta',
                'descripcion' => 'Lechuga, tomate, cebolla y zanahoria',
                'precio' => 25.00,
                'tiempo_preparacion' => 5,
                'disponible' => true,
                'id_categoria' => 5,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Ensalada César',
                'descripcion' => 'Lechuga romana con aderezo césar',
                'precio' => 35.00,
                'tiempo_preparacion' => 8,
                'disponible' => true,
                'id_categoria' => 5,
                'created_at' => now(),
            ],

            // Bebidas Sin Alcohol (id_categoria = 6)
            [
                'nombre' => 'Coca Cola',
                'descripcion' => 'Refresco de cola 500ml',
                'precio' => 10.00,
                'tiempo_preparacion' => 1,
                'disponible' => true,
                'id_categoria' => 6,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Jugo Natural',
                'descripcion' => 'Jugo de frutas naturales',
                'precio' => 15.00,
                'tiempo_preparacion' => 3,
                'disponible' => true,
                'id_categoria' => 6,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Agua Mineral',
                'descripcion' => 'Agua mineral 500ml',
                'precio' => 8.00,
                'tiempo_preparacion' => 1,
                'disponible' => true,
                'id_categoria' => 6,
                'created_at' => now(),
            ],

            // Bebidas Alcohólicas (id_categoria = 7)
            [
                'nombre' => 'Cerveza Paceña',
                'descripcion' => 'Cerveza nacional 620ml',
                'precio' => 15.00,
                'tiempo_preparacion' => 1,
                'disponible' => true,
                'id_categoria' => 7,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Vino Tinto',
                'descripcion' => 'Copa de vino tinto reserva',
                'precio' => 25.00,
                'tiempo_preparacion' => 2,
                'disponible' => true,
                'id_categoria' => 7,
                'created_at' => now(),
            ],
        ];

        foreach ($productos as $producto) {
            DB::table('producto')->insert($producto);
        }
    }
}
