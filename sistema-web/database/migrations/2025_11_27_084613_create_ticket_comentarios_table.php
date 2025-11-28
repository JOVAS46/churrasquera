<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_comentarios', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('usuario_id');
            
            $table->text('comentario');
            $table->enum('tipo', ['comentario', 'cambio_estado', 'asignacion', 'escalamiento'])->default('comentario');
            $table->boolean('es_interno')->default(false); // Si es visible solo para staff
            $table->boolean('es_resolucion')->default(false); // Si es el comentario de resolución
            
            // Adjuntos y referencias
            $table->json('adjuntos')->nullable(); // URLs de archivos adjuntos
            $table->string('estado_anterior')->nullable(); // Para cambios de estado
            $table->string('estado_nuevo')->nullable();
            
            // Información temporal
            $table->timestamp('fecha_lectura')->nullable(); // Cuando fue leído
            $table->integer('tiempo_respuesta_minutos')->nullable(); // Tiempo desde comentario anterior
            
            $table->timestamps();
            
            // Índices
            $table->index(['ticket_id', 'created_at']);
            $table->index(['usuario_id']);
            $table->index(['tipo']);
            
            // Llaves foráneas
            $table->foreign('ticket_id')->references('id_ticket')->on('tickets')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id_usuario')->on('usuario')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_comentarios');
    }
};
