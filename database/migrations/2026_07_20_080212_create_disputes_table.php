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
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->string('dispute_number')->unique();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->enum('dispute_type', ['product_quality', 'not_delivered', 'wrong_product', 'fraud', 'other']);
            $table->text('customer_claim');
            $table->text('vendor_response')->nullable();
            $table->enum('admin_decision', ['pending', 'refund_customer', 'reject_claim', 'request_more_info'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('admin_decided_by')->nullable()->constrained('users');
            $table->timestamp('decided_at')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->enum('status', ['open', 'under_review', 'resolved', 'closed'])->default('open');
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
        Schema::dropIfExists('disputes');
    }
};
