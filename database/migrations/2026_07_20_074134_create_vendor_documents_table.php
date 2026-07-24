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
        Schema::create('vendor_documents', function (Blueprint $table) {
           $table->id();
           $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
           $table->enum('document_type',['cnic','tax_certificate','bank_proof','store_photo']);
           $table->string('file_path');
           $table->string('file_name');
           $table->integer('file_size');
           $table->string('mime_type');
           $table->enum('status',['pending','approved','rejected'])->default('pending');
           $table->text('rejection_reason')->nullable();
           $table->foreignId('verified_by')->nullable()->constrained('users');
           $table->timestamp('verified_at')->nullable();
           $table->timestamps();


           $table->index('vendor_id');
           $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_documents');
    }
};
