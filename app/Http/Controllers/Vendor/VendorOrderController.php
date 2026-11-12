<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorOrderController extends Controller
{
    public function index(Request $request)
    {
        $vendor = auth()->user()->vendor;

        $query = VendorOrder::where('vendor_id', $vendor->id)
            ->with(['order.customer', 'order.address', 'order.payment']);

        // Filter by status (new, pending, confirmed, processing, shipped, delivered, cancelled)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = VendorOrder::where('vendor_id', $vendor->id)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('vendor.orders.index', compact('orders', 'counts'));
    }

    public function show(VendorOrder $vendorOrder)
    {
        $this->ensureOwnedByCurrentVendor($vendorOrder);

        $vendorOrder->load([
            'order.customer',
            'order.address',
            'order.payment',
            'order.statusHistory.changer',
            'items.product.images',
            'items.variant',
        ]);

        $items = $vendorOrder->items->isEmpty()
            ? $vendorOrder->order->items()
                ->whereHas('product', fn ($query) => $query->where('vendor_id', $vendorOrder->vendor_id))
                ->with(['product.images', 'variant'])
                ->get()
            : $vendorOrder->items;

        return view('vendor.orders.show', [
            'vendorOrder' => $vendorOrder,
            'order' => $vendorOrder->order,
            'items' => $items,
        ]);
    }

    /**
     * Update vendor sub-order status with full transition validation.
     */
    public function updateStatus(Request $request, VendorOrder $vendorOrder)
    {
        $this->ensureOwnedByCurrentVendor($vendorOrder);

        $validated = $request->validate([
            'status' => 'required|in:confirmed,processing,shipped,delivered,cancelled',
            'tracking_number' => 'required_if:status,shipped|nullable|string|max:255',
            'tracking_url' => 'required_if:status,shipped|nullable|url|max:255',
            'reason' => 'nullable|string|max:1000',
        ]);

        if (!$vendorOrder->canTransitionTo($validated['status'])) {
            return back()->with('error', "Cannot move this sub-order from '{$vendorOrder->status}' to '{$validated['status']}'.");
        }

        try {
            DB::transaction(function () use ($vendorOrder, $validated) {
                match ($validated['status']) {
                    'confirmed' => $vendorOrder->confirm(),
                    'processing' => $vendorOrder->markAsProcessing(),
                    'shipped' => $vendorOrder->markAsShipped($validated['tracking_number'] ?? null, $validated['tracking_url'] ?? null),
                    'delivered' => $vendorOrder->markAsDelivered(),
                    'cancelled' => $vendorOrder->cancel($validated['reason'] ?? 'Vendor rejected the order'),
                    default => null,
                };

            });

            return redirect()
                ->route('vendor.orders.show', $vendorOrder)
                ->with('success', 'Sub-order status updated to ' . $validated['status'] . '.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function verifyPayment(VendorOrder $vendorOrder)
    {
        $this->ensureOwnedByCurrentVendor($vendorOrder);

        $payment = $vendorOrder->order()->with('payment')->first()->payment;

        if (!$payment) {
            return back()->with('error', 'This order does not have a payment record to verify.');
        }

        if ($payment->status === 'completed') {
            return back()->with('success', 'This payment is already verified.');
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);

            $payment->order()->update([
                'payment_status' => 'completed',
            ]);
        });

        return back()->with('success', 'Payment marked as verified successfully.');
    }

    /**
     * Printable vendor order invoice/receipt.
     */
    public function print(VendorOrder $vendorOrder)
    {
        $this->ensureOwnedByCurrentVendor($vendorOrder);

        $vendorOrder->load([
            'order.customer',
            'order.address',
            'order.payment',
            'items.product',
            'items.variant',
        ]);

        return view('vendor.orders.print', [
            'vendorOrder' => $vendorOrder,
            'order' => $vendorOrder->order,
            'items' => $vendorOrder->items,
        ]);
    }

    private function ensureOwnedByCurrentVendor(VendorOrder $vendorOrder): void
    {
        abort_unless(
            auth()->user()->vendor && auth()->user()->vendor->id === $vendorOrder->vendor_id,
            403
        );
    }
}
