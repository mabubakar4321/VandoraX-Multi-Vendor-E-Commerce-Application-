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
        Schema::create('vendor_orders', function (Blueprint $table) {
            $table->id();
             $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
              $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
              $table->string('vendor_order_number')->unique();
              $table->decimal('subtotal', 12, 2);
              $table->decimal('commission_amount', 12, 2);
              $table->decimal('vendor_amount', 12, 2);
              $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
              $table->string('tracking_number')->nullable();
              $table->string('tracking_url')->nullable();
              $table->timestamp('confirmed_at')->nullable();
               $table->timestamp('shipped_at')->nullable();
               $table->timestamp('delivered_at')->nullable();
            
            $table->timestamps();


            $table->index('order_id');
            $table->index('vendor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_orders');
    }
};
