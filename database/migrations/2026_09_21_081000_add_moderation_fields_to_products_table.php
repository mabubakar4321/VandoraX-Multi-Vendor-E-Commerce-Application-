<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('moderation_status', 20)->default('pending')->after('status');
            $table->text('moderation_reason')->nullable()->after('moderation_status');
            $table->unsignedBigInteger('moderated_by')->nullable()->after('moderation_reason');
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            $table->foreign('moderated_by')->references('id')->on('users')->nullOnDelete();
            $table->index('moderation_status');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropIndex(['moderation_status']);
            $table->dropColumn(['moderation_status', 'moderation_reason', 'moderated_by', 'moderated_at']);
        });
    }
};
