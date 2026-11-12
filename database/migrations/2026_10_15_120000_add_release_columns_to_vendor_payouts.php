<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_payouts', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_payouts', 'vendor_order_id')) {
                $table->foreignId('vendor_order_id')->nullable()->after('vendor_id')->constrained('vendor_orders')->nullOnDelete();
                $table->index('vendor_order_id');
            }
            if (!Schema::hasColumn('vendor_payouts', 'payment_id')) {
                $table->foreignId('payment_id')->nullable()->after('vendor_order_id')->constrained('payments')->nullOnDelete();
                $table->unique(['payment_id', 'vendor_id', 'vendor_order_id'], 'payout_unique_release');
            }
            if (!Schema::hasColumn('vendor_payouts', 'gross_amount')) {
                $table->decimal('gross_amount', 12, 2)->nullable()->after('amount')->comment('Original order amount before commission');
            }
            if (!Schema::hasColumn('vendor_payouts', 'commission_amount')) {
                $table->decimal('commission_amount', 12, 2)->nullable()->after('gross_amount')->comment('Admin commission deducted');
            }
            // notes already exists
        });
    }

    public function down(): void
    {
        Schema::table('vendor_payouts', function (Blueprint $table) {
            try { $table->dropUnique('payout_unique_release'); } catch (\Throwable $e) {}
            $table->dropForeign(['vendor_order_id']);
            $table->dropForeign(['payment_id']);
            $table->dropColumn(['vendor_order_id', 'payment_id', 'gross_amount', 'commission_amount']);
        });
    }
};
