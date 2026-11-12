<?php

namespace App\Notifications;

use App\Models\VendorOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVendorOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public VendorOrder $vendorOrder) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $order = $this->vendorOrder->order;
        $items = $this->vendorOrder->items()->with(['product', 'variant'])->get();

        $itemLines = $items->map(function ($item) {
            $name = $item->product?->name ?? 'Unknown product';
            if ($item->variant) {
                $name .= " ({$item->variant->variant_value})";
            }
            return "  • {$name} × {$item->quantity} — ₨" . number_format($item->total_amount);
        })->implode("\n");

        $customerName = $order->customer ? $order->customer->name : 'Customer';
        return (new MailMessage)
            ->subject("New order sub-order #{$this->vendorOrder->vendor_order_number} from {$customerName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have received a new sub-order that requires your confirmation.")
            ->line("Order Number: {$order->order_number}")
            ->line("Sub-order Number: {$this->vendorOrder->vendor_order_number}")
            ->line("Customer: {$customerName}")
            ->line("Total (your share): ₨" . number_format($this->vendorOrder->vendor_amount))
            ->line("Items:\n{$itemLines}")
            ->line("Please review and confirm this sub-order in your vendor dashboard as soon as possible.")
            ->action('Review in Dashboard', route('vendor.orders.show', $this->vendorOrder))
            ->line('Thank you for being a VandoraX seller.');
    }

    public function toArray($notifiable): array
    {
        $order = $this->vendorOrder->order;
        return [
            'type'         => 'new_vendor_order',
            'vendor_order_id' => $this->vendorOrder->id,
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'customer'     => $order->customer ? $order->customer->name : null,
            'vendor_amount'=> (float) $this->vendorOrder->vendor_amount,
            'status'       => $this->vendorOrder->status,
        ];
    }
}
