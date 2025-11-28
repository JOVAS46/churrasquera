<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora', function (Blueprint $table) {
            $table->id('id_bitacora');
            $table->string('accion', 100);
            $table->string('tabla_afectada', 50)->nullable();
            $table->timestamp('fecha_hora')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->text('detalles')->nullable();
            $table->unsignedBigInteger('id_usuario')->nullable();

            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora');
    }
};
