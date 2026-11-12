<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Models\RefundAppeal;
use Illuminate\Http\Request;

class RefundAppealController extends Controller
{
    public function index()
    {
        $vendorId = auth()->user()->vendor->id;
        $appeals = RefundAppeal::where('vendor_id', $vendorId)
            ->with([
                'customer',
                'returnRequest.photos',
                'order',
                'orderItem.product.images',
                'orderItem.variant',
            ])
            ->latest()
            ->paginate(12);

        return view('vendor.refund-appeals.index', compact('appeals'));
    }

    public function create()
    {
        $vendorId = auth()->user()->vendor->id;
        $returns = ProductReturn::where('vendor_id', $vendorId)
            ->whereIn('status', ['requested', 'approved', 'shipped_back', 'received', 'rejected'])
            ->with(['orderItem.product', 'orderItem.order.customer'])
            ->latest()
            ->get();

        return view('vendor.refund-appeals.create', compact('returns'));
    }

    public function store(Request $request)
    {
        $vendorId = auth()->user()->vendor->id;
        $validated = $request->validate([
            'return_id' => ['required', 'integer', 'exists:returns,id'],
            'requested_amount' => ['required', 'numeric', 'min:0.01'],
            'refund_method' => ['nullable', 'string', 'max:100'],
            'refund_details' => ['nullable', 'string', 'max:2000'],
            'reason' => ['required', 'string', 'max:1000'],
            'seller_explanation' => ['required', 'string', 'min:10', 'max:4000'],
        ]);

        $return = ProductReturn::where('vendor_id', $vendorId)
            ->with(['orderItem.order', 'orderItem.product'])
            ->findOrFail($validated['return_id']);

        if (in_array($return->status, ['refunded'], true)) {
            return back()->withInput()->withErrors(['return_id' => 'A refund has already been processed for this return.']);
        }

        if ((float) $validated['requested_amount'] > (float) $return->orderItem->total_amount) {
            return back()->withInput()->withErrors(['requested_amount' => 'The requested refund cannot exceed the returned item total.']);
        }

        if (RefundAppeal::where('return_id', $return->id)->where('status', 'pending')->exists()) {
            return back()->withInput()->withErrors(['return_id' => 'This return already has a refund request awaiting admin review.']);
        }

        $appeal = RefundAppeal::create([
            'appeal_number' => 'RFA-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)),
            'return_id' => $return->id,
            'order_id' => $return->orderItem->order_id,
            'order_item_id' => $return->orderItem->id,
            'customer_id' => $return->customer_id,
            'vendor_id' => $vendorId,
            'requested_amount' => $validated['requested_amount'],
            'refund_method' => $validated['refund_method'] ?? null,
            'refund_details' => $validated['refund_details'] ?? null,
            'reason' => $validated['reason'],
            'seller_explanation' => $validated['seller_explanation'],
        ]);

        return redirect()->route('vendor.refund-appeals.create')
            ->with('success', 'Refund appeal ' . $appeal->appeal_number . ' was sent to the admin panel.');
    }
}
