<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->after('id');
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            $table->integer('quantity')->default(0)->after('variant_id');
            $table->integer('reserved_quantity')->default(0)->after('quantity');
            $table->integer('low_stock_threshold')->default(10)->after('reserved_quantity');
            $table->timestamp('last_updated')->useCurrent()->after('low_stock_threshold');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->index('product_id');
            $table->index('variant_id');
            $table->unique(['product_id', 'variant_id']);
        });
    }

    public function down(): void
    {
        Schema::table('product_inventory', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variant_id']);
            $table->dropUnique(['product_id', 'variant_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['variant_id']);
            $table->dropColumn([
                'product_id',
                'variant_id',
                'quantity',
                'reserved_quantity',
                'low_stock_threshold',
                'last_updated',
            ]);
        });
    }
};
