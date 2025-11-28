<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('tiempo_preparacion')->nullable();
            $table->boolean('disponible')->default(true);
            $table->string('imagen', 255)->nullable();
            $table->unsignedBigInteger('id_categoria');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('id_categoria')
                  ->references('id_categoria')
                  ->on('categoria')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
