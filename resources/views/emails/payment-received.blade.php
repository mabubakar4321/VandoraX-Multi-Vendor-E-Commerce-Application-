@component('mail::message')
# Payment received

Hello {{ $order->customer->name }},

We received your payment for order **{{ $order->order_number }}**.

**Amount:** Rs {{ number_format($order->total_amount) }}

Your invoice is attached separately. We will continue processing your order.
@endcomponent
