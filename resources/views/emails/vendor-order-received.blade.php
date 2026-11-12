@component('mail::message')
# New order received

A customer placed order **{{ $order->order_number }}** containing products from your store.

**Your vendor amount:** Rs {{ number_format($vendorOrder->vendor_amount) }}

Please review the order in the vendor dashboard.
@endcomponent
