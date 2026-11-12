<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('returns', function (Blueprint $table) {
            $table->string('status')->default('requested')->change();
            $table->text('return_shipping_address')->nullable();
            $table->text('return_instructions')->nullable();
            $table->string('return_tracking_number')->nullable();
            $table->string('return_tracking_url')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('inspection_notes')->nullable();
            $table->string('refund_source')->nullable();
            $table->decimal('vendor_debit_amount', 12, 2)->default(0);
            $table->timestamp('return_deadline')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('shipped_back_at')->nullable();
            $table->timestamp('refund_failed_at')->nullable();
            $table->foreignId('admin_decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('admin_decided_at')->nullable();
            $table->text('admin_decision_notes')->nullable();
        });

        Schema::create('return_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });

        Schema::table('refund_records', function (Blueprint $table) {
            $table->string('refund_source')->nullable();
            $table->string('failure_reason')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('refunded_amount', 12, 2)->default(0);
        });

        Schema::table('escrow_accounts', function (Blueprint $table) {
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->nullable();
        });

        Schema::table('disputes', function (Blueprint $table) {
            $table->foreignId('return_id')->nullable()->constrained('returns')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('disputes', fn (Blueprint $table) => $table->dropForeign(['return_id'])->dropColumn('return_id'));
        Schema::table('escrow_accounts', fn (Blueprint $table) => $table->dropColumn(['refunded_amount', 'remaining_amount']));
        Schema::table('payments', fn (Blueprint $table) => $table->dropColumn('refunded_amount'));
        Schema::table('refund_records', function (Blueprint $table) {
            $table->dropForeign(['processed_by']);
            $table->dropColumn(['refund_source', 'failure_reason', 'processed_by']);
        });
        Schema::dropIfExists('return_photos');
        Schema::table('returns', function (Blueprint $table) {
            $table->dropForeign(['admin_decided_by']);
            $table->dropColumn([
                'return_shipping_address', 'return_instructions', 'return_tracking_number',
                'return_tracking_url', 'rejection_reason', 'inspection_notes', 'refund_source',
                'vendor_debit_amount', 'return_deadline', 'rejected_at', 'shipped_back_at',
                'refund_failed_at', 'admin_decided_by', 'admin_decided_at', 'admin_decision_notes',
            ]);
        });
    }
};
