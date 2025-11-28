<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_insumo', function (Blueprint $table) {
            $table->id('id_producto_insumo');
            $table->decimal('cantidad_necesaria', 10, 2);
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_insumo');

            $table->foreign('id_producto')
                  ->references('id_producto')
                  ->on('producto')
                  ->onDelete('cascade');

            $table->foreign('id_insumo')
                  ->references('id_insumo')
                  ->on('insumo')
                  ->onDelete('cascade');

            $table->unique(['id_producto', 'id_insumo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_insumo');
    }
};
