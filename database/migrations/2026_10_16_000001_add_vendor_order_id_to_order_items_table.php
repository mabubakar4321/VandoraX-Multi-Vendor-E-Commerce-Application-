<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_order_id')->nullable()->after('variant_id');

            $table->foreign('vendor_order_id')
                ->references('id')
                ->on('vendor_orders')
                ->nullOnDelete();

            $table->index('vendor_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['vendor_order_id']);
            $table->dropIndex('vendor_order_id');
            $table->dropColumn('vendor_order_id');
        });
    }
};
