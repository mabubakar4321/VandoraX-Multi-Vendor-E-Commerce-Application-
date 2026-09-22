<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('vendor_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('subcategory_id')->nullable();

            $table->string('name', 255);
            $table->string('slug', 300)->unique();
            $table->longText('description');

            $table->decimal('base_price', 12, 2);
            $table->string('sku', 100)->unique();

            $table->text('warranty')->nullable();
            $table->integer('delivery_time_days')->default(3);

            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_visible')->default(true);

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict');

            $table->foreign('subcategory_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null');

            // Indexes
            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};