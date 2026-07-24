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
        Schema::create('vendor_bad_reviews', function (Blueprint $table) {
            $table->id();
             $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
             $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
              $table->integer('review_rating');
              $table->timestamp('flagged_at')->useCurrent();
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('review_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_bad_reviews');
    }
};
