<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename spec_name to spec_key
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->renameColumn('spec_name', 'spec_key');
        });

        // Change column lengths/types
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->string('spec_key', 100)->change();
            $table->text('spec_value')->change();
        });

        // Add unique combination
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->unique(['product_id', 'spec_key']);
        });
    }

    public function down(): void
    {
        // Remove unique constraint
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'spec_key']);
        });

        // Restore original columns
        Schema::table('product_specifications', function (Blueprint $table) {
            $table->string('spec_value')->change();
            $table->string('spec_key')->change();
            $table->renameColumn('spec_key', 'spec_name');
        });
    }
};