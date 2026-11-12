<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;

class AdminCollectionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Commission::with([
            'vendorOrder.vendor.user',
            'vendorOrder.vendor.bankDetails',
            'vendorOrder.order.customer',
            'vendorOrder.order.address',
            'product',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vendor_id')) {
            $query->whereHas('vendorOrder', fn($q) => $q->where('vendor_id', $request->vendor_id));
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('vendorOrder.vendor.user', fn($uq) => $uq->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
                  ->orWhereHas('vendorOrder.vendor', fn($vq) => $vq->where('store_name', 'like', "%{$s}%"))
                  ->orWhereHas('vendorOrder.order', fn($oq) => $oq->where('order_number', 'like', "%{$s}%"))
                  ->orWhereHas('vendorOrder', fn($voq) => $voq->where('vendor_order_number', 'like', "%{$s}%"))
                  ->orWhereHas('vendorOrder.order.customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            });
        }

        $collections = $query->paginate(20)->withQueryString();

        $stats = [
            'total_collected' => (float) Commission::whereIn('status', ['released','paid'])->sum('commission_amount'),
            'total_orders' => Commission::count(),
            'released_collections' => Commission::where('status', 'released')->count(),
            'pending_collections' => Commission::where('status', 'pending')->count(),
            'average_rate' => Commission::avg('commission_rate') ?? 0,
        ];

        // For vendor filter dropdown
        $vendors = \App\Models\Vendor::with('user')->get(['id','store_name','user_id']);

        return view('admin.admin-collections.index', compact('collections', 'stats', 'vendors'));
    }
}
