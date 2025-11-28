<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa', function (Blueprint $table) {
            $table->id('id_mesa');
            $table->integer('numero_mesa')->unique();
            $table->integer('capacidad');
            $table->string('ubicacion', 50)->nullable();
            $table->enum('estado', ['disponible', 'ocupada', 'reservada', 'mantenimiento'])
                  ->default('disponible');
            $table->timestamp('created_at')->useCurrent();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa');
    }
};
