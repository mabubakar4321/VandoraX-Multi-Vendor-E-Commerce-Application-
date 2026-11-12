<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\VendorOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorOrderReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public VendorOrder $vendorOrder) {}

    public function build(): self
    {
        return $this->subject('New order received: ' . $this->order->order_number)
            ->view('emails.vendor-order-received');
    }
}
