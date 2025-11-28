<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserva', function (Blueprint $table) {
            $table->id('id_reserva');
            $table->date('fecha_reserva');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('numero_personas');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'completada'])
                  ->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_mesa');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_cliente')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('cascade');

            $table->foreign('id_mesa')
                  ->references('id_mesa')
                  ->on('mesa')
                  ->onDelete('restrict');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva');
    }
};
