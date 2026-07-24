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
        Schema::create('vendor_blocks', function (Blueprint $table) {
            $table->id();
             $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
             $table->enum('block_type',['auto_bad_reviews','manual','suspension']);
             $table->text('reason');
             $table->integer('bad_review_count')->nullable();
             $table->foreignId('blocked_by')->nullable()->constrained('users');
             $table->boolean('can_request_unblock')->default(true);
             $table->timestamp('blocked_at')->useCurrent();
             

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_blocks');
    }
};
