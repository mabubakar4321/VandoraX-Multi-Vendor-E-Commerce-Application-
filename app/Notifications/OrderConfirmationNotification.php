<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmationNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $order = $this->order;
        $vendors = $order->vendorOrders()->with('vendor')->get()
            ->map(fn ($vo) => "  • " . ($vo->vendor?->store_name ?? 'Vendor ' . $vo->vendor_id) . " — sub-order " . $vo->vendor_order_number)
            ->implode("\n");

        return (new MailMessage)
            ->subject("Order {$order->order_number} confirmed — " . number_format($order->total_amount) . " PKR")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your order has been placed successfully. A total of {$order->vendor_count} vendor(s) will be fulfilling your order.")
            ->line("Order Number: {$order->order_number}")
            ->line("Total: ₨" . number_format($order->total_amount))
            ->line("Payment Status: " . ucfirst($order->payment_status))
            ->line("Vendors assigned:\n{$vendors}")
            ->line("You can track your order at any time from your account.")
            ->action('View Order', route('order.show', $order->id))
            ->line('Thank you for shopping with VandoraX.');
    }

    public function toArray($notifiable): array
    {
        $order = $this->order;
        return [
            'type'         => 'order_confirmed',
            'order_id'     => $order->id,
            'order_number' => $order->order_number,
            'total'        => (float) $order->total_amount,
            'vendor_count' => $order->vendor_count,
        ];
    }
}
