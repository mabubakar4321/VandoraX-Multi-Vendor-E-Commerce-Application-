<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\CommissionExport;
use App\Models\Commission;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CommissionReportController extends Controller
{
    public function index(Request $request, CommissionService $commissionService)
    {
        $query = Commission::with(['vendorOrder.vendor', 'vendorOrder.order', 'product.category'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vendor_id')) {
            $query->whereHas('vendorOrder', fn($q) => $q->where('vendor_id', $request->vendor_id));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->paginate(25)->withQueryString();

        $report = $commissionService->report();

        $vendors = \App\Models\Vendor::with('user')->get(['id', 'store_name', 'user_id']);

        return view('admin.commission-reports.index', compact('commissions', 'report', 'vendors'));
    }

    public function export(Request $request)
    {
        $filters = [
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'vendor_id' => $request->get('vendor_id') ? (int) $request->get('vendor_id') : null,
            'status' => $request->get('status'),
        ];

        return Excel::download(new CommissionExport($filters), 'commission-report-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function adjust(Request $request, Commission $commission)
    {
        $request->validate([
            'new_amount' => 'required|numeric|min:0|max:999999999.99',
            'reason' => 'required|string|max:500',
        ]);

        $oldAmount = (float) $commission->commission_amount;
        $newAmount = (float) $request->new_amount;
        $gross = (float) $commission->order_amount;
        $rate = $gross > 0 ? round(($newAmount / $gross) * 100, 2) : 0;
        $newPayout = round($gross - $newAmount, 2);

        $commission->update([
            'commission_amount' => $newAmount,
            'commission_rate' => $rate,
            'vendor_payout' => $newPayout,
        ]);

        if ($commission->vendorOrder) {
            $commission->vendorOrder->update([
                'commission_amount' => $newAmount,
                'vendor_amount' => $newPayout,
            ]);
        }

        return back()->with('success', "Commission adjusted from ₨" . number_format($oldAmount, 2) . " to ₨" . number_format($newAmount, 2) . ". Reason: {$request->reason}");
    }
}