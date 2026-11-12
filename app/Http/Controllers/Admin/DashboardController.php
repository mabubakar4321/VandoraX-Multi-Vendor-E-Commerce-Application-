<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\Payment;
use App\Models\VendorOrder;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics and charts.
     */
    public function index()
    {
        // ── Totals ──
        $totalProducts   = Product::count();
        $totalCategories = Category::count();
        $totalUsers      = User::role('customer')->count();
        $totalVendors    = Vendor::count();
        $activeVendors   = Vendor::active()->count();
        $pendingVendors  = Vendor::where('status', 'pending')->count();
        $blockedVendors  = Vendor::where('status', 'blocked')->count();

        // ── Money ──
        $totalRevenue    = (float) Payment::where('status', 'completed')->sum('amount');
        $pendingRevenue  = (float) Payment::where('status', 'pending')->sum('amount');
        $totalCommission = (float) VendorOrder::sum('commission_amount');

        // ── Orders ──
        $totalOrders     = Order::count();
        $pendingOrders   = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        $todayOrders     = Order::whereDate('created_at', today())->count();
        $todayRevenue    = (float) Payment::where('status', 'completed')
            ->whereDate('paid_at', today())
            ->sum('amount');

        // ── Payments ──
        $pendingPayments = Payment::where('status', 'pending')->count();
        $completedPayments = Payment::where('status', 'completed')->count();
        $failedPayments  = Payment::where('status', 'failed')->count();
        $refundedPayments = Payment::where('status', 'refunded')->count();

        // ── Chart 1: Revenue + Orders last 7 days ──
        $last7Days = collect(range(6, 0))->map(function ($i) {
            return now()->subDays($i)->toDateString();
        });

        $revenueByDay = Payment::where('status', 'completed')
            ->whereDate('paid_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(paid_at) as day, SUM(amount) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->map(fn ($v) => (float) $v);

        $ordersByDay = Order::whereDate('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->map(fn ($v) => (int) $v);

        $revenueLabels = $last7Days->map(fn ($d) => \Carbon\Carbon::parse($d)->format('M d'))->values();
        $revenueData   = $last7Days->map(fn ($d) => $revenueByDay->get($d, 0))->values();
        $ordersData    = $last7Days->map(fn ($d) => $ordersByDay->get($d, 0))->values();

        // ── Chart 2: Orders by status (doughnut) ──
        $orderStatusData = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->map(fn ($v) => (int) $v);

        // ── Chart 3: Products by category (bar) ──
        $productsByCategory = Product::selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->with('category')
            ->pluck('count', 'category_id')
            ->map(fn ($v) => (int) $v);

        $categoryNames = \App\Models\Category::whereIn('id', $productsByCategory->keys())
            ->pluck('name', 'id');

        // ── Chart 4: Vendors by status (doughnut) ──
        $vendorStatusData = Vendor::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->map(fn ($v) => (int) $v);

        // ── Chart 5: Top products by sales (bar) ──
        $topProducts = Product::orderBy('created_at', 'desc')->take(5)->get(['name']);

        // ── Recent orders ──
        $recentOrders = Order::with(['customer', 'payment'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'totalCategories', 'totalUsers', 'totalVendors', 'activeVendors',
            'pendingVendors', 'blockedVendors',
            'totalRevenue', 'pendingRevenue', 'totalCommission',
            'totalOrders', 'pendingOrders', 'completedOrders', 'cancelledOrders', 'todayOrders', 'todayRevenue',
            'pendingPayments', 'completedPayments', 'failedPayments', 'refundedPayments',
            'revenueLabels', 'revenueData', 'ordersData',
            'orderStatusData', 'productsByCategory', 'categoryNames',
            'vendorStatusData', 'topProducts', 'recentOrders'
        ));
    }
}