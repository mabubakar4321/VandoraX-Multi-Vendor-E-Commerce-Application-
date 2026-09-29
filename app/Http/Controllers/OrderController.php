<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show all orders for the customer
     */
    public function index()
    {
        $orders = Order::where('customer_id', auth()->id())
                       ->with(['items.product', 'address'])
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

        $order->load(['items.product.vendor.user', 'items.product.images', 'items.variant', 'address']);

        $itemsByVendor = $order->items()
                              ->with(['product.vendor.user', 'product.images', 'variant'])
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

        $order->load(['items.product.vendor.user', 'items.product.images', 'items.variant', 'address', 'vendorOrders']);

        $itemsByVendor = $order->items()
                              ->with(['product.vendor.user', 'product.images', 'variant'])
                              ->get()
                              ->groupBy('product.vendor_id');

        return view('orders.show', compact('order', 'itemsByVendor'));
    }
}
