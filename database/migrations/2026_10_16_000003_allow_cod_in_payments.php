<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            // Cover both payment_method and payment_gateway for COD
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('card','bank_transfer','wallet','cod') NOT NULL");
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_gateway ENUM('stripe','bank_transfer','jazzcash','easypaisa','cod') NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('card','bank_transfer','wallet') NOT NULL");
            DB::statement("ALTER TABLE payments MODIFY COLUMN payment_gateway ENUM('stripe','bank_transfer','jazzcash','easypaisa') NOT NULL");
        }
    }
};
