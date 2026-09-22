<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename stock_quantity to stock
        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('stock_quantity', 'stock');
        });

        // Remove old variant_type and is_available columns
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'variant_type',
                'is_available',
            ]);
        });

        // Change existing column lengths
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('variant_name', 100)->change();
            $table->string('variant_value', 100)->change();
            $table->string('sku', 100)->change();
        });

        // Add new columns
      Schema::table('product_variants', function (Blueprint $table) {
    $table->string('weight')->nullable()->after('sku');
    $table->string('dimensions')->nullable()->after('weight');
    $table->softDeletes();
});

        // Add unique combination
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unique(['product_id', 'variant_value']);
        });
    }

    public function down(): void
    {
        // Rename stock back
        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('stock', 'stock_quantity');
        });

        // Remove new columns
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'weight',
                'dimensions',
            ]);
        });

        // Add old columns back
        Schema::table('product_variants', function (Blueprint $table) {
            $table->enum('variant_type', [
                'size',
                'color',
                'storage',
                'other'
            ])->after('variant_name');

            $table->boolean('is_available')
                ->default(true)
                ->after('stock_quantity');
        });

        // Remove new unique combination
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'variant_value']);
        });
    }
};