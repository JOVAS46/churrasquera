<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('busquedas', function (Blueprint $table) {
            $table->id();
            $table->string('termino', 255);
            $table->string('tipo', 50); // 'producto', 'receta', 'categoria', etc.
            $table->integer('resultados')->default(0);
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->timestamp('fecha_busqueda');
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('set null');
            $table->index(['termino', 'tipo']);
            $table->index('fecha_busqueda');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('busquedas');
    }
};