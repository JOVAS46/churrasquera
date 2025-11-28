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
        Schema::table('pago', function (Blueprint $table) {
            // Campos específicos para PagoFácil
            $table->string('transaction_id')->nullable()->after('id_metodo_pago');
            $table->text('qr_code')->nullable()->after('transaction_id');
            $table->text('qr_image')->nullable()->after('qr_code');
            $table->string('payment_number')->nullable()->after('qr_image');
            $table->json('callback_data')->nullable()->after('payment_number');
            $table->string('client_code', 20)->nullable()->after('callback_data');
            $table->integer('currency')->default(2)->after('client_code'); // 2 = BOB
            $table->timestamp('expires_at')->nullable()->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pago', function (Blueprint $table) {
            $table->dropColumn([
                'transaction_id',
                'qr_code',
                'qr_image',
                'payment_number',
                'callback_data',
                'client_code',
                'currency',
                'expires_at'
            ]);
        });
    }
};