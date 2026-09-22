<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_documents', function ($table) {
            $table->integer('file_size')->nullable()->change();
            $table->string('mime_type')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vendor_documents', function ($table) {
            $table->integer('file_size')->nullable(false)->change();
            $table->string('mime_type')->nullable(false)->change();
        });
    }
};