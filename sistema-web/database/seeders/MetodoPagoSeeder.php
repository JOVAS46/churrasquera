<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $metodos = [
            [
                'nombre' => 'Efectivo',
                'descripcion' => 'Pago en efectivo',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Tarjeta de Débito',
                'descripcion' => 'Pago con tarjeta de débito',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Tarjeta de Crédito',
                'descripcion' => 'Pago con tarjeta de crédito',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'descripcion' => 'Pago por transferencia bancaria',
                'activo' => true,
                'created_at' => now(),
            ],
            [
                'nombre' => 'QR/Billetera Digital',
                'descripcion' => 'Pago mediante código QR o billetera digital',
                'activo' => true,
                'created_at' => now(),
            ],
        ];

        foreach ($metodos as $metodo) {
            DB::table('metodo_pago')->insert($metodo);
        }
    }
}
