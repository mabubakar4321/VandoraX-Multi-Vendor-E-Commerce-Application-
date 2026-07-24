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
        Schema::create('vendors', function (Blueprint $table) {
          $table->id();
          $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
          $table->string('store_name')->unique();
          $table->string('store_slug')->unique();
          $table->text('store_description')->nullable();
          $table->string('logo_url')->nullable();
          $table->string('banner_url')->nullable();
          $table->foreignId('category_id')->nullable()->constrained('categories');
          $table->decimal('commission_rate',5,2)->default(15.00);
          $table->decimal('total_sales',12,2)->default(0);
          $table->integer('total_orders')->default(0);
          $table->decimal('average_rating',3,2)->default(0);
          $table->integer('total_followers')->default(0);
          $table->enum('status',['pending','active','blocked','suspended'])->default('pending');
          $table->boolean('is_verified')->default(false);
          $table->timestamp('verification_date')->nullable();
          $table->integer('bad_reviews_count')->default(0);
          $table->boolean('requested_unblock')->default(false);
          $table->text('unblock_message')->nullable();
          $table->timestamp('joined_at')->useCurrent();
          $table->timestamps();


        //   indexes for faster queries
        $table->index('user_id');
        $table->index('status');
        $table->index('is_verified');
        $table->index('bad_reviews_count');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
