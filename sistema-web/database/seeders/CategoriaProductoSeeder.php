<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            // Categorías de Platos
            [
                'nombre' => 'Carnes a la Parrilla',
                'descripcion' => 'Carnes asadas a la parrilla',
                'tipo' => 'plato',
                'created_at' => now(),
            ],
            [
                'nombre' => 'Pollo',
                'descripcion' => 'Platos de pollo',
                'tipo' => 'plato',
                'created_at' => now(),
            ],
            [
                'nombre' => 'Pescados y Mariscos',
                'descripcion' => 'Pescados y mariscos frescos',
                'tipo' => 'plato',
                'created_at' => now(),
            ],
            [
                'nombre' => 'Acompañamientos',
                'descripcion' => 'Guarniciones y acompañamientos',
                'tipo' => 'plato',
                'created_at' => now(),
            ],
            [
                'nombre' => 'Ensaladas',
                'descripcion' => 'Ensaladas frescas',
                'tipo' => 'plato',
                'created_at' => now(),
            ],

            // Categorías de Bebidas
            [
                'nombre' => 'Bebidas Sin Alcohol',
                'descripcion' => 'Refrescos, jugos y aguas',
                'tipo' => 'bebida',
                'created_at' => now(),
            ],
            [
                'nombre' => 'Bebidas Alcohólicas',
                'descripcion' => 'Cervezas, vinos y licores',
                'tipo' => 'bebida',
                'created_at' => now(),
            ],
            [
                'nombre' => 'Bebidas Calientes',
                'descripcion' => 'Café, té e infusiones',
                'tipo' => 'bebida',
                'created_at' => now(),
            ],
        ];

        foreach ($categorias as $categoria) {
            DB::table('categoria')->insert($categoria);
        }
    }
}
