<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_unblock_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_unblock_requests', 'improvements')) {
                $table->text('improvements')->nullable()->after('vendor_id');
            }
            if (!Schema::hasColumn('vendor_unblock_requests', 'reason_to_unblock')) {
                $table->text('reason_to_unblock')->nullable()->after('improvements');
            }
            if (!Schema::hasColumn('vendor_unblock_requests', 'evidence_files')) {
                $table->json('evidence_files')->nullable()->after('reason_to_unblock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_unblock_requests', function (Blueprint $table) {
            $table->dropColumn(['improvements', 'reason_to_unblock', 'evidence_files']);
        });
    }
};
