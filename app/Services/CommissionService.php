<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Commission;
use App\Models\VendorOrder;
use App\Models\VendorPayout;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Calculate commission for an order amount at a given rate.
     */
    public function calculate(float $orderAmount, float $rate): array
    {
        $commission = round($orderAmount * ($rate / 100), 2);
        return [
            'rate' => $rate,
            'commission' => $commission,
            'payout' => round($orderAmount - $commission, 2),
        ];
    }

    /**
     * Get the effective commission rate for a category (falls back to vendor or default).
     */
    public function rateForCategory(?int $categoryId, ?VendorOrder $vendorOrder = null): float
    {
        if ($categoryId) {
            $rate = Category::find($categoryId)?->commission_rate;
            if ($rate !== null) {
                return (float) $rate;
            }
        }
        if ($vendorOrder) {
            $vendorRate = $vendorOrder->vendor?->commission_rate;
            if ($vendorRate !== null) {
                return (float) $vendorRate;
            }
        }
        return (float) config('vandorax.default_commission_rate', 15);
    }

    /**
     * Record a commission for a vendor sub-order. Idempotent per vendor order.
     *
     * Returns [commissionRecord, wasCreated].
     */
    public function recordForVendorOrder(VendorOrder $vendorOrder, ?float $rateOverride = null): array
    {
        $existing = Commission::where('vendor_order_id', $vendorOrder->id)->first();
        if ($existing) {
            return [$existing, false];
        }

        $rate = $rateOverride ?? $this->rateForCategory($this->primaryCategoryId($vendorOrder), $vendorOrder);
        $gross = (float) $vendorOrder->subtotal;
        $calc = $this->calculate($gross, $rate);

        $commission = Commission::create([
            'vendor_order_id' => $vendorOrder->id,
            'product_id' => $this->primaryProductId($vendorOrder),
            'order_amount' => $gross,
            'commission_rate' => $calc['rate'],
            'commission_amount' => $calc['commission'],
            'vendor_payout' => $calc['payout'],
            'status' => 'pending',
        ]);

        // Keep the sub-order's stored commission fields in sync with the recorded rate
        $vendorOrder->update([
            'commission_amount' => $calc['commission'],
            'vendor_amount' => $calc['payout'],
        ]);

        return [$commission, true];
    }

    /**
     * Record commission for every sub-order of a main order (checkout flow).
     */
    public function recordForOrder($order): array
    {
        $results = [];
        foreach ($order->vendorOrders as $vo) {
            $results[$vo->id] = $this->recordForVendorOrder($vo);
        }
        return $results;
    }

    /**
     * Release commission + credit vendor payout when an order is delivered
     * (or auto-confirmed after X days). Idempotent per payment.
     *
     * @return array summary of what was released
     */
    public function releaseForPayment(\App\Models\Payment $payment, string $trigger = 'manual'): array
    {
        return DB::transaction(function () use ($payment, $trigger) {
            $order = $payment->order;

            // Already released? Duplicate guard.
            if (VendorPayout::where('payment_id', $payment->id)->exists()) {
                return ['released' => false, 'reason' => 'already_released', 'payouts' => []];
            }

            $payouts = [];
            foreach ($order->vendorOrders as $vo) {
                if ($vo->status === 'cancelled') {
                    continue;
                }

                [$commission, ] = $this->recordForVendorOrder($vo);

                $gross = (float) $vo->subtotal;
                $commissionAmount = (float) $commission->commission_amount;
                $net = round($gross - $commissionAmount, 2);

                $payout = VendorPayout::create([
                    'vendor_id' => $vo->vendor_id,
                    'vendor_order_id' => $vo->id,
                    'payment_id' => $payment->id,
                    'amount' => $net,
                    'gross_amount' => $gross,
                    'commission_amount' => $commissionAmount,
                    'status' => 'completed',
                    'payout_method' => 'bank_transfer',
                    'transaction_id' => 'REL-' . $payment->id . '-' . $vo->id . '-' . substr(md5(uniqid()), 0, 8),
                    'notes' => "Escrow release ({$trigger}). Order {$order->order_number}, sub-order {$vo->vendor_order_number}.",
                    'requested_at' => now(),
                    'processed_at' => now(),
                ]);

                $commission->update([
                    'status' => 'released',
                    'released_at' => now(),
                ]);

                $payouts[] = $payout;
            }

            // Release the escrow account + payment flags
            $escrow = $payment->escrowAccount;
            if ($escrow && $escrow->status === 'held') {
                $escrow->update(['status' => 'released', 'released_at' => now()]);
            }
            $payment->update(['escrow_status' => 'released', 'released_at' => now()]);

            // Promote order payment status to completed/confirmed
            if ($order->payment_status !== 'refunded') {
                $order->update(['payment_status' => 'completed']);
            }

            return ['released' => true, 'payouts' => $payouts, 'trigger' => $trigger];
        });
    }

    /**
     * Summarize pending payout per vendor: total un-released commission + net vendor share.
     */
    public function pendingPayoutsByVendor(): array
    {
        $minPayout = (float) config('vandorax.min_payout_amount', 5000);

        $query = DB::table('vendor_orders as vo')
            ->join('orders as o', 'vo.order_id', '=', 'o.id')
            ->leftJoin('payments as p', 'o.payment_id', '=', 'p.id')
            ->whereNotIn('vo.status', ['cancelled'])
            ->whereIn('p.status', ['completed', 'pending'])
            ->whereNull('p.refunded_at');

        $rows = $query->selectRaw("
                vo.vendor_id,
                SUM(vo.vendor_amount) as pending_net,
                SUM(vo.commission_amount) as pending_commission,
                COUNT(*) as orders
            ")->groupBy('vo.vendor_id')->get();

        return $rows->map(function ($row) use ($minPayout) {
            return [
                'vendor_id' => $row->vendor_id,
                'pending_net' => (float) $row->pending_net,
                'pending_commission' => (float) $row->pending_commission,
                'orders' => (int) $row->orders,
                'meets_threshold' => (float) $row->pending_net >= $minPayout,
            ];
        })->all();
    }

    protected function primaryCategoryId(VendorOrder $vo): ?int
    {
        $itemId = $vo->order->items()
            ->where('vendor_order_id', $vo->id)
            ->whereNotNull('product_id')
            ->value('product_id');
        if (!$itemId) {
            return null;
        }
        $product = \App\Models\Product::find($itemId);
        return $product?->category_id;
    }

    protected function primaryProductId(VendorOrder $vo): ?int
    {
        return $vo->order->items()
            ->where('vendor_order_id', $vo->id)
            ->whereNotNull('product_id')
            ->value('product_id');
    }

    /**
     * Commission report aggregates for dashboards.
     *
     * @return array{
     *   by_category: array, by_vendor: array, totals: array,
     *   this_month: array, lifetime: array,
     * }
     */
    public function report(): array
    {
        $base = fn () => Commission::whereIn('status', ['released', 'paid']);

        $totalCommission = (float) $base()->sum('commission_amount');
        $totalGross = (float) $base()->sum('order_amount');
        $totalPaid = (float) $base()->where('status', 'paid')->sum('commission_amount');

        // By category (via the primary product of each vendor order)
        $byCategory = Commission::with(['vendorOrder', 'product.category'])
            ->whereIn('status', ['released', 'paid'])
            ->get()
            ->groupBy(fn ($c) => $c->product?->category_id ?? null)
            ->map(function ($group, $catId) {
                return [
                    'category_id' => $catId,
                    'category' => collect($group->first()->product?->category ?? null)?->name ?? 'Uncategorized',
                    'commission' => (float) $group->sum('commission_amount'),
                    'gross' => (float) $group->sum('order_amount'),
                    'orders' => $group->count(),
                ];
            })
            ->values()
            ->sortByDesc('commission');

        // By vendor
        $byVendor = Commission::with('vendorOrder.vendor')
            ->whereIn('status', ['released', 'paid'])
            ->get()
            ->groupBy(fn ($c) => $c->vendorOrder?->vendor_id)
            ->map(function ($group, $vendorId) {
                $vendor = $group->first()->vendorOrder?->vendor;
                return [
                    'vendor_id' => $vendorId,
                    'vendor' => $vendor?->store_name ?? 'Vendor #' . $vendorId,
                    'commission' => (float) $group->sum('commission_amount'),
                    'gross' => (float) $group->sum('order_amount'),
                    'payouts' => (float) $group->sum('vendor_payout'),
                    'orders' => $group->count(),
                ];
            })
            ->values()
            ->sortByDesc('commission');

        return [
            'by_category' => $byCategory->all(),
            'by_vendor' => $byVendor->all(),
            'totals' => [
                'commission' => $totalCommission,
                'gross' => $totalGross,
                'paid' => $totalPaid,
                'pending' => $totalCommission - $totalPaid,
                'records' => $base()->count(),
            ],
            'this_month' => [
                'commission' => (float) Commission::whereIn('status', ['released', 'paid'])
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('commission_amount'),
            ],
            'lifetime' => [
                'commission' => $totalCommission,
            ],
        ];
    }
}
