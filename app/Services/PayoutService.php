<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\EscrowAccount;
use App\Models\Order;
use App\Models\VendorOrder;
use App\Models\VendorPayout;
use App\Notifications\VendorPayoutFailedNotification;
use App\Notifications\VendorPayoutProcessedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutService
{
    protected CommissionService $commissionService;

    public function __construct(CommissionService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    /**
     * Release escrow only after an explicit admin approval.
     *
     * Delivery must never release escrow or create a vendor payout.
     */
    public function releaseOrderEscrow(Order $order, string $trigger = 'delivery'): array
    {
        if ($trigger !== 'manual_admin_release') {
            return ['released' => false, 'reason' => 'admin_approval_required', 'payouts' => []];
        }

        $payment = $order->payment;
        if (!$payment || !$payment->escrowAccount) {
            return ['released' => false, 'reason' => 'no_escrow', 'payouts' => []];
        }

        if ($payment->escrow_status === 'released') {
            return ['released' => false, 'reason' => 'already_released', 'payouts' => []];
        }

        return $this->commissionService->releaseForPayment($payment, $trigger);
    }

    /**
     * Compute the pending payout amount available to a vendor.
     * Excludes cancelled orders, refunded payments, and disputed orders.
     */
    public function vendorPendingPayout(int $vendorId): array
    {
        $minPayout = (float) config('vandorax.min_payout_amount', 5000);

        $base = DB::table('vendor_orders as vo')
            ->join('orders as o', 'vo.order_id', '=', 'o.id')
            ->leftJoin('payments as p', 'o.payment_id', '=', 'p.id')
            ->where('vo.vendor_id', $vendorId)
            ->whereNotIn('vo.status', ['cancelled'])
            ->whereNull('p.refunded_at')
            ->where('p.escrow_status', 'released')
            ->where('vo.commission_amount', '>', 0);

        // Hold payout if any of the vendor's orders is under an open dispute
        $hasOpenDispute = DB::table('disputes')
            ->where('vendor_id', $vendorId)
            ->whereIn('status', ['open', 'under_review'])
            ->exists();

        // Exclude orders tied to payouts already released (completed payout records)
        $releasedVendorOrderIds = VendorPayout::where('vendor_id', $vendorId)
            ->whereNotNull('vendor_order_id')
            ->whereIn('status', ['completed', 'processing'])
            ->pluck('vendor_order_id');

        if ($releasedVendorOrderIds->isNotEmpty()) {
            $base->whereNotIn('vo.id', $releasedVendorOrderIds);
        }

        $row = $base->selectRaw("
                COALESCE(SUM(vo.vendor_amount), 0) as pending_net,
                COALESCE(SUM(vo.commission_amount), 0) as pending_commission,
                COUNT(*) as eligible_orders
            ")->first();

        return [
            'vendor_id' => $vendorId,
            'pending_net' => (float) ($row->pending_net ?? 0),
            'pending_commission' => (float) ($row->pending_commission ?? 0),
            'eligible_orders' => (int) ($row->eligible_orders ?? 0),
            'meets_threshold' => (float) ($row->pending_net ?? 0) >= $minPayout,
            'held_due_to_dispute' => $hasOpenDispute,
            'min_threshold' => $minPayout,
        ];
    }

    /**
     * Batch process payouts for all vendors whose pending payout crosses the
     * configured minimum. Called by the scheduled payout command.
     */
    public function processScheduledPayouts(string $frequency = 'weekly'): array
    {
        $minPayout = (float) config('vandorax.min_payout_amount', 5000);
        $summary = ['vendors_processed' => 0, 'total_credited' => 0.0, 'skipped' => 0];

        $vendorIds = DB::table('vendor_orders as vo')
            ->join('orders as o', 'vo.order_id', '=', 'o.id')
            ->leftJoin('payments as p', 'o.payment_id', '=', 'p.id')
            ->whereNotIn('vo.status', ['cancelled'])
            ->whereNull('p.refunded_at')
            ->where('p.escrow_status', 'released')
            ->where('vo.commission_amount', '>', 0)
            ->groupBy('vo.vendor_id')
            ->havingRaw('SUM(vo.vendor_amount) >= ?', [$minPayout])
            ->pluck('vo.vendor_id');

        // Exclude vendors with open disputes
        $disputedVendors = collect(DB::table('disputes')
            ->whereIn('status', ['open', 'under_review'])
            ->distinct()
            ->pluck('vendor_id'));

        foreach ($vendorIds as $vendorId) {
            if ($disputedVendors->contains($vendorId)) {
                $summary['skipped']++;
                continue;
            }

            $payouts = $this->processVendorPayout($vendorId, $frequency);
            if (count($payouts) > 0) {
                $summary['vendors_processed']++;
                $summary['total_credited'] += collect($payouts)->sum('amount');
            }
        }

        Log::info('Scheduled payout processing complete', [
            'frequency' => $frequency,
            'summary' => $summary,
        ]);

        return $summary;
    }

    /**
     * Process pending payouts for a single vendor.
     */
    public function processVendorPayout(int $vendorId, string $trigger = 'manual'): array
    {
        $pending = $this->vendorPendingPayout($vendorId);

        if ($pending['held_due_to_dispute']) {
            Log::warning('Vendor payout held due to open dispute', ['vendor_id' => $vendorId]);
            return [];
        }

        $payouts = [];

        DB::transaction(function () use (&$payouts, $vendorId, $trigger) {
            $vendorOrders = VendorOrder::where('vendor_id', $vendorId)
                ->whereNotIn('status', ['cancelled'])
                ->where('commission_amount', '>', 0)
                ->whereHas('order.payment', fn ($query) => $query->where('escrow_status', 'released'))
                ->whereDoesntHave('commissions', fn($q) => $q->whereIn('status', ['released', 'paid']))
                ->with('order.payment', 'order.customer')
                ->get();

            foreach ($vendorOrders as $vo) {
                $commission = $this->commissionService->recordForVendorOrder($vo)[0];
                $gross = (float) $vo->subtotal;
                $commissionAmount = (float) $commission->commission_amount;
                $net = round($gross - $commissionAmount, 2);

                $payout = VendorPayout::create([
                    'vendor_id' => $vendorId,
                    'vendor_order_id' => $vo->id,
                    'payment_id' => $vo->order->payment?->id,
                    'amount' => $net,
                    'gross_amount' => $gross,
                    'commission_amount' => $commissionAmount,
                    'status' => 'completed',
                    'payout_method' => 'bank_transfer',
                    'transaction_id' => 'PAYOUT-' . now()->format('Ymd') . '-' . substr(md5($vo->id . $vendorId), 0, 8),
                    'notes' => "Batch payout ({$trigger}). Sub-order {$vo->vendor_order_number}.",
                    'requested_at' => now(),
                    'processed_at' => now(),
                ]);

                $commission->update(['status' => 'paid', 'paid_at' => now()]);

                $payouts[] = $payout;

                $this->notifyPayoutProcessed($payout);
            }
        });

        return $payouts;
    }

    /**
     * Create a vendor withdrawal request.
     */
    public function requestWithdrawal(int $vendorId, float $amount, string $method = 'bank_transfer', ?string $notes = null)
    {
        $pending = $this->vendorPendingPayout($vendorId);

        if ($pending['held_due_to_dispute']) {
            return ['ok' => false, 'message' => 'Withdrawal held: an order is under dispute. Payouts are blocked until the dispute is resolved.'];
        }

        $minThreshold = $pending['min_threshold'];
        if ($amount < $minThreshold) {
            return [
                'ok' => false,
                'message' => "Minimum withdrawal amount is ₨" . number_format($minThreshold, 0) . ". Your request is below this threshold.",
            ];
        }

        if ($amount > $pending['pending_net']) {
            return [
                'ok' => false,
                'message' => 'You cannot withdraw more than your available pending payout (₨' . number_format($pending['pending_net'], 2) . ').',
            ];
        }

        $withdrawal = \App\Models\VendorWithdrawal::create([
            'vendor_id' => $vendorId,
            'amount' => $amount,
            'status' => 'pending',
            'payout_method' => $method,
            'notes' => $notes,
        ]);

        Log::info('Vendor withdrawal requested', [
            'vendor_id' => $vendorId,
            'withdrawal_id' => $withdrawal->id,
            'amount' => $amount,
        ]);

        return ['ok' => true, 'withdrawal' => $withdrawal, 'message' => 'Withdrawal request submitted for admin review.'];
    }

    /**
     * Approve a pending withdrawal: mark it completed and process the payout.
     */
    public function approveWithdrawal(\App\Models\VendorWithdrawal $withdrawal, ?\App\Models\User $admin = null): array
    {
        if ($withdrawal->status !== 'pending') {
            return ['ok' => false, 'message' => 'This withdrawal has already been processed.'];
        }

        $payouts = $this->processVendorPayout($withdrawal->vendor_id, 'on-demand');

        $withdrawal->update([
            'status' => 'completed',
            'processed_at' => now(),
            'reference' => 'WD-' . $withdrawal->id . '-' . substr(md5(uniqid()), 0, 6),
            'notes' => $withdrawal->notes . ' Approved by admin #' . ($admin?->id ?? 'system'),
        ]);

        $total = collect($payouts)->sum('amount');
        return [
            'ok' => true,
            'payouts' => $payouts,
            'total_credited' => $total,
            'message' => "Withdrawal approved. ₨" . number_format($total, 2) . " credited to the vendor's payout account.",
        ];
    }

    /**
     * Reject a withdrawal request.
     */
    public function rejectWithdrawal(\App\Models\VendorWithdrawal $withdrawal, string $reason, ?\App\Models\User $admin = null): array
    {
        if ($withdrawal->status !== 'pending') {
            return ['ok' => false, 'message' => 'This withdrawal has already been processed.'];
        }

        $withdrawal->update([
            'status' => 'rejected',
            'processed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return ['ok' => true, 'message' => 'Withdrawal rejected: ' . $reason];
    }

    /**
     * Record a payout as failed (used for retriable bank failures).
     */
    public function markPayoutFailed(VendorPayout $payout, string $reason): void
    {
        $payout->update([
            'status' => 'failed',
            'notes' => $reason,
        ]);
        $this->notifyPayoutFailed($payout, $reason);
    }

    protected function notifyPayoutProcessed(VendorPayout $payout): void
    {
        $vendorUser = $payout->vendor?->user;
        if ($vendorUser) {
            $vendorUser->notify(new VendorPayoutProcessedNotification($payout));
        }
    }

    protected function notifyPayoutFailed(VendorPayout $payout, string $reason): void
    {
        $vendorUser = $payout->vendor?->user;
        if ($vendorUser) {
            $vendorUser->notify(new VendorPayoutFailedNotification($payout, $reason));
        }
    }

    public function notifyPayoutProcessedPublic(VendorPayout $payout): void
    {
        $this->notifyPayoutProcessed($payout);
    }
}
