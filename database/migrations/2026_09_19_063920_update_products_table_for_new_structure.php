<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Step 1: Remove the old foreign key from sub_category_id
         * because this column will be renamed and will point
         * to the categories table instead of sub_categories.
         */
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
        });

        /*
         * Step 2: Rename columns
         */
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('sub_category_id', 'subcategory_id');
            $table->renameColumn('price', 'base_price');
            $table->renameColumn('warranty_info', 'warranty');
            $table->renameColumn('estimated_delivery_days', 'delivery_time_days');
            $table->renameColumn('is_featured', 'is_visible');
        });

        /*
         * Step 3: Change existing column definitions
         */
        Schema::table('products', function (Blueprint $table) {
            $table->longText('description')->change();

            $table->string('slug', 300)->change();

            $table->string('sku', 100)->change();

            $table->text('meta_description')
                ->nullable()
                ->change();

            $table->boolean('is_visible')
                ->default(true)
                ->change();

            $table->enum('status', ['draft', 'published'])
                ->default('draft')
                ->change();
        });

        /*
         * Step 4: Add keywords column
         */
        Schema::table('products', function (Blueprint $table) {
            $table->text('keywords')
                ->nullable()
                ->after('meta_description');
        });

        /*
         * Step 5: Remove old columns
         */
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'stock_quantity',
                'average_rating',
                'total_reviews',
                'total_sales',
            ]);
        });

        /*
         * Step 6: Change slug from normal index to UNIQUE
         */
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->unique('slug');
        });

        /*
         * Step 7: Add new foreign key for subcategory
         */
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('subcategory_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');
        });

        /*
         * Step 8: Add created_at index
         */
        Schema::table('products', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
    
    // Reverse the changes if the migration is rolled back.
    }
};