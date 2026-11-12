<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send vendor order confirmation reminders every 6 hours
Schedule::command('orders:process-timeouts')
    ->everySixHours();

// Automatic vendor payout schedule (default weekly; use --frequency=daily|weekly|monthly)
Schedule::command('payouts:process-scheduled')
    ->weeklyOn(0, '08:00');
