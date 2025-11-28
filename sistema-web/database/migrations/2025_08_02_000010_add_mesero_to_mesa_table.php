<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesa', function (Blueprint $table) {
            $table->unsignedBigInteger('id_mesero')->nullable()->after('estado');
            
            $table->foreign('id_mesero')
                  ->references('id_usuario')
                  ->on('usuario')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('mesa', function (Blueprint $table) {
            $table->dropForeign(['id_mesero']);
            $table->dropColumn('id_mesero');
        });
    }
};