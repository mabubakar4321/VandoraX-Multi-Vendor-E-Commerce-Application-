<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Mail\PaymentReceivedMail;
use App\Mail\VendorOrderReceivedMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentNotificationService
{
    public function sendPaymentReceived(Order $order): void
    {
        try {
            $order->loadMissing(['customer', 'vendorOrders.vendor.user']);
            if ($order->customer?->email) {
                Mail::to($order->customer->email)->send(new PaymentReceivedMail($order));
                Mail::to($order->customer->email)->send(new InvoiceMail($order));
            }

            foreach ($order->vendorOrders as $vendorOrder) {
                $email = $vendorOrder->vendor?->user?->email;
                if ($email) {
                    Mail::to($email)->send(new VendorOrderReceivedMail($order, $vendorOrder));
                }
            }
        } catch (\Throwable $exception) {
            Log::error('Payment notification delivery failed', [
                'order_id' => $order->id,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
