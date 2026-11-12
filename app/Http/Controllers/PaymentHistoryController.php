<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::whereHas('order', fn ($q) => $q->where('customer_id', auth()->id()))
            ->with(['order.vendorOrders.vendor', 'order.items'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('transaction_id', 'like', "%{$s}%")
                  ->orWhereHas('order', fn($oq) => $oq->where('order_number', 'like', "%{$s}%"));
            });
        }

        $payments = $query->paginate(15)->withQueryString();
        return view('payments.history', compact('payments'));
    }
}
