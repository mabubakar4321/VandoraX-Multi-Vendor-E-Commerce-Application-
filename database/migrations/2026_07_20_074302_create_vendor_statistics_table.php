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
        Schema::create('vendor_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->unique()->constrained('vendors')->onDelete('cascade');
            $table->decimal('total_sales_amount',12,2)->default(0);
            $table->integer('total_orders_count')->default(0);
            $table->integer('total_products')->default(0);
            $table->integer('total_customers')->default(0);
            $table->integer('average_response_time')->default(0);
            $table->decimal('return_rate',5,2)->default(0);
            $table->decimal('cancellation_rate',5,2)->default(0);
            $table->integer('total_positive_reviews')->default(0);
            $table->integer('total_negative_reviews')->default(0);
            $table->decimal('completion_rate',5,2)->default(0);
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_statistics');
    }
};
