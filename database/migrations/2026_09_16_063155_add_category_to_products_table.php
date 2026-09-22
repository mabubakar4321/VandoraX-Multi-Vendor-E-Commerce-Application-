<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')
                    ->nullable()
                    ->after('vendor_id');

                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('restrict');

                $table->index('category_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropForeignKeyIfExists(['category_id']);
                $table->dropIndex(['category_id']);
                $table->dropColumn('category_id');
            }
        });
    }
};