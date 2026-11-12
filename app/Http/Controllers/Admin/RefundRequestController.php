<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefundAppeal;
use Illuminate\Http\Request;

class RefundRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = RefundAppeal::with(['customer', 'vendor', 'returnRequest', 'orderItem.product', 'order'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $refundRequests = $query->paginate(20)->withQueryString();
        return view('admin.refund-requests.index', compact('refundRequests'));
    }

    public function show(RefundAppeal $refundAppeal)
    {
        $refundAppeal->load([
            'customer',
            'vendor.user',
            'order.payment',
            'order.address',
            'orderItem.product.images',
            'orderItem.variant',
            'returnRequest.photos',
            'returnRequest.statusHistory',
        ]);

        return view('admin.refund-requests.show', compact('refundAppeal'));
    }
}
