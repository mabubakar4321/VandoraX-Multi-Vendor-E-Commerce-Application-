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
            DB::statement("ALTER TABLE order_items MODIFY COLUMN status ENUM('pending','processing','shipped','delivered','returned','cancelled') NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'sqlite') {
            // SQLite does not enforce ENUM; no-op — application validation handles it
        } else {
            DB::statement("ALTER TABLE order_items ALTER COLUMN status TYPE VARCHAR(20)");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE order_items MODIFY COLUMN status ENUM('pending','processing','shipped','delivered','returned') NOT NULL DEFAULT 'pending'");
        }
    }
};
