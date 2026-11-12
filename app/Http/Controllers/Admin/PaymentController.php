<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\EscrowAccount;
use App\Models\Payment;
use App\Models\PaymentRefund;
use App\Models\VendorPayout;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['order.customer', 'order.vendorOrders.vendor'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('gateway')) {
            $query->where('payment_gateway', $request->gateway);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('transaction_id', 'like', "%{$s}%")
                  ->orWhere('id', $s)
                  ->orWhereHas('order', fn($oq) => $oq->where('order_number', 'like', "%{$s}%")->orWhere('customer_id', $s));
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'refunded' => Payment::where('status', 'refunded')->count(),
            'held_escrow' => Payment::where('escrow_status', 'held')->count(),
            'released_escrow' => Payment::where('escrow_status', 'released')->count(),
            'revenue' => (float) Payment::where('status', 'completed')->sum('amount'),
            'pending_amount' => (float) Payment::where('status', 'pending')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.customer', 'order.address', 'order.items.product.vendor', 'order.vendorOrders.vendor', 'order.vendorOrders.commissions', 'escrowAccount', 'refunds', 'order.vendorOrders.vendor.bankDetails']);
        // Pre-calc per-vendor breakdown for Release Amount modal
        $vendorBreakdown = $payment->order?->vendorOrders ?? collect();
        return view('admin.payments.show', compact('payment', 'vendorBreakdown'));
    }
    public function refund(Request $request, Payment $payment, StripePaymentService $stripe)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01', 'reason' => 'required|string|max:500']);
        if ($payment->status !== 'completed') {
            return back()->with('error', 'The Stripe payment must be completed before it can be refunded.');
        }
        // Prevent refund after already released
        if ($payment->escrow_status === 'released') {
            return back()->with('error', 'Cannot refund: funds already released to vendor(s).');
        }
        try {
                if ($payment->payment_gateway === 'stripe') {
                    $amount = (float) $request->input('amount');
                    $stripe->refundAmount($payment, $amount, $request->input('reason', 'Refunded by admin'));
            } else {
                DB::transaction(function () use ($payment, $request) {
                        $amount = (float) $request->input('amount');
                        $remaining = round((float) $payment->amount - (float) $payment->refunded_amount, 2);
                        if ($amount > $remaining) {
                            throw new \RuntimeException('The refund amount exceeds the remaining payment balance.');
                        }
                        $refundedTotal = round((float) $payment->refunded_amount + $amount, 2);
                        $fullyRefunded = $refundedTotal >= (float) $payment->amount;
                        PaymentRefund::create([
                            'payment_id' => $payment->id,
                            'transaction_id' => 'refund_' . $payment->id . '_' . bin2hex(random_bytes(6)),
                            'amount' => $amount,
                            'currency' => $payment->currency,
                            'status' => 'succeeded',
                            'reason' => $request->input('reason'),
                            'processed_by' => auth()->id(),
                        ]);
                        $payment->update([
                            'status' => $fullyRefunded ? 'refunded' : 'completed',
                            'escrow_status' => $fullyRefunded ? 'refunded' : 'held',
                            'refunded_amount' => $refundedTotal,
                            'refunded_at' => $fullyRefunded ? now() : $payment->refunded_at,
                            'refund_reason' => $request->input('reason'),
                        ]);
                        $payment->order->update(['payment_status' => $fullyRefunded ? 'refunded' : 'completed']);
                        EscrowAccount::where('payment_id', $payment->id)->update([
                            'status' => $fullyRefunded ? 'refunded' : 'held',
                            'refunded_amount' => $refundedTotal,
                            'remaining_amount' => max(0, round((float) $payment->amount - $refundedTotal, 2)),
                            'refunded_at' => $fullyRefunded ? now() : null,
                        ]);
                    });
            }
            return back()->with('success', 'Payment refunded successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }

    /**
     * Release Amount: deducts admin commission per vendor_order, creates VendorPayout + Commission records,
     * releases escrow, and updates order payment_status so customer sees Confirmed.
     * Delegates to the shared PayoutService so delivery-triggered release and manual release behave identically.
     * Duplicate-safe: unique (payment_id, vendor_id, vendor_order_id) + idempotency check.
     */
    public function releaseEscrow(Request $request, Payment $payment)
    {
        if ($payment->escrow_status !== 'held') {
            return back()->with('error', 'Only held escrow payments can be released. Current escrow: ' . ($payment->escrow_status ?? 'none'));
        }
        if ((float) $payment->refunded_amount > 0) {
            return back()->with('error', 'This payment has a partial refund. Do not release the remaining escrow until the refund and vendor settlement are reconciled.');
        }
        // COD has no payment record — not releaseable. Stripe pending means Stripe did not confirm yet.
        // Allow admin to force-complete pending Stripe payments on release (common when webhook was missed / test mode).
        $wasPending = $payment->status === 'pending' && $payment->payment_gateway === 'stripe';
        // Allow admins always (withdrawal_requires_approval is false per your request, but keep guard for future)
        if (config('escrow.restrictions.withdrawal_requires_approval') && !auth()->user()?->hasRole('admin')) {
            return back()->with('error', 'Withdrawal requires admin approval.');
        }

        try {
            if ($wasPending) {
                $payment->update(['status' => 'completed', 'paid_at' => $payment->paid_at ?? now()]);
            }

            $result = app(\App\Services\PayoutService::class)->releaseOrderEscrow($payment->order, 'manual_admin_release');

            if (!empty($result['released'])) {
                Log::info('Payment released to vendors', ['payment_id' => $payment->id, 'by' => auth()->id()]);
                return back()->with('success', 'Amount released successfully: vendor(s) credited after commission deduction. Payment now shows Confirmed for customer.');
            }

            if (($result['reason'] ?? null) === 'no_escrow') {
                return back()->with('error', 'No escrow record exists for this payment.');
            }

            return back()->with('error', 'This payment has already been released. Duplicate release prevented.');
        } catch (\Throwable $e) {
            Log::error('Release escrow failed', ['payment_id' => $payment->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Release failed: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Payment::with(['order.customer'])->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('gateway')) $query->where('payment_gateway', $request->gateway);
        $payments = $query->get();
        $csv = "ID,Order,Customer,Gateway,Transaction ID,Status,Escrow,Amount,Currency,Created At\n";
        foreach ($payments as $p) {
            $csv .= implode(',', [
                $p->id,
                $p->order->order_number ?? '',
                '"' . str_replace('"', '""', $p->order->customer->name ?? '') . '"',
                $p->payment_gateway,
                $p->transaction_id,
                $p->status,
                $p->escrow_status,
                $p->amount,
                $p->currency,
                $p->created_at->toDateTimeString(),
            ]) . "\n";
        }
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
