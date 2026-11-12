<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build(): self
    {
        $this->order->loadMissing(['customer', 'address', 'payment', 'vendorOrders.vendor', 'items.product.vendor', 'items.variant']);
        $pdf = Pdf::loadView('invoices.order', ['order' => $this->order])->setPaper('a4');

        return $this->subject('Invoice for order ' . $this->order->order_number)
            ->view('emails.invoice')
            ->attachData($pdf->output(), 'invoice-' . $this->order->order_number . '.pdf', ['mime' => 'application/pdf']);
    }
}
