<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // vendor_withdrawals: vendor on-demand withdrawal requests
        if (!Schema::hasTable('vendor_withdrawals')) {
            Schema::create('vendor_withdrawals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
                $table->foreignId('payout_id')->nullable()->constrained('vendor_payouts')->nullOnDelete();
                $table->decimal('amount', 12, 2);
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'failed'])->default('pending');
                $table->string('payout_method', 40)->default('bank_transfer');
                $table->string('reference', 120)->nullable();
                $table->text('rejection_reason')->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->index('vendor_id');
                $table->index('status');
            });
        }

        // commissions: ensure product_id exists and is nullable
        // Skip entirely if column already exists to avoid duplicate column errors
        if (!Schema::hasColumn('commissions', 'product_id')) {
            Schema::table('commissions', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->after('vendor_id');
            });
        }

        // vendor_payouts: add pending_amount tracking flags used by batch processing
        if (!Schema::hasColumn('vendor_payouts', 'disputed')) {
            Schema::table('vendor_payouts', function (Blueprint $table) {
                $table->boolean('disputed')->default(false)->after('commission_amount');
                $table->foreignId('withdrawal_id')->nullable()->constrained('vendor_withdrawals')->nullOnDelete()->after('disputed');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vendor_withdrawals')) {
            Schema::dropIfExists('vendor_withdrawals');
        }
        if (Schema::hasColumn('vendor_payouts', 'withdrawal_id')) {
            Schema::table('vendor_payouts', function (Blueprint $table) {
                $table->dropForeign(['withdrawal_id']);
                $table->dropColumn(['withdrawal_id', 'disputed']);
            });
        }
    }
};
