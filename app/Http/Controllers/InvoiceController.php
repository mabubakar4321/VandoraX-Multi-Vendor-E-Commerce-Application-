<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function download(Order $order): Response
    {
        abort_unless($this->canView($order), 403);

        $order->load(['customer', 'address', 'payment', 'vendorOrders.vendor', 'items.product.vendor', 'items.variant']);

        return Pdf::loadView('invoices.order', compact('order'))
            ->setPaper('a4')
            ->download('invoice-' . $order->order_number . '.pdf');
    }

    public function view(Order $order)
    {
        abort_unless($this->canView($order), 403);

        $order->load(['customer', 'address', 'payment', 'vendorOrders.vendor', 'items.product.vendor', 'items.variant']);

        return view('invoices.order', compact('order'));
    }

    private function canView(Order $order): bool
    {
        return auth()->id() === $order->customer_id || auth()->user()?->hasRole('admin');
    }
}
