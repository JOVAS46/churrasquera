<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factura', function (Blueprint $table) {
            $table->id('id_factura');
            $table->string('numero_factura', 20)->unique();
            $table->timestamp('fecha_emision')->useCurrent();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('igv', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->unsignedBigInteger('id_pago')->unique();

            $table->foreign('id_pago')
                  ->references('id_pago')
                  ->on('pago')
                  ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura');
    }
};
