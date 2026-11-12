<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Show all orders for the customer
     */
    public function index()
    {
        $orders = Order::where('customer_id', auth()->id())
                       ->with(['items.product', 'address', 'vendorOrders'])
                       ->orderBy('created_at', 'desc')
                       ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show order confirmation page after checkout
     */
    public function confirmation(Order $order)
    {
        // Verify order belongs to current user
        if ($order->customer_id !== auth()->id()) {
            return redirect()->route('order.index')->with('error', 'Order not found.');
        }

        $order->load(['items.product.vendor.user', 'items.product.images', 'items.variant', 'address', 'payment', 'vendorOrders.vendor']);

        $itemsByVendor = $order->items()
                              ->with(['product.vendor.user', 'product.images', 'variant', 'vendorOrder'])
                              ->get()
                              ->groupBy('product.vendor_id');

        return view('orders.confirmation', compact('order', 'itemsByVendor'));
    }

    /**
     * Show detailed order information
     */
    public function show(Order $order)
    {
        // Verify order belongs to current user
        if ($order->customer_id !== auth()->id()) {
            return redirect()->route('order.index')->with('error', 'Order not found.');
        }

        $order->load([
            'items.product.vendor.user',
            'items.product.images',
            'items.variant',
            'items.vendorOrder',
            'items.returns',
            'address',
            'vendorOrders',
            'payment.refunds',
            'statusHistory',
        ]);

        $itemsByVendor = $order->items()
                              ->with(['product.vendor.user', 'product.images', 'variant', 'vendorOrder'])
                              ->get()
                              ->groupBy('product.vendor_id');

        return view('orders.show', compact('order', 'itemsByVendor'));
    }

    /**
     * Cancel an entire order before any vendor confirms.
     */
    public function cancel(Order $order, Request $request)
    {
        if ($order->customer_id !== auth()->id()) {
            return back()->with('error', 'Order not found.');
        }

        if (!$order->canCancel()) {
            return back()->with('error', 'This order can no longer be cancelled — a vendor has already confirmed it.');
        }

        $reason = $request->input('reason', 'Customer requested cancellation');

        try {
            app(OrderService::class)->cancelOrder($order, auth()->user(), $reason);

            return back()->with('success', 'Your order has been cancelled. Any payment will be refunded to your original source.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel individual items where the vendor has not yet confirmed.
     */
    public function cancelItems(Order $order, Request $request)
    {
        if ($order->customer_id !== auth()->id()) {
            return back()->with('error', 'Order not found.');
        }

        $validated = $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'integer|exists:order_items,id',
        ]);

        try {
            app(OrderService::class)->cancelItems(
                $order,
                $validated['item_ids'],
                auth()->user()
            );

            return back()->with('success', 'Selected items have been cancelled.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show edit page for pending orders
     */
    public function edit(Order $order)
    {
        // Verify order belongs to current user
        if ($order->customer_id !== auth()->id()) {
            return redirect()->route('order.index')->with('error', 'Order not found.');
        }

        // Only allow editing pending orders
        if ($order->status !== 'pending') {
            return redirect()->route('order.index')->with('error', 'Only pending orders can be edited.');
        }

        $order->load(['items.product', 'address', 'payment']);

        return view('orders.edit', compact('order'));
    }

    /**
     * Update a pending order.
     *
     * Uses a database transaction to ensure all changes (items and address)
     * succeed together, or are all rolled back. Payment method is immutable.
     */
    public function update(Request $request, Order $order)
    {
        // Verify order belongs to current user
        if ($order->customer_id !== auth()->id()) {
            return redirect()->route('order.index')->with('error', 'Order not found.');
        }

        // Only allow updating pending orders
        if ($order->status !== 'pending') {
            return redirect()->route('order.index')->with('error', 'Only pending orders can be edited.');
        }

        $rules = [
            'quantity.*' => 'required|integer|min:1',
            'address_full_name' => 'required|string|max:255',
            'address_phone' => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'address_city' => 'required|string|max:100',
            'address_state_province' => 'required|string|max:100',
            'address_postal_code' => 'required|string|max:20',
            'address_country' => 'required|string|max:100',
            'address_delivery_instructions' => 'nullable|string|max:1000',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            DB::beginTransaction();

            // 1. Update item quantities and recalculate totals
            $totalAmount = 0;
            foreach ($request->get('quantity', []) as $itemId => $quantity) {
                $item = $order->items()->findOrFail($itemId);
                $item->quantity = $quantity;
                $item->total_amount = $item->price_at_purchase * $quantity;
                $item->save();
                $totalAmount += $item->total_amount;
            }

            // 2. Recalculate order total
            $order->total_amount = $totalAmount;
            $order->save();

            // 3. Update vendor orders subtotals and amounts
            foreach ($order->vendorOrders as $vendorOrder) {
                $vendorItems = $order->items()->whereHas('product', function($q) use ($vendorOrder) {
                    $q->where('vendor_id', $vendorOrder->vendor_id);
                })->get();

                $vendorSubtotal = $vendorItems->sum('total_amount');
                $vendorCommission = $vendorItems->sum(function($item) {
                    $rate = $item->product->category->commission_rate ?? 15;
                    return $item->total_amount * $rate / 100;
                });

                $vendorOrder->update([
                    'subtotal' => $vendorSubtotal,
                    'commission_amount' => $vendorCommission,
                    'vendor_amount' => $vendorSubtotal - $vendorCommission,
                ]);
            }

            // 4. Update delivery address
            if ($order->address) {
                $order->address->update([
                    'full_name' => $validated['address_full_name'],
                    'phone_number' => $validated['address_phone'],
                    'address_line_1' => $validated['address_line_1'],
                    'address_line_2' => $validated['address_line_2'],
                    'city' => $validated['address_city'],
                    'province' => $validated['address_state_province'],
                    'postal_code' => $validated['address_postal_code'],
                    'country' => $validated['address_country'],
                    'delivery_instructions' => $validated['address_delivery_instructions'],
                ]);
            }

            // 5. Keep the existing payment method and payment record unchanged.
            if ($order->payment) {
                $order->payment->update(['amount' => $order->total_amount]);
            }

            $order->save();

            DB::commit();

            return redirect()->route('order.index')->with('success', 'Order updated successfully. All changes saved.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order update failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update order: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete a pending order
     */
    public function destroy(Order $order)
    {
        // Verify order belongs to current user
        if ($order->customer_id !== auth()->id()) {
            return redirect()->route('order.index')->with('error', 'Order not found.');
        }

        // Only allow deleting pending orders
        if ($order->status !== 'pending') {
            return redirect()->route('order.index')->with('error', 'Only pending orders can be deleted.');
        }

        try {
            // Delete order items first
            $order->items()->delete();

            // Delete order
            $order->delete();

            return redirect()->route('order.index')->with('success', 'Order deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('order.index')->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }
}
