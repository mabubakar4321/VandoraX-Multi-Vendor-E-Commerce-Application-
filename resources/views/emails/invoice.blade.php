@component('mail::message')
# Your VandoraX Invoice

Hello {{ $order->customer->name }},

Your payment for order **{{ $order->order_number }}** has been received. Your detailed invoice (PDF) is attached to this email.

---

**Order Details**

| | |
|---|---|
| **Order Number** | {{ $order->order_number }} |
| **Total Amount** | Rs {{ number_format($order->total_amount, 2) }} |
| **Payment Method** | {{ $order->payment ? ($order->payment->payment_method === 'card' ? 'Stripe Card' : ucfirst(str_replace('_',' ',$order->payment->payment_method))) : 'Cash on Delivery' }} |
| **Payment Status** | {{ ucfirst($order->payment_status) }} |
| **Vendors** | {{ $order->vendor_count }} vendor(s) |

**Item Summary (by vendor):**

@foreach($order->items->groupBy('product.vendor_id') as $vendorId => $vendorItems)
@php $vendor = $vendorItems->first()->product->vendor; @endphp
- **{{ $vendor->store_name ?? 'Vendor' }}** — {{ $vendorItems->count() }} item(s), subtotal Rs {{ number_format($vendorItems->sum('total_amount'), 2) }}
@endforeach

@if($order->payment && $order->payment->escrow_status === 'held')
> **Escrow Notice:** Your payment of Rs {{ number_format($order->total_amount, 2) }} is held securely in escrow and will be released to the vendor(s) after delivery confirmation.
@endif

---

**View / Download Invoice:** [Click here]({{ url('orders/'.$order->id.'/invoice') }})

If you have questions about this invoice, reply to this email.

Thank you for shopping with VandoraX.

VandoraX Support
@endcomponent
