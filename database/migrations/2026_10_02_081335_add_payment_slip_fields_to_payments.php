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
        Schema::table('payments', function (Blueprint $table) {
            // Customer-provided Bank Transfer payment details
            $table->string('bank_payer_name')->nullable();
            $table->string('bank_payment_app')->nullable();
            $table->string('bank_payment_source')->nullable();
            $table->string('bank_customer_account_number')->nullable();

            // Uploaded payment slip / receipt image
            $table->string('payment_slip_path')->nullable();
            $table->string('payment_slip_original_name')->nullable();
            $table->string('payment_slip_mime_type')->nullable();
            $table->integer('payment_slip_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'bank_payer_name',
                'bank_payment_app',
                'bank_payment_source',
                'bank_customer_account_number',
                'payment_slip_path',
                'payment_slip_original_name',
                'payment_slip_mime_type',
                'payment_slip_size',
            ]);
        });
    }
};