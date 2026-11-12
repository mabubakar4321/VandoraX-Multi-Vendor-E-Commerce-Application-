<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\ProductReturn;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductReturn::with(['orderItem.order.customer', 'orderItem.product', 'vendor', 'refundRecords']);
        foreach (['status', 'vendor_id', 'customer_id'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->input($filter));
            }
        }
        $returns = $query->latest()->paginate(20)->withQueryString();
        return view('admin.returns.index', compact('returns'));
    }

    public function show(ProductReturn $return)
    {
        $return->load([
            'orderItem.order.customer', 'orderItem.product.images', 'orderItem.variant',
            'vendor.user', 'photos', 'statusHistory.changer', 'refundRecords', 'orderItem.order.payment',
        ]);
        $dispute = Dispute::where('return_id', $return->id)->latest()->first();
        return view('admin.returns.show', compact('return', 'dispute'));
    }

    public function forceRefund(ProductReturn $return, ReturnService $service)
    {
        try {
            $service->refund($return, auth()->id(), 'Admin forced refund after dispute review.');
            Dispute::where('return_id', $return->id)->whereIn('status', ['open', 'under_review'])
                ->update(['status' => 'resolved', 'admin_decision' => 'refund_customer', 'admin_decided_by' => auth()->id(), 'decided_at' => now()]);
            $return->load(['customer', 'vendor.user']);
            $return->customer?->notify(new \App\Notifications\ReturnUpdatedNotification($return, 'Admin approved a refund for your appealed return.'));
            $return->vendor?->user?->notify(new \App\Notifications\ReturnUpdatedNotification($return, 'Admin approved a refund for return ' . $return->return_number . '.'));
            return back()->with('success', 'Refund forced and dispute resolved in the customer’s favor.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function uphold(Request $request, ProductReturn $return)
    {
        $validated = $request->validate(['admin_notes' => 'required|string|min:10|max:3000']);
        Dispute::where('return_id', $return->id)->whereIn('status', ['open', 'under_review'])
            ->update(['status' => 'resolved', 'admin_decision' => 'reject_claim', 'admin_notes' => $validated['admin_notes'], 'admin_decided_by' => auth()->id(), 'decided_at' => now()]);
        $return->update(['admin_decided_by' => auth()->id(), 'admin_decided_at' => now(), 'admin_decision_notes' => $validated['admin_notes']]);
        $return->load(['customer', 'vendor.user']);
        $return->customer?->notify(new \App\Notifications\ReturnUpdatedNotification($return, 'Admin upheld the vendor rejection for return ' . $return->return_number . '.'));
        $return->vendor?->user?->notify(new \App\Notifications\ReturnUpdatedNotification($return, 'Admin upheld your rejection for return ' . $return->return_number . '.'));
        return back()->with('success', 'Rejection upheld and dispute closed.');
    }

    public function requestInfo(Request $request, ProductReturn $return)
    {
        $validated = $request->validate(['admin_notes' => 'required|string|min:10|max:3000']);
        Dispute::where('return_id', $return->id)->whereIn('status', ['open', 'under_review'])
            ->update(['status' => 'under_review', 'admin_decision' => 'request_more_info', 'admin_notes' => $validated['admin_notes']]);
        $return->update(['admin_decision_notes' => $validated['admin_notes']]);
        $return->setStatus('vendor_review', $validated['admin_notes']);
        $return->load(['customer', 'vendor.user']);
        $return->customer?->notify(new \App\Notifications\ReturnUpdatedNotification($return, 'Admin requested more information for return ' . $return->return_number . '.'));
        $return->vendor?->user?->notify(new \App\Notifications\ReturnUpdatedNotification($return, 'Admin requested more information for return ' . $return->return_number . '.'));
        return back()->with('success', 'More information requested from the customer and vendor.');
    }
}
