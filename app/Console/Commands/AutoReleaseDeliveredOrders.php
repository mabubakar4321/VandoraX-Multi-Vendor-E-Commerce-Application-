<?php

namespace App\Console\Commands;

use App\Services\PayoutService;
use Illuminate\Console\Command;

class AutoReleaseDeliveredOrders extends Command
{
    protected $signature = 'payouts:auto-release-delivered {--days=14 : Auto-release held escrow N days after delivery}';

    protected $description = 'Deprecated: escrow release requires explicit admin approval';

    public function handle(PayoutService $payoutService): int
    {
        $this->warn('No escrow was released. An administrator must use the payment Release Amount action.');
        return self::SUCCESS;
    }
}
