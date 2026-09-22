<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename existing columns
        Schema::table('product_images', function (Blueprint $table) {
            $table->renameColumn('image_url', 'image_path');
            $table->renameColumn('image_alt_text', 'alt_text');
            $table->renameColumn('is_primary', 'is_main');
        });

        // Change display order and add new columns
        Schema::table('product_images', function (Blueprint $table) {
            $table->string('thumbnail_path')
                ->nullable()
                ->after('image_path');

            $table->integer('file_size')
                ->nullable()
                ->after('alt_text');

            $table->softDeletes();
        });

        // Add index for is_main
        Schema::table('product_images', function (Blueprint $table) {
            $table->index('is_main');
        });
    }

    public function down(): void
    {
        // Remove added columns
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex(['is_main']);

            $table->dropColumn([
                'thumbnail_path',
                'file_size',
                'deleted_at',
            ]);
        });

        // Rename columns back
        Schema::table('product_images', function (Blueprint $table) {
            $table->renameColumn('image_path', 'image_url');
            $table->renameColumn('alt_text', 'image_alt_text');
            $table->renameColumn('is_main', 'is_primary');
        });
    }
};