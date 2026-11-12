<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorPayout;
use App\Models\VendorWithdrawal;
use App\Services\PayoutService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $vendor = auth()->user()->vendor;
        abort_unless($vendor, 403, 'Vendor account not found.');

        $query = VendorPayout::where('vendor_id', $vendor->id)
            ->with(['vendorOrder.order.customer', 'vendorOrder.order.address', 'payment', 'vendorOrder.commissions']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('transaction_id', 'like', "%{$s}%")
                  ->orWhereHas('vendorOrder', fn($vq) => $vq->where('vendor_order_number', 'like', "%{$s}%"))
                  ->orWhereHas('payment', fn($pq) => $pq->where('transaction_id', 'like', "%{$s}%"))
                  ->orWhereHas('vendorOrder.order', fn($oq) => $oq->where('order_number', 'like', "%{$s}%"));
            });
        }

        $payouts = $query->latest()->paginate(15)->withQueryString();

        $heldPayments = \App\Models\VendorOrder::where('vendor_id', $vendor->id)
            ->whereNotIn('status', ['cancelled'])
            ->whereHas('order.payment', fn ($payment) => $payment->where('escrow_status', 'held'))
            ->with(['order.customer', 'order.payment', 'order.address'])
            ->latest()
            ->get();

        $stats = [
            'total' => (float) VendorPayout::where('vendor_id', $vendor->id)->sum('amount'),
            'completed' => (float) VendorPayout::where('vendor_id', $vendor->id)->where('status', 'completed')->sum('amount'),
            'pending' => (float) VendorPayout::where('vendor_id', $vendor->id)->where('status', 'pending')->sum('amount'),
            'held' => (float) $heldPayments->sum('vendor_amount'),
            'commission_total' => (float) VendorPayout::where('vendor_id', $vendor->id)->sum('commission_amount'),
            'count' => VendorPayout::where('vendor_id', $vendor->id)->count(),
        ];

        $vendor->load(['bankDetails']);

        return view('vendor.payments.index', compact('payouts', 'heldPayments', 'stats', 'vendor'));
    }

    public function show(VendorPayout $payout)
    {
        $vendor = auth()->user()->vendor;
        abort_unless($vendor && (int) $payout->vendor_id === (int) $vendor->id, 403);

        $payout->load(['vendorOrder.order.customer', 'vendorOrder.order.address', 'vendorOrder.order.items.product', 'payment', 'vendor']);

        return view('vendor.payments.show', compact('payout'));
    }

    /**
     * Hide a payout from this vendor's history without deleting linked
     * customer payment, escrow, or withdrawal records.
     */
    public function destroy(VendorPayout $payout)
    {
        $vendor = auth()->user()->vendor;
        abort_unless($vendor && (int) $payout->vendor_id === (int) $vendor->id, 403);

        $payout->delete();

        return redirect()
            ->route('vendor.payments.index')
            ->with('success', 'Payment detail deleted from your payment history.');
    }

    /**
     * On-demand withdrawal request (vendor-initiated).
     */
    public function requestWithdrawal(Request $request, PayoutService $payoutService)
    {
        $vendor = auth()->user()->vendor;
        abort_unless($vendor, 403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:999999999.99',
            'payout_method' => 'nullable|in:bank_transfer,check,wallet',
            'notes' => 'nullable|string|max:500',
        ]);

        $result = $payoutService->requestWithdrawal(
            $vendor->id,
            (float) $validated['amount'],
            $validated['payout_method'] ?? 'bank_transfer',
            $validated['notes'] ?? null
        );

        if (!$result['ok']) {
            return back()->with('error', $result['message']);
        }

        return redirect()
            ->route('vendor.payments.withdrawals')
            ->with('success', 'Withdrawal request submitted. Admin will review and process it.');
    }

    /**
     * List this vendor's withdrawal requests.
     */
    public function withdrawals(Request $request)
    {
        $vendor = auth()->user()->vendor;
        abort_unless($vendor, 403);

        $withdrawals = VendorWithdrawal::where('vendor_id', $vendor->id)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $pending = $payoutService = app(PayoutService::class);
        $pendingPayout = $pending->vendorPendingPayout($vendor->id);

        return view('vendor.payments.withdrawals', compact('withdrawals', 'pendingPayout'));
    }
}
