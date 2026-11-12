<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'bank_payer_name')) {
                $table->string('bank_payer_name')->nullable();
            }
            if (!Schema::hasColumn('payments', 'bank_payment_app')) {
                $table->string('bank_payment_app')->nullable();
            }
            if (!Schema::hasColumn('payments', 'bank_payment_source')) {
                $table->string('bank_payment_source')->nullable();
            }
            if (!Schema::hasColumn('payments', 'bank_customer_account_number')) {
                $table->string('bank_customer_account_number')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columns = [
                'bank_payer_name',
                'bank_payment_app',
                'bank_payment_source',
                'bank_customer_account_number',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('payments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
