<?php

namespace App\Console\Commands;

use App\Models\OrderStatusHistory;
use App\Models\Vendor;
use App\Models\VendorOrder;
use App\Notifications\VendorOrderReminderNotification;
use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessVendorOrderTimeouts extends Command
{
    protected $signature = 'orders:process-timeouts';
    protected $description = 'Send reminders and auto-cancel pending vendor sub-orders that exceed the configured threshold';

    public function handle(): int
    {
        $reminderHours = (int) config('vandorax.vendor_confirm_reminder_hours', 12);
        $cancelDays    = (int) config('vandorax.vendor_confirm_cancel_days', 7);

        $cancelledCount = 0;
        $remindedCount  = 0;

        $this->info('Processing vendor order timeouts...');

        // Auto-cancel sub-orders pending for longer than $cancelDays
        $toCancel = VendorOrder::pending()
            ->where('created_at', '<=', now()->subDays($cancelDays))
            ->with('order', 'vendor.user')
            ->get();

        foreach ($toCancel as $vendorOrder) {
            try {
                $vendorOrder->cancel('Auto-cancelled: vendor did not confirm within ' . $cancelDays . ' days.');

                // Restore stock for this sub-order items
                $this->restoreStockForVendorOrder($vendorOrder);

                // Refund pro-rata portion of payment
                $this->refundProRata($vendorOrder);

                $cancelledCount++;
                $this->warn("Cancelled vendor order {$vendorOrder->vendor_order_number} (order {$vendorOrder->order_id})");
            } catch (\Throwable $e) {
                $this->error("Failed to cancel {$vendorOrder->vendor_order_number}: " . $e->getMessage());
                Log::error('Auto-cancel failed', [
                    'vendor_order_id' => $vendorOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send reminders to pending sub-orders that are past the reminder threshold
        $toRemind = VendorOrder::pending()
            ->where('created_at', '<=', now()->subHours($reminderHours))
            ->where('created_at', '>', now()->subDays($cancelDays))
            ->with('vendor.user')
            ->get();

        foreach ($toRemind as $vendorOrder) {
            $vendorUser = $vendorOrder->vendor?->user;
            if ($vendorUser) {
                $vendorUser->notify(new VendorOrderReminderNotification($vendorOrder));
                $remindedCount++;
                $this->info("Reminder sent for vendor order {$vendorOrder->vendor_order_number}");
            }
        }

        $this->info("Done. Cancelled: {$cancelledCount}, Reminded: {$remindedCount}");
        return self::SUCCESS;
    }

    protected function restoreStockForVendorOrder(VendorOrder $vendorOrder): void
    {
        $items = $vendorOrder->order->items()->where('vendor_order_id', $vendorOrder->id)->get();
        $orderService = app(OrderService::class);
        foreach ($items as $item) {
            // Call the internal restore method via reflection or use the public cancelItems
            // We'll use a simpler direct approach:
            \App\Models\ProductInventory::where('product_id', $item->product_id)
                ->where('variant_id', $item->variant_id)
                ->increment('quantity', $item->quantity);

            \App\Models\ProductInventoryLog::create([
                'product_id'      => $item->product_id,
                'variant_id'      => $item->variant_id,
                'quantity_before' => null,
                'quantity_after'  => null,
                'quantity_change' => $item->quantity,
                'action_type'     => 'order_cancelled',
                'reason'          => 'Auto-cancelled vendor order — stock restored',
            ]);
        }
    }

    protected function refundProRata(VendorOrder $vendorOrder): void
    {
        $order = $vendorOrder->order;
        $payment = $order->payment;
        if (!$payment || $payment->status === 'refunded') {
            return;
        }

        // Refund proportionally based on vendor share
        $total = (float) $order->total_amount;
        $vendorShare = $total > 0 ? ((float) $vendorOrder->subtotal / $total) : 0;
        $refundAmount = round($payment->amount * $vendorShare, 2);

        if ($refundAmount > 0) {
            if ($payment->payment_gateway === 'stripe' && $payment->status === 'completed') {
                app(\App\Services\StripePaymentService::class)->refundPayment(
                    $payment,
                    $refundAmount,
                    "Pro-rata refund for auto-cancelled vendor order {$vendorOrder->vendor_order_number}"
                );
            } else {
                $payment->update([
                    'status'       => 'refunded',
                    'escrow_status'=> 'refunded',
                    'refunded_amount' => $refundAmount,
                    'refunded_at'  => now(),
                    'refund_reason'=> "Pro-rata refund for auto-cancelled vendor order {$vendorOrder->vendor_order_number}",
                ]);
            }
        }
    }
}
