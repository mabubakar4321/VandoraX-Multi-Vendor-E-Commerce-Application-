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
        Schema::create('refund_records', function (Blueprint $table) {
            $table->id();
             $table->foreignId('return_id')->constrained('returns')->onDelete('cascade');
             $table->decimal('refund_amount', 12, 2);
              $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
                  $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refund_records');
    }
};
