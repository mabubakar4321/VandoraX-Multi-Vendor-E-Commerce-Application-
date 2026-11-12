<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Apply the schema that migration 2026_10_15_120000_add_release_columns_to_vendor_payouts
        // was supposed to create. The migrations table records it as "Ran", but the physical
        // columns were never actually added to the database.
        if (!Schema::hasColumn('vendor_payouts', 'vendor_order_id')) {
            Schema::table('vendor_payouts', function (Blueprint $table) {
                $table->foreignId('vendor_order_id')->nullable()->after('vendor_id')->constrained('vendor_orders')->nullOnDelete();
                $table->index('vendor_order_id');
            });
        }

        if (!Schema::hasColumn('vendor_payouts', 'payment_id')) {
            Schema::table('vendor_payouts', function (Blueprint $table) {
                $table->foreignId('payment_id')->nullable()->after('vendor_order_id')->constrained('payments')->nullOnDelete();
                $table->index('payment_id');
            });
        }

        if (!Schema::hasColumn('vendor_payouts', 'gross_amount')) {
            Schema::table('vendor_payouts', function (Blueprint $table) {
                $table->decimal('gross_amount', 12, 2)->nullable()->after('amount');
            });
        }

        if (!Schema::hasColumn('vendor_payouts', 'commission_amount')) {
            Schema::table('vendor_payouts', function (Blueprint $table) {
                $table->decimal('commission_amount', 12, 2)->nullable()->after('gross_amount');
            });
        }
    }

    public function down(): void
    {
        //
    }
};
