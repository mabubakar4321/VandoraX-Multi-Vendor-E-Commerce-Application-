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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
             $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
             $table->integer('product_rating'); 
            $table->integer('vendor_rating'); 
            $table->string('review_title');
            $table->text('review_text');
             $table->boolean('is_verified_purchase')->default(true);
             $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
              $table->integer('is_helpful_count')->default(0);
            $table->integer('is_unhelpful_count')->default(0);
             $table->text('rejected_reason')->nullable();
            $table->timestamps();



            $table->index('product_id');
            $table->index('vendor_id');
            $table->index('customer_id');
            $table->index('product_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
