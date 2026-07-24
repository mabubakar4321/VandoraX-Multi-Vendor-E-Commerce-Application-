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
        Schema::create('vendor_bank_details', function (Blueprint $table) {
          $table->id();
          $table->foreignId('vendor_id')->unique()->constrained('vendors')->onDelete('cascade');
          $table->string('account_holder_name');
          $table->string('account_number');
          $table->string('iban')->nullable();
          $table->string('bank_name');
          $table->enum('account_type',['savings','checking','business'])->default('savings');
          $table->boolean('is_primary')->default(true);
          $table->boolean('is_verified')->default(false);
          $table->timestamp('verification_date')->nullable();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_bank_details');
    }
};
