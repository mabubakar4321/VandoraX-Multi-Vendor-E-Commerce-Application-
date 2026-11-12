<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductReturn;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = auth()->user()->vendor->id;
        $query = ProductReturn::where('vendor_id', $vendorId)
            ->with(['orderItem.order.customer', 'orderItem.product', 'photos']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $returns = $query->latest()->paginate(15)->withQueryString();
        return view('vendor.returns.index', compact('returns'));
    }

    public function show(ProductReturn $return)
    {
        $this->owned($return);
        $return->load([
            'orderItem.order.customer',
            'orderItem.order.address',
            'orderItem.order.payment.escrowAccount',
            'orderItem.vendorOrder',
            'orderItem.order.items.product.images',
            'orderItem.order.items.variant',
            'orderItem.product.images',
            'orderItem.product.category',
            'orderItem.product.subcategory',
            'orderItem.product.specifications',
            'orderItem.variant',
            'returnItems',
            'photos',
            'statusHistory',
            'refundRecords',
        ]);
        return view('vendor.returns.show', compact('return'));
    }

    public function approve(Request $request, ProductReturn $return, ReturnService $service)
    {
        $this->owned($return);
        $validated = $request->validate([
            'return_shipping_address' => 'required|string|max:1000',
            'return_instructions' => 'nullable|string|max:2000',
            'return_label_url' => 'nullable|url|max:255',
        ]);
        try {
            $service->approve($return, auth()->user()->vendor->id, $validated);
            return back()->with('success', 'Return approved and shipping instructions sent to the customer.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, ProductReturn $return, ReturnService $service)
    {
        $this->owned($return);
        $validated = $request->validate(['rejection_reason' => 'required|string|min:10|max:2000']);
        try {
            $service->reject($return, auth()->user()->vendor->id, $validated['rejection_reason']);
            return back()->with('success', 'Return rejected with a reason.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receive(Request $request, ProductReturn $return, ReturnService $service)
    {
        $this->owned($return);
        try {
            $service->receive($return, auth()->user()->vendor->id, $request->input('inspection_notes'));
            return back()->with('success', 'Return marked as received. You can now approve the refund.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function refund(ProductReturn $return, ReturnService $service)
    {
        $this->owned($return);
        try {
            $service->refund($return);
            return back()->with('success', 'Refund processed for this returned item.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function owned(ProductReturn $return): void
    {
        abort_unless($return->vendor_id === auth()->user()->vendor->id, 403);
    }
}
