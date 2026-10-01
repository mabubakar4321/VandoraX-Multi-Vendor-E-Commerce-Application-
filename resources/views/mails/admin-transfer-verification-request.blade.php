@component('mail::message')
# Bank Transfer Verification Request

## Payment Information
- **Order Number:** {{ $order->order_number }}
- **Amount:** ₨{{ number_format($order->total_amount) }}
- **Customer Reference:** {{ $customer_reference }}

## Action Required

The customer has submitted their transaction reference for verification. Please review and verify if the transfer matches the expected amount and has been received.

---

Log in to the admin dashboard to verify and approve or reject this payment.

@endcomponent
