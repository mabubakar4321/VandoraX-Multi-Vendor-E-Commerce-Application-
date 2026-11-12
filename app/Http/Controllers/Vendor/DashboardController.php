<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\VendorOrder;
use App\Models\VendorPayout;

class DashboardController extends Controller
{
    public function index()
    {
        $vendor = auth()->user()->vendor;
        abort_unless($vendor, 403, 'Vendor account not found.');

        $orders = VendorOrder::where('vendor_id', $vendor->id);
        $productQuery = Product::where('vendor_id', $vendor->id);

        $stats = [
            'total_sales' => (float) (clone $orders)->whereNotIn('status', ['cancelled'])->sum('subtotal'),
            'deducted_commission' => (float) (clone $orders)->whereNotIn('status', ['cancelled'])->sum('commission_amount'),
            'net_earnings' => (float) VendorPayout::where('vendor_id', $vendor->id)->where('status', 'completed')->sum('amount'),
            'total_orders' => (clone $orders)->count(),
            'pending_orders' => (clone $orders)->where('status', VendorOrder::STATUS_PENDING)->count(),
            'shipping_orders' => (clone $orders)->whereIn('status', [VendorOrder::STATUS_PROCESSING, VendorOrder::STATUS_SHIPPED])->count(),
            'delivered_orders' => (clone $orders)->where('status', VendorOrder::STATUS_DELIVERED)->count(),
            'customers' => (clone $orders)->join('orders', 'orders.id', '=', 'vendor_orders.order_id')->distinct('orders.customer_id')->count('orders.customer_id'),
            'products' => (clone $productQuery)->count(),
            'published_products' => (clone $productQuery)->where('status', 'published')->count(),
        ];

        $payoutStats = [
            'completed' => (float) VendorPayout::where('vendor_id', $vendor->id)->where('status', 'completed')->sum('amount'),
            'pending' => (float) VendorPayout::where('vendor_id', $vendor->id)->where('status', 'pending')->sum('amount'),
            'held' => (float) (clone $orders)->whereNotIn('status', ['cancelled'])
                ->whereHas('order.payment', fn ($query) => $query->where('escrow_status', 'held'))
                ->sum('vendor_amount'),
        ];

        $categories = Category::active()
            ->whereHas('products', fn ($query) => $query->where('vendor_id', $vendor->id))
            ->withCount(['products' => fn ($query) => $query->where('vendor_id', $vendor->id)])
            ->with('parent')
            ->ordered()
            ->get();

        $monthlySales = collect(range(5, 0))->map(function (int $monthsAgo) use ($vendor) {
            $month = now()->subMonths($monthsAgo);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $query = VendorOrder::where('vendor_id', $vendor->id)
                ->whereNotIn('status', ['cancelled'])
                ->whereBetween('created_at', [$start, $end]);

            return [
                'label' => $month->format('M'),
                'sales' => (float) (clone $query)->sum('subtotal'),
                'orders' => (clone $query)->count(),
            ];
        });

        $recentOrders = VendorOrder::where('vendor_id', $vendor->id)
            ->with('order.customer')
            ->latest()
            ->limit(5)
            ->get();

        return view('vendor.dashboard', compact(
            'vendor',
            'stats',
            'payoutStats',
            'categories',
            'monthlySales',
            'recentOrders'
        ));
    }
}
