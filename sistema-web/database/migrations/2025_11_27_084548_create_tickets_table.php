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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('id_ticket');
            $table->string('codigo_ticket')->unique(); // Código único del ticket (ej: TKT-2025-001)
            
            // Información básica
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('categoria', [
                'soporte_tecnico', 
                'queja_cliente', 
                'sugerencia', 
                'problema_servicio',
                'incidente_operativo',
                'solicitud_mantenimiento',
                'consulta_general'
            ]);
            $table->enum('prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->enum('estado', [
                'abierto', 
                'en_proceso', 
                'esperando_respuesta',
                'resuelto', 
                'cerrado',
                'escalado'
            ])->default('abierto');
            
            // Relaciones
            $table->unsignedBigInteger('usuario_creador_id'); // Quien creó el ticket
            $table->unsignedBigInteger('usuario_asignado_id')->nullable(); // A quien está asignado
            $table->unsignedBigInteger('mesa_id')->nullable(); // Si está relacionado con una mesa
            $table->unsignedBigInteger('pedido_id')->nullable(); // Si está relacionado con un pedido
            $table->unsignedBigInteger('reserva_id')->nullable(); // Si está relacionado con una reserva
            
            // Información temporal
            $table->timestamp('fecha_limite')->nullable(); // Fecha límite de resolución
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamp('fecha_cerrado')->nullable();
            
            // Campos adicionales
            $table->json('etiquetas')->nullable(); // Etiquetas personalizadas
            $table->text('resolucion')->nullable(); // Descripción de la resolución
            $table->string('canal_origen')->default('sistema'); // web, telefono, presencial, etc.
            $table->boolean('es_interno')->default(false); // Si es comunicación interna
            $table->integer('tiempo_respuesta_minutos')->nullable(); // SLA de respuesta
            $table->decimal('costo_resolucion', 10, 2)->nullable(); // Si aplica un costo
            
            $table->timestamps();
            
            // Índices
            $table->index(['estado', 'prioridad']);
            $table->index(['categoria', 'estado']);
            $table->index(['usuario_creador_id']);
            $table->index(['usuario_asignado_id']);
            $table->index(['fecha_limite']);
            $table->index(['created_at']);
            
            // Llaves foráneas
            $table->foreign('usuario_creador_id')->references('id_usuario')->on('usuario')->onDelete('cascade');
            $table->foreign('usuario_asignado_id')->references('id_usuario')->on('usuario')->onDelete('set null');
            $table->foreign('mesa_id')->references('id_mesa')->on('mesa')->onDelete('set null');
            $table->foreign('pedido_id')->references('id_pedido')->on('pedido')->onDelete('set null');
            $table->foreign('reserva_id')->references('id_reserva')->on('reserva')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
