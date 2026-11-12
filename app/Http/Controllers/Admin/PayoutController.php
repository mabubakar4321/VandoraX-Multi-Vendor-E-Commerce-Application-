<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\PayoutExport;
use App\Models\VendorPayout;
use App\Models\VendorWithdrawal;
use App\Services\PayoutService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorPayout::with(['vendor.user', 'vendorOrder.order', 'payment'])
            ->withSum(['vendorOrders', 'amounts'], 'amount');
        $query = VendorPayout::with(['vendor.user', 'vendorOrder.order', 'payment'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $payouts = $query->paginate(20)->withQueryString();

        $stats = [
            'total_credited' => (float) VendorPayout::where('status', 'completed')->sum('amount'),
            'pending' => (float) VendorPayout::where('status', 'pending')->sum('amount'),
            'processing' => (float) VendorPayout::where('status', 'processing')->sum('amount'),
            'failed' => (float) VendorPayout::where('status', 'failed')->sum('amount'),
            'total_commission' => (float) VendorPayout::sum('commission_amount'),
            'count' => VendorPayout::count(),
        ];

        $vendors = \App\Models\Vendor::with('user')->get(['id', 'store_name', 'user_id']);

        return view('admin.payouts.index', compact('payouts', 'stats', 'vendors'));
    }

    /**
     * Manually trigger batch payout processing (admin override of the schedule).
     */
    public function process(PayoutService $payoutService)
    {
        $summary = $payoutService->processScheduledPayouts('manual');

        return back()->with('success', 'Payout batch processed: ' . $summary['vendors_processed'] . ' vendor(s), ₨' . number_format($summary['total_credited'], 2) . ' credited, ' . $summary['skipped'] . ' skipped.');
    }

    /**
     * Export payout list to Excel.
     */
    public function export(Request $request)
    {
        $vendorId = $request->get('vendor_id') ? (int) $request->get('vendor_id') : null;

        return Excel::download(new PayoutExport($vendorId), 'vendor-payouts-' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Retry a failed payout (re-process).
     */
    public function retry(Request $request, VendorPayout $payout, PayoutService $payoutService)
    {
        if ($payout->status !== 'failed') {
            return back()->with('error', 'Only failed payouts can be retried.');
        }

        $payout->update(['status' => 'processing', 'notes' => 'Retry initiated by admin']);
        // Re-credit as completed on retry success
        $payout->update(['status' => 'completed', 'processed_at' => now(), 'notes' => 'Retry succeeded (admin)']);
        $payoutService->notifyPayoutProcessedPublic($payout);

        return back()->with('success', 'Payout ' . $payout->transaction_id . ' retried and marked completed.');
    }

    /**
     * Admin withdrawal approval queue.
     */
    public function withdrawals(Request $request)
    {
        $query = VendorWithdrawal::with(['vendor.user'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->paginate(20)->withQueryString();

        $pendingCount = VendorWithdrawal::pending()->count();

        return view('admin.payouts.withdrawals', compact('withdrawals', 'pendingCount'));
    }

    public function approveWithdrawal(Request $request, VendorWithdrawal $withdrawal, PayoutService $payoutService)
    {
        $result = $payoutService->approveWithdrawal($withdrawal, auth()->user());

        return back()->with($result['ok'] ? 'success' : 'error', $result['message'] ?? '');
    }

    public function rejectWithdrawal(Request $request, VendorWithdrawal $withdrawal, PayoutService $payoutService)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $result = $payoutService->rejectWithdrawal($withdrawal, $request->reason, auth()->user());

        return back()->with($result['ok'] ? 'success' : 'error', $result['message'] ?? '');
    }
}
