@component('mail::message')
# Payment Confirmed

Hello {{ $order->customer->name }},

Great news! Your payment has been confirmed.

## Order Confirmation

- **Order Number:** {{ $order->order_number }}
- **Payment Method:** {{ $payment_method }}
- **Total Amount:** ₨{{ number_format($order->total_amount) }}
- **Status:** Payment Completed ✓

Your order is now being processed and will be shipped soon. You'll receive a tracking number as soon as your items are dispatched.

## What's Next?

1. Our team will prepare your order for shipment
2. You'll receive a shipping notification with tracking details
3. Your items will be delivered to your address

## Need Help?

If you have any questions about your order, please don't hesitate to contact us.

@component('mail::button', ['url' => route('order.show', $order)])
View Your Order
@endcomponent

Thank you for shopping with us!

@endcomponent
