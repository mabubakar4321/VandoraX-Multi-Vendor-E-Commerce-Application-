<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #15352b; font-size: 12px; line-height: 1.5; margin: 0; padding: 20px; }
        h1, h2, h3 { margin: 0 0 8px; }
        h1 { font-size: 24px; }
        h2 { font-size: 15px; border-bottom: 1px solid #d9e2dc; padding-bottom: 6px; }
        h3 { font-size: 13px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #f2b705; padding-bottom: 18px; margin-bottom: 24px; }
        .muted { color: #66736d; }
        .box { border: 1px solid #d9e2dc; padding: 14px; margin-bottom: 18px; }
        .row { display: flex; justify-content: space-between; padding: 5px 0; }
        .total { font-size: 16px; font-weight: bold; border-top: 2px solid #15352b; margin-top: 8px; padding-top: 10px; }
        .items { width: 100%; border-collapse: collapse; }
        .items th, .items td { border-bottom: 1px solid #d9e2dc; text-align: left; padding: 8px; }
        .items th:last-child, .items td:last-child { text-align: right; }
        .vendor-section { margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px dashed #d9e2dc; }
        .vendor-section:last-child { border-bottom: none; }
        .vendor-header { background: #f5f7f5; padding: 8px 12px; margin-bottom: 8px; font-weight: bold; }
        .tax-row { color: #66736d; font-size: 11px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .badge-paid { background: #d4edda; color: #155724; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-failed { background: #f8d7da; color: #721c24; }
        .escrow-info { background: #e8f4fd; border: 1px solid #bee5eb; padding: 10px; margin-top: 12px; font-size: 11px; }
        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #d9e2dc; text-align: center; font-size: 10px; color: #66736d; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>VandoraX</h1>
            <div class="muted">Multi-Vendor Marketplace Invoice</div>
            <div class="muted" style="margin-top: 4px;">{{ config('app.url') }}</div>
        </div>
        <div style="text-align: right">
            <strong style="font-size: 16px;">{{ $order->order_number }}</strong><br>
            <span class="muted">{{ $order->created_at->format('F d, Y') }}</span><br>
            @php
                $statusBadge = match($order->payment_status) {
                    'completed' => 'badge-paid',
                    'pending' => 'badge-pending',
                    default => 'badge-failed',
                };
            @endphp
            <span class="badge {{ $statusBadge }}">{{ ucfirst($order->payment_status) }}</span>
        </div>
    </div>

    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
        <div class="box" style="flex: 1;">
            <h2>Customer Details</h2>
            <strong>{{ $order->customer->name ?? 'Customer' }}</strong><br>
            {{ $order->customer->email ?? '' }}<br>
            <span class="muted">Customer ID: #{{ $order->customer_id }}</span>
        </div>
        <div class="box" style="flex: 1;">
            <h2>Delivery Address</h2>
            <strong>{{ $order->address->full_name ?? '' }}</strong><br>
            {{ $order->address->address_line_1 ?? '' }}<br>
            @if($order->address->address_line_2){{ $order->address->address_line_2 }}<br>@endif
            {{ $order->address->city ?? '' }}, {{ $order->address->province ?? '' }} {{ $order->address->postal_code ?? '' }}<br>
            {{ $order->address->country ?? '' }}<br>
            <span class="muted">Phone: {{ $order->address->phone_number ?? '' }}</span>
        </div>
    </div>

    @if($order->payment)
    <div class="box">
        <h2>Payment Information</h2>
        <div style="display: flex; gap: 20px;">
            <div style="flex: 1;">
                <div class="row"><span class="muted">Payment Method:</span> <strong>{{ $order->payment->payment_method === 'card' ? 'Stripe Card' : ($order->payment->payment_method === 'bank_transfer' ? 'Bank Transfer' : 'Cash on Delivery') }}</strong></div>
                <div class="row"><span class="muted">Gateway:</span> <strong>{{ ucfirst($order->payment->payment_gateway ?? 'N/A') }}</strong></div>
                <div class="row"><span class="muted">Transaction ID:</span> <strong style="font-size: 10px;">{{ $order->payment->transaction_id ?? 'N/A' }}</strong></div>
            </div>
            <div style="flex: 1;">
                <div class="row"><span class="muted">Payment Status:</span> <strong>{{ ucfirst($order->payment->status) }}</strong></div>
                <div class="row"><span class="muted">Escrow Status:</span> <strong>{{ ucfirst($order->payment->escrow_status ?? 'N/A') }}</strong></div>
                @if($order->payment->paid_at)
                <div class="row"><span class="muted">Paid At:</span> <strong>{{ $order->payment->paid_at->format('M d, Y H:i') }}</strong></div>
                @endif
            </div>
        </div>
        @if($order->payment->escrow_status === 'held')
        <div class="escrow-info">
            <strong>Escrow Notice:</strong> Funds are held in escrow. The admin can release funds to the vendor(s) at any time after verifying delivery.
        </div>
        @endif
    </div>
    @endif

    <div class="box">
        <h2>Order Items — Vendor Breakdown</h2>
        
        @php
            $itemsByVendor = $order->items->groupBy(function($item) {
                return $item->product->vendor_id;
            });
            $grandSubtotal = 0;
            $grandCommission = 0;
            $grandTax = 0;
            $taxRate = 0;
        @endphp

        @foreach($itemsByVendor as $vendorId => $vendorItems)
            @php
                $vendor = $vendorItems->first()->product->vendor;
                $vendorSubtotal = $vendorItems->sum('total_amount');
                $vendorCommission = 0;
                foreach ($vendorItems as $item) {
                    $rate = $item->product->category->commission_rate ?? config('vandorax.default_commission_rate', 15);
                    $vendorCommission += ($item->total_amount * $rate / 100);
                }
                $grandSubtotal += $vendorSubtotal;
                $grandCommission += $vendorCommission;
            @endphp
            <div class="vendor-section">
                <div class="vendor-header">
                    Vendor: {{ $vendor->store_name ?? $vendor->user->name ?? 'Vendor #' . $vendorId }}
                    <span class="muted" style="float: right; font-weight: normal; font-size: 11px;">Commission: {{ number_format($vendorCommission, 2) }} PKR</span>
                </div>
                <table class="items">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Product</th>
                            <th style="width: 15%;">Variant</th>
                            <th style="width: 10%;">Qty</th>
                            <th style="width: 15%;">Unit Price</th>
                            <th style="width: 20%;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendorItems as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->variant->variant_value ?? '—' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rs {{ number_format($item->price_at_purchase, 2) }}</td>
                            <td>Rs {{ number_format($item->total_amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="text-align: right; margin-top: 6px;">
                    <span class="muted">Vendor Subtotal:</span> <strong>Rs {{ number_format($vendorSubtotal, 2) }}</strong>
                    <span class="muted" style="margin-left: 15px;">Vendor Receives:</span> <strong>Rs {{ number_format($vendorSubtotal - $vendorCommission, 2) }}</strong>
                </div>
            </div>
        @endforeach

        <div style="margin-top: 16px; padding-top: 12px; border-top: 2px solid #15352b;">
            <div class="row"><span>Subtotal (All Vendors)</span><strong>Rs {{ number_format($grandSubtotal, 2) }}</strong></div>
            <div class="row"><span>Platform Commission ({{ config('vandorax.default_commission_rate', 15) }}%)</span><strong style="color: #66736d;">- Rs {{ number_format($grandCommission, 2) }}</strong></div>
            <div class="row"><span>Shipping</span><strong style="color: #10b981;">Free</strong></div>
            <div class="row tax-row"><span>Tax (Included in prices)</span><span>Inclusive</span></div>
            <div class="row total"><span>Grand Total</span><span>Rs {{ number_format($order->total_amount, 2) }}</span></div>
        </div>
    </div>

    @if($order->vendorOrders && $order->vendorOrders->count() > 0)
    <div class="box">
        <h2>Vendor Order Allocations</h2>
        <table class="items">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Vendor Order #</th>
                    <th>Subtotal</th>
                    <th>Commission</th>
                    <th>Vendor Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->vendorOrders as $vo)
                <tr>
                    <td>{{ $vo->vendor->store_name ?? $vo->vendor->user->name ?? 'Vendor' }}</td>
                    <td style="font-size: 10px;">{{ $vo->vendor_order_number }}</td>
                    <td>Rs {{ number_format($vo->subtotal, 2) }}</td>
                    <td>Rs {{ number_format($vo->commission_amount, 2) }}</td>
                    <td><strong>Rs {{ number_format($vo->vendor_amount, 2) }}</strong></td>
                    <td><span class="badge {{ $vo->status === 'delivered' ? 'badge-paid' : ($vo->status === 'cancelled' ? 'badge-failed' : 'badge-pending') }}">{{ ucfirst($vo->status) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p><strong>VandoraX Multi-Vendor Marketplace</strong></p>
        <p>For inquiries, contact support at {{ config('mail.from.address', 'support@vandorax.com') }}</p>
        <p>Escrow Account: {{ config('escrow.account.holder', 'VandoraX Escrow') }} — {{ config('escrow.account.bank_name', 'HBL') }} — {{ config('escrow.account.iban', '') }}</p>
        <p style="margin-top: 8px;">This is a computer-generated invoice. Thank you for shopping with VandoraX.</p>
    </div>
</body>
</html>
