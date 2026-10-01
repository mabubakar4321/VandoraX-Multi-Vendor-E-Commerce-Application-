@component('mail::message')
# Bank Transfer Payment Details

Hello {{ $order->customer->name }},

Thank you for your order! Please complete your payment using the bank transfer details below.

## Order Information
- **Order Number:** {{ $order->order_number }}
- **Order Total:** ₨{{ number_format($order->total_amount) }}
- **Reference Number:** {{ $reference }}

## Bank Details

Please transfer the amount to:

| Detail | Value |
|--------|-------|
| **Bank Name** | {{ $bank_details['name'] }} |
| **Account Title** | {{ $bank_details['account_title'] }} |
| **Account Number** | {{ $bank_details['account_number'] }} |
| **IBAN** | {{ $bank_details['iban'] }} |
| **Swift Code** | {{ $bank_details['swift_code'] }} |

## Transfer Instructions

@foreach($instructions['steps'] as $step)
- {{ $step }}
@endforeach

## Important Notes

- Please include the reference number **{{ $reference }}** in your transfer to help us match your payment
- Your order will be confirmed once we receive the transfer
- This usually takes 1-2 business days
- Please keep your bank transfer receipt for records

If you have any questions, please contact our support team.

---

**Amount to Transfer:** ₨{{ number_format($order->total_amount) }}
**Reference:** {{ $reference }}

@endcomponent
