<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductReturn;
use App\Services\ReturnService;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = ProductReturn::where('customer_id', auth()->id())
            ->with(['orderItem.product', 'vendor', 'refundRecords'])
            ->latest()
            ->paginate(12);

        return view('returns.index', compact('returns'));
    }

    public function create(Order $order, OrderItem $item)
    {
        abort_unless($order->customer_id === auth()->id() && $item->order_id === $order->id, 403);
        $item->load(['product', 'variant', 'vendorOrder']);
        return view('returns.create', compact('order', 'item'));
    }

    public function store(Request $request, Order $order, OrderItem $item, ReturnService $service)
    {
        abort_unless($order->customer_id === auth()->id() && $item->order_id === $order->id, 403);
        $validated = $request->validate([
            'reason' => 'required|in:wrong_size,defective,damaged,changed_mind,not_as_described,other',
            'description' => 'required|string|min:10|max:2000',
            'quantity' => 'required|integer|min:1|max:' . $item->quantity,
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $service->create($item, $validated, $request->file('photos', []));
            return redirect()->route('returns.index')->with('success', 'Return request submitted to the vendor.');
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(ProductReturn $return)
    {
        abort_unless($return->customer_id === auth()->id(), 403);
        $return->load(['orderItem.product.images', 'orderItem.variant', 'vendor', 'photos', 'statusHistory', 'refundRecords']);
        return view('returns.show', compact('return'));
    }

    public function shipBack(Request $request, ProductReturn $return, ReturnService $service)
    {
        $validated = $request->validate([
            'return_tracking_number' => 'required|string|max:255',
            'return_tracking_url' => 'nullable|url|max:255',
        ]);
        try {
            $service->shipBack($return, $validated);
            return back()->with('success', 'Return tracking submitted.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function appeal(Request $request, ProductReturn $return, ReturnService $service)
    {
        $validated = $request->validate(['claim' => 'required|string|min:10|max:3000']);
        try {
            $service->appeal($return, $validated['claim']);
            return back()->with('success', 'Your appeal was submitted to admin for review.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
