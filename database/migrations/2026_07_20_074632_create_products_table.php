<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
             $table->id();
             $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
             $table->foreignId('category_id')->constrained('categories');
             $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories');
             $table->string('name');
             $table->string('slug');
             $table->text('description');
             $table->string('sku')->unique();
             $table->decimal('price',12,2);
             $table->integer('stock_quantity')->default(0);
             $table->text('warranty_info')->nullable();
             $table->integer('estimated_delivery_days')->default(3);
             $table->decimal('average_rating', 3, 2)->default(0);
             $table->integer('total_reviews')->default(0);
             $table->integer('total_sales')->default(0);
             $table->enum('status', ['draft', 'published', 'inactive', 'deleted'])->default('draft');
             $table->boolean('is_featured')->default(false);
             $table->string('meta_title')->nullable();
             $table->string('meta_description')->nullable();
             $table->timestamps();
             $table->softDeletes(); 



            $table->index('vendor_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('slug');
            $table->fullText(['name', 'description']); 
    

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
