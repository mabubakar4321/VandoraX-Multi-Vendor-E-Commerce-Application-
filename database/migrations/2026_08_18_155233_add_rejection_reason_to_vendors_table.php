<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->text('rejection_reason')
                ->nullable()
                ->after('verification_date');

            $table->enum('status', [
                'pending',
                'active',
                'rejected',
                'blocked',
                'suspended',
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');

            $table->enum('status', [
                'pending',
                'active',
                'blocked',
                'suspended',
            ])->default('pending')->change();
        });
    }
};