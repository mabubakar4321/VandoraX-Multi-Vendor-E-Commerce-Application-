<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `status`
            ENUM('active','inactive','pending','suspended') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `status`
            ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active'");
    }
};
