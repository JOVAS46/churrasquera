<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MesaSeeder extends Seeder
{
    public function run(): void
    {
        $mesas = [
            // Mesas para 2 personas
            ['numero_mesa' => 1, 'capacidad' => 2, 'ubicacion' => 'Terraza', 'estado' => 'disponible'],
            ['numero_mesa' => 2, 'capacidad' => 2, 'ubicacion' => 'Terraza', 'estado' => 'disponible'],
            ['numero_mesa' => 3, 'capacidad' => 2, 'ubicacion' => 'Interior', 'estado' => 'disponible'],
            ['numero_mesa' => 4, 'capacidad' => 2, 'ubicacion' => 'Interior', 'estado' => 'disponible'],

            // Mesas para 4 personas
            ['numero_mesa' => 5, 'capacidad' => 4, 'ubicacion' => 'Interior', 'estado' => 'disponible'],
            ['numero_mesa' => 6, 'capacidad' => 4, 'ubicacion' => 'Interior', 'estado' => 'disponible'],
            ['numero_mesa' => 7, 'capacidad' => 4, 'ubicacion' => 'Terraza', 'estado' => 'disponible'],
            ['numero_mesa' => 8, 'capacidad' => 4, 'ubicacion' => 'Terraza', 'estado' => 'disponible'],

            // Mesas para 6 personas
            ['numero_mesa' => 9, 'capacidad' => 6, 'ubicacion' => 'Interior', 'estado' => 'disponible'],
            ['numero_mesa' => 10, 'capacidad' => 6, 'ubicacion' => 'Terraza', 'estado' => 'disponible'],

            // Mesa para 8 personas
            ['numero_mesa' => 11, 'capacidad' => 8, 'ubicacion' => 'Salón VIP', 'estado' => 'disponible'],
            ['numero_mesa' => 12, 'capacidad' => 8, 'ubicacion' => 'Salón VIP', 'estado' => 'disponible'],
        ];

        foreach ($mesas as $mesa) {
            DB::table('mesa')->insert(array_merge($mesa, [
                'created_at' => now(),
            ]));
        }
    }
}
