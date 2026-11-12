<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\VendorOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * List all orders with optional search by order number, customer name/email, or vendor store name.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'payment', 'vendorOrders.vendor']);

        // Search by order number
        if ($request->filled('search')) {
            $term = trim((string) $request->search);
            $request->merge(['search' => $term]);

            if ($term === '') {
                $term = null;
            }

            if ($term !== null) {
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                  ->orWhereHas('customer', fn ($cq) => $cq
                      ->where('name', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%"))
                  ->orWhereHas('vendorOrders.vendor', fn ($vq) => $vq
                      ->where('store_name', 'like', "%{$term}%"));
            });
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $counts = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load([
            'customer',
            'address',
            'payment',
            'vendorOrders.vendor.user',
            'vendorOrders.vendor.category',
            'items.product.category',
            'items.product.vendor.user',
            'items.product.vendor.category',
            'items.product.images',
            'items.variant',
            'items.vendorOrder',
            'statusHistory',
            'items.product' => fn ($query) => $query->withTrashed(),
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Cancel an order on behalf of a customer (admin override).
     */
    public function cancel(Order $order)
    {
        $service = app(\App\Services\OrderService::class);

        try {
            $service->cancelOrder($order, auth()->user(), 'Cancelled by admin');
            return redirect()->route('admin.orders.show', $order)->with('success', 'Order cancelled.');
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.orders.show', $order)->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
            $validated = $request->validate([
                'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            ]);

            if ($order->status === $validated['status']) {
                return back()->with('info', 'The order is already ' . $validated['status'] . '.');
            }

            DB::transaction(function () use ($order, $validated) {
                $oldStatus = $order->status;
                $newStatus = $validated['status'];

                $order->update(['status' => $newStatus]);

                foreach ($order->vendorOrders as $vendorOrder) {
                    $milestones = [
                        'confirmed_at' => null,
                        'shipped_at' => null,
                        'delivered_at' => null,
                    ];

                    if (in_array($newStatus, [
                        VendorOrder::STATUS_CONFIRMED,
                        VendorOrder::STATUS_PROCESSING,
                        VendorOrder::STATUS_SHIPPED,
                        VendorOrder::STATUS_DELIVERED,
                    ], true)) {
                        $milestones['confirmed_at'] = $vendorOrder->confirmed_at ?: now();
                    }

                    if (in_array($newStatus, [
                        VendorOrder::STATUS_SHIPPED,
                        VendorOrder::STATUS_DELIVERED,
                    ], true)) {
                        $milestones['shipped_at'] = $vendorOrder->shipped_at ?: now();
                    }

                    if ($newStatus === VendorOrder::STATUS_DELIVERED) {
                        $milestones['delivered_at'] = $vendorOrder->delivered_at ?: now();
                    }

                    $vendorOrder->update(array_merge(['status' => $newStatus], $milestones));
                }

                OrderStatusHistory::record(
                    $order,
                    $newStatus,
                    "Order status changed from {$oldStatus} to {$newStatus} by admin.",
                    auth()->user(),
                    $oldStatus
                );
            });

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Order status updated to ' . $validated['status'] . '. Customer and vendor order statuses were synchronized.');
    }

    /**
     * Return stock for an order (admin override when a vendor auto-cancelled).
     */
    public function returnStock(Order $order)
    {
        $service = app(\App\Services\OrderService::class);
        $service->restoreStockPublic($order);
        return redirect()->route('admin.orders.show', $order)->with('success', 'Stock returned to inventory.');
    }

    public function destroy(Order $order)
    {
        if ($order->payment?->payment_slip_path && Storage::disk('public')->exists($order->payment->payment_slip_path)) {
            Storage::disk('public')->delete($order->payment->payment_slip_path);
        }

        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}
