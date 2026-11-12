@php
    $payment = $order->payment;
    $paymentMethod = $payment?->payment_method;
    $paymentLabel = match ($paymentMethod) {
        'bank_transfer' => 'Bank Transfer',
        'wallet', 'jazzcash' => 'JazzCash',
        'card' => 'Card Payment',
        'cod' => 'Cash on Delivery',
        default => $payment ? ucfirst(str_replace('_', ' ', $paymentMethod ?? 'Unknown')) : 'Cash on Delivery',
    };
@endphp

<div class="vendor-print-invoice p-8 bg-white text-gray-900" id="print-area" style="max-width:800px; margin:0 auto; font-family:Arial,sans-serif;">

    <!-- Header -->
    <div class="flex justify-between items-start mb-8 border-b-2 border-gray-800 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">VandoraX</h1>
            <p class="text-sm text-gray-500">Order Invoice / Receipt</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-semibold text-gray-900">Sub-Order: {{ $vendorOrder->vendor_order_number }}</p>
            <p class="text-sm text-gray-500">Main Order: {{ $order->order_number }}</p>
            <p class="text-sm text-gray-500">Date: {{ $order->created_at->format('F d, Y H:i') }}</p>
        </div>
    </div>

    <!-- Vendor & Customer -->
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div>
            <p class="text-xs font-bold uppercase text-gray-400 mb-2">Vendor Store</p>
            <p class="font-semibold text-gray-900">{{ $vendorOrder->vendor?->store_name ?? 'Vendor' }}</p>
            <p class="text-sm text-gray-500">Owner: {{ $vendorOrder->vendor?->user?->name ?? 'Not available' }}</p>
            <p class="text-sm text-gray-500">Category: {{ $vendorOrder->vendor?->category?->name ?? 'Unassigned' }}</p>
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-gray-400 mb-2">Customer</p>
            <p class="font-semibold text-gray-900">{{ $order->customer->name ?? 'Customer' }}</p>
            <p class="text-sm text-gray-500">{{ $order->customer->email ?? 'No email' }}</p>
            <p class="text-sm text-gray-500">Phone: {{ $order->address->phone_number ?? 'N/A' }}</p>
        </div>
    </div>

    <!-- Delivery Address -->
    <div class="mb-8 p-4 bg-gray-50 rounded">
        <p class="text-xs font-bold uppercase text-gray-400 mb-2">Delivery Address</p>
        <p class="text-sm text-gray-800">{{ $order->address->full_name ?? '' }}</p>
        <p class="text-sm text-gray-800">{{ $order->address->address_line_1 ?? '' }}@if($order->address?->address_line_2), {{ $order->address->address_line_2 }}@endif</p>
        <p class="text-sm text-gray-800">{{ $order->address->city ?? '' }}, {{ $order->address->province ?? '' }} {{ $order->address->postal_code ?? '' }}</p>
        <p class="text-sm text-gray-800">{{ $order->address->country ?? '' }}</p>
        @if($order->address?->delivery_instructions)
            <p class="text-xs text-gray-500 mt-1 italic">Note: {{ $order->address->delivery_instructions }}</p>
        @endif
    </div>

    <!-- Items Table -->
    <table class="w-full text-sm mb-6" style="border-collapse: collapse;">
        <thead>
            <tr class="border-b-2 border-gray-300 text-left text-xs uppercase text-gray-500">
                <th class="py-2 pr-4">Item</th>
                <th class="py-2 pr-4">Variant</th>
                <th class="py-2 pr-4 text-right">Qty</th>
                <th class="py-2 pr-4 text-right">Unit Price</th>
                <th class="py-2 text-right">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr class="border-b border-gray-200">
                    <td class="py-2 pr-4 font-medium">{{ $item->product?->name ?? 'Product' }}</td>
                    <td class="py-2 pr-4 text-gray-500">{{ $item->variant?->variant_value ?? '—' }}</td>
                    <td class="py-2 pr-4 text-right">{{ $item->quantity }}</td>
                    <td class="py-2 pr-4 text-right">Rs {{ number_format($item->price_at_purchase) }}</td>
                    <td class="py-2 text-right font-semibold">Rs {{ number_format($item->total_amount) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-3 text-gray-500">No items found.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="border-t-2 border-gray-300">
                <td colspan="4" class="py-2 pr-4 text-right font-semibold">Subtotal</td>
                <td class="py-2 text-right font-bold">Rs {{ number_format($vendorOrder->subtotal) }}</td>
            </tr>
            <tr>
                <td colspan="4" class="py-1 pr-4 text-right text-gray-500">Commission ({{ $vendorOrder->vendor?->commission_rate ?? '0.00' }}%)</td>
                <td class="py-1 text-right text-gray-600">Rs {{ number_format($vendorOrder->commission_amount) }}</td>
            </tr>
            <tr class="border-t border-gray-300">
                <td colspan="4" class="py-2 pr-4 text-right font-bold text-lg">Your Payout</td>
                <td class="py-2 text-right font-bold text-lg text-green-700">Rs {{ number_format($vendorOrder->vendor_amount) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Payment & Status -->
    <div class="grid grid-cols-2 gap-6 mb-8">
        <div>
            <p class="text-xs font-bold uppercase text-gray-400 mb-2">Payment Details</p>
            <p class="text-sm text-gray-700">Method: {{ $paymentLabel }}</p>
            <p class="text-sm text-gray-700">Status: <span class="capitalize font-medium">{{ $payment?->status ?? 'N/A' }}</span></p>
            <p class="text-sm text-gray-700">Order Total: Rs {{ number_format($order->total_amount) }}</p>
        </div>
        <div>
            <p class="text-xs font-bold uppercase text-gray-400 mb-2">Sub-Order Status</p>
            <p class="text-sm text-gray-700">Status: <span class="capitalize font-medium">{{ $vendorOrder->status }}</span></p>
            @if($vendorOrder->tracking_number)
                <p class="text-sm text-gray-700">Tracking: {{ $vendorOrder->tracking_number }}</p>
            @endif
            @if($vendorOrder->confirmed_at)
                <p class="text-sm text-gray-700">Confirmed: {{ $vendorOrder->confirmed_at->format('M d, Y H:i') }}</p>
            @endif
            @if($vendorOrder->shipped_at)
                <p class="text-sm text-gray-700">Shipped: {{ $vendorOrder->shipped_at->format('M d, Y H:i') }}</p>
            @endif
            @if($vendorOrder->delivered_at)
                <p class="text-sm text-gray-700">Delivered: {{ $vendorOrder->delivered_at->format('M d, Y H:i') }}</p>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="border-t border-gray-200 pt-4 text-center text-xs text-gray-400">
        <p>This is a system-generated invoice from VandoraX.</p>
        <p>Sub-order: {{ $vendorOrder->vendor_order_number }} | Main Order: {{ $order->order_number }}</p>
        <p>Printed: {{ now()->format('F d, Y H:i:s') }}</p>
    </div>

    <!-- Print Button -->
    <div class="mt-8 text-center no-print" style="display:none;">
        <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded font-semibold">Print This Page</button>
    </div>
</div>

<script>
    window.addEventListener('load', function () {
        // Automatically show print button
        document.querySelector('.no-print').style.display = 'block';
    });
</script>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>
