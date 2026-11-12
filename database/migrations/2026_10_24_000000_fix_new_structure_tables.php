<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===== product_variants: align physical schema with the 09-19 update =====
        if (Schema::hasColumn('product_variants', 'stock_quantity') && !Schema::hasColumn('product_variants', 'stock')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->renameColumn('stock_quantity', 'stock');
            });
        }

        if (Schema::hasColumn('product_variants', 'variant_type')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('variant_type');
            });
        }

        if (Schema::hasColumn('product_variants', 'is_available')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropColumn('is_available');
            });
        }

        if (!Schema::hasColumn('product_variants', 'weight')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->string('weight')->nullable()->after('sku');
                $table->string('dimensions')->nullable()->after('weight');
                $table->softDeletes();
            });
        }

        // ===== product_specifications: rename spec_name -> spec_key =====
        if (Schema::hasColumn('product_specifications', 'spec_name') && !Schema::hasColumn('product_specifications', 'spec_key')) {
            Schema::table('product_specifications', function (Blueprint $table) {
                $table->renameColumn('spec_name', 'spec_key');
            });
        }

        // ===== product_images: align with new structure =====
        if (Schema::hasColumn('product_images', 'image_url') && !Schema::hasColumn('product_images', 'image_path')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->renameColumn('image_url', 'image_path');
            });
        }

        if (Schema::hasColumn('product_images', 'image_alt_text') && !Schema::hasColumn('product_images', 'alt_text')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->renameColumn('image_alt_text', 'alt_text');
            });
        }

        if (Schema::hasColumn('product_images', 'is_primary') && !Schema::hasColumn('product_images', 'is_main')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->renameColumn('is_primary', 'is_main');
            });
        }

        if (!Schema::hasColumn('product_images', 'thumbnail_path')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->string('thumbnail_path')->nullable()->after('image_path');
            });
        }

        if (!Schema::hasColumn('product_images', 'file_size')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->unsignedBigInteger('file_size')->nullable();
            });
        }

        if (!Schema::hasColumn('product_images', 'deleted_at')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        //
    }
};
