<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'sqlite') return;
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_gateway ENUM('stripe','bank_transfer','jazzcash','easypaisa') NOT NULL");
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'sqlite') return;
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_gateway ENUM('stripe','jazzcash','easypaisa') NOT NULL");
    }
};
