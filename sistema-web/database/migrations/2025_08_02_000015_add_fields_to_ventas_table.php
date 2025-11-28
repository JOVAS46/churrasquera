<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Agregar campos faltantes
            $table->unsignedBigInteger('id_usuario')->nullable()->after('receta_id');
            $table->unsignedBigInteger('id_mesa')->nullable()->after('id_usuario');
            $table->unsignedBigInteger('id_metodo_pago')->nullable()->after('id_mesa');
            $table->timestamp('fecha_venta')->useCurrent()->after('id_metodo_pago');
            $table->decimal('monto_recibido', 10, 2)->nullable()->after('fecha_venta');
            $table->decimal('vuelto', 10, 2)->nullable()->after('monto_recibido');
            
            // Agregar foreign keys
            $table->foreign('id_usuario')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('set null');
            
            $table->foreign('id_mesa')
                  ->references('id_mesa')
                  ->on('mesa')
                  ->onDelete('set null');

            $table->foreign('id_metodo_pago')
                  ->references('id_metodo_pago')
                  ->on('metodo_pago')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['id_usuario']);
            $table->dropForeign(['id_mesa']);
            $table->dropForeign(['id_metodo_pago']);
            $table->dropColumn([
                'id_usuario',
                'id_mesa',
                'id_metodo_pago',
                'fecha_venta',
                'monto_recibido',
                'vuelto'
            ]);
        });
    }
};