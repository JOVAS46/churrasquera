<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedido', function (Blueprint $table) {
            $table->unsignedBigInteger('id_cocinero')->nullable()->after('id_mesero');
            
            $table->foreign('id_cocinero')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pedido', function (Blueprint $table) {
            $table->dropForeign(['id_cocinero']);
            $table->dropColumn('id_cocinero');
        });
    }
};