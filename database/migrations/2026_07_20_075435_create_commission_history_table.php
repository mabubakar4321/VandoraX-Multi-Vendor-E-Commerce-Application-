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
        Schema::create('commission_history', function (Blueprint $table) {
           $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('description');
            $table->enum('type', ['pending', 'released', 'paid', 'reversed']);
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();
            
            $table->index('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_history');
    }
};
