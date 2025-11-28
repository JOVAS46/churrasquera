<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🛒 Insertando productos de prueba de 1 Bs...\n\n";

// Obtener una categoría de bebidas (o la primera disponible)
$categoriaBebidas = DB::table('categoria')->where('nombre', 'like', '%Bebida%')->first();
if (!$categoriaBebidas) {
    $categoriaBebidas = DB::table('categoria')->first();
}

if (!$categoriaBebidas) {
    echo "❌ Error: No hay categorías en la base de datos.\n";
    echo "Ejecuta: php artisan db:seed --class=CategoriaProductoSeeder\n";
    exit(1);
}

echo "✓ Usando categoría: {$categoriaBebidas->nombre}\n\n";

// Productos de 1 Bs para pruebas
$productos = [
    [
        'nombre' => 'Gaseosa Pequeña',
        'descripcion' => 'Gaseosa personal 350ml',
        'precio' => 1.00,
        'disponible' => true,
        'id_categoria' => $categoriaBebidas->id_categoria,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nombre' => 'Agua Mineral',
        'descripcion' => 'Botella de agua 500ml',
        'precio' => 1.00,
        'disponible' => true,
        'id_categoria' => $categoriaBebidas->id_categoria,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nombre' => 'Jugo Natural',
        'descripcion' => 'Vaso de jugo de naranja natural',
        'precio' => 1.00,
        'disponible' => true,
        'id_categoria' => $categoriaBebidas->id_categoria,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nombre' => 'Té Helado',
        'descripcion' => 'Vaso de té helado',
        'precio' => 1.00,
        'disponible' => true,
        'id_categoria' => $categoriaBebidas->id_categoria,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'nombre' => 'Café Express',
        'descripcion' => 'Café express pequeño',
        'precio' => 1.00,
        'disponible' => true,
        'id_categoria' => $categoriaBebidas->id_categoria,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

// Insertar productos
foreach ($productos as $producto) {
    DB::table('producto')->insert($producto);
    echo "✓ Insertado: {$producto['nombre']} - Bs. {$producto['precio']}\n";
}

echo "\n✅ ¡{count($productos)} productos de 1 Bs insertados correctamente!\n\n";

// Mostrar todos los productos de 1 Bs
echo "📋 Lista de productos de 1 Bs disponibles:\n";
echo str_repeat("=", 70) . "\n";

$productosBaratos = DB::table('producto')
    ->where('precio', 1.00)
    ->where('disponible', true)
    ->get();

foreach ($productosBaratos as $p) {
    echo sprintf(
        "ID: %-3d | %-30s | Bs. %.2f\n",
        $p->id_producto,
        $p->nombre,
        $p->precio
    );
}

echo str_repeat("=", 70) . "\n";
echo "\n🎯 Ahora puedes usar estos productos para probar el sistema de pagos!\n";
echo "\n💡 Sugerencias de prueba:\n";
echo "   1. Crea un pedido con estos productos baratos\n";
echo "   2. El total será muy bajo (ej: 3-5 Bs)\n";
echo "   3. Perfecto para probar pagos pequeños\n";
