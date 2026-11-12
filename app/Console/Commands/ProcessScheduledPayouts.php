<?php

namespace App\Console\Commands;

use App\Services\PayoutService;
use Illuminate\Console\Command;

class ProcessScheduledPayouts extends Command
{
    protected $signature = 'payouts:process-scheduled {--frequency=weekly : Payout frequency (daily|weekly|monthly)}';

    protected $description = 'Batch process pending vendor payouts that cross the configured minimum threshold';

    public function handle(PayoutService $payoutService): int
    {
        $frequency = (string) $this->option('frequency');

        // Only run on the configured schedule cadence
        $now = now();
        $shouldRun = match ($frequency) {
            'daily' => true,
            'weekly' => $now->isSunday(),
            'monthly' => $now->isFirstOfMonth(),
            default => true,
        };

        if (!$shouldRun) {
            $this->info("Not the scheduled payout day for frequency '{$frequency}'. Skipping.");
            return self::SUCCESS;
        }

        $this->info("Processing scheduled payouts ({$frequency})...");

        $summary = $payoutService->processScheduledPayouts($frequency);

        $this->info('Done.');
        $this->line('  Vendors processed: ' . $summary['vendors_processed']);
        $this->line('  Total credited: ₨' . number_format($summary['total_credited'], 2));
        $this->line('  Skipped (disputed/below threshold): ' . $summary['skipped']);

        return self::SUCCESS;
    }
}
