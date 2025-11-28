<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contador_visitas', function (Blueprint $table) {
            $table->id();
            $table->string('pagina', 255);
            $table->integer('visitas')->default(0);
            $table->date('fecha');
            $table->timestamps();

            $table->unique(['pagina', 'fecha']);
            $table->index(['pagina', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contador_visitas');
    }
};