<?php

namespace App\Notifications;

use App\Models\VendorOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorOrderReminderNotification extends Notification
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
        return (new MailMessage)
            ->subject("Reminder: Sub-order #{$this->vendorOrder->vendor_order_number} still pending confirmation")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your sub-order {$this->vendorOrder->vendor_order_number} (order {$order->order_number}) has been pending confirmation for " . now()->diffForHumans($this->vendorOrder->created_at, true) . ".")
            ->line("Please confirm or reject it as soon as possible.")
            ->line("If not confirmed within the required timeframe, the sub-order will be auto-cancelled and the customer refunded.")
            ->action('Confirm Now', route('vendor.orders.show', $this->vendorOrder))
            ->line('Thank you.');
    }

    public function toArray($notifiable): array
    {
        $order = $this->vendorOrder->order;
        return [
            'type'              => 'vendor_order_reminder',
            'vendor_order_id'   => $this->vendorOrder->id,
            'order_id'          => $order->id,
            'order_number'      => $order->order_number,
            'vendor_order_number' => $this->vendorOrder->vendor_order_number,
            'days_pending'      => now()->diffInDays($this->vendorOrder->created_at),
        ];
    }
}
