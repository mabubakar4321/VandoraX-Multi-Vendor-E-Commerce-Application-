<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'sqlite') return;
        DB::statement("ALTER TABLE `vendor_bank_details` MODIFY COLUMN `account_type`
            ENUM('current','savings','business') NOT NULL DEFAULT 'current'");
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'sqlite') return;
        DB::statement("ALTER TABLE `vendor_bank_details` MODIFY COLUMN `account_type`
            ENUM('savings','checking','business') NOT NULL");
    }
};
