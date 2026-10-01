@component('mail::message')
# New Bank Transfer Pending Verification

## Order Information
- **Order Number:** {{ $order->order_number }}
- **Customer:** {{ $order->customer->name }} ({{ $order->customer->email }})
- **Amount:** ₨{{ number_format($amount) }}
- **Created At:** {{ $order->created_at->format('M d, Y H:i') }}

## Bank Details Provided
- **Account Title:** {{ $bank_details['account_title'] }}
- **Account Number:** {{ $bank_details['account_number'] }}
- **Reference Number:** {{ $payment->transaction_id }}

## Action Required

This payment is awaiting manual verification. Please check your bank records to verify if the transfer has been received.

**Options:**
1. **Approve Payment** - If the transfer has been received, verify and release the order
2. **Request More Info** - If you need additional information from the customer
3. **Reject Payment** - If there's an issue with the transfer

## Payment Details
- **Payment ID:** {{ $payment->id }}
- **Status:** Pending Verification
- **Created:** {{ $payment->created_at->format('M d, Y H:i:s') }}

---

Log in to the admin dashboard to manage this payment.

@endcomponent
