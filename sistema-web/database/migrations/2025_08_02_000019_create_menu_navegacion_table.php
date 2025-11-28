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
        Schema::create('menu_navegacion', function (Blueprint $table) {
            $table->id('id_menu');
            $table->string('nombre', 100);
            $table->string('icono', 50)->nullable();
            $table->string('url', 255)->nullable();
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('id_padre')->nullable();
            $table->enum('mostrar_en', ['ambos', 'header', 'sidebar'])->default('ambos');
            $table->unsignedBigInteger('id_rol')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_padre')
                  ->references('id_menu')
                  ->on('menu_navegacion')
                  ->onDelete('cascade');

            $table->foreign('id_rol')
                  ->references('id_rol')
                  ->on('rol')
                  ->onDelete('set null');

            // Índices
            $table->index('id_padre');
            $table->index('id_rol');
            $table->index('orden');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_navegacion');
    }
};
