<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_usuario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->enum('tema', ['ninos', 'jovenes', 'adultos'])->default('adultos');
            $table->enum('modo', ['dia', 'noche', 'auto'])->default('auto');
            $table->enum('tamano_letra', ['pequeno', 'normal', 'grande'])->default('normal');
            $table->enum('contraste', ['normal', 'alto'])->default('normal');
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuario')->onDelete('cascade');
            $table->unique('id_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_usuario');
    }
};