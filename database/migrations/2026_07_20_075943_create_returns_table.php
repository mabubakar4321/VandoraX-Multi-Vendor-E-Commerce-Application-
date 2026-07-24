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
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
             $table->string('return_number')->unique();
             $table->foreignId('order_item_id')->constrained('order_items')->onDelete('cascade');
              $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
              $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
              $table->enum('reason', ['wrong_size', 'defective', 'damaged', 'changed_mind', 'not_as_described', 'other']);
              $table->text('description');
               $table->enum('status', ['requested', 'vendor_review', 'approved', 'rejected', 'shipped_back', 'received', 'refunded'])->default('requested');
               $table->text('return_reason_detail')->nullable();
               $table->text('vendor_response')->nullable();
               $table->timestamp('vendor_approved_at')->nullable();
                $table->string('return_label_url')->nullable();
                $table->timestamp('returned_at')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamp('refund_approved_at')->nullable();
                 $table->decimal('refund_amount', 12, 2)->nullable();
                 $table->timestamp('refund_processed_at')->nullable();
            $table->timestamps();


             $table->index('customer_id');
            $table->index('vendor_id');
            $table->index('status');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
