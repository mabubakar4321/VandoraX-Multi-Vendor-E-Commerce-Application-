<?php

namespace App\Notifications;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorAutoBlockedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Vendor $vendor,
        private string $reason
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Store Has Been Automatically Blocked')
            ->greeting('Hello ' . $this->vendor->store_name)
            ->line('Your store has been automatically blocked due to receiving too many negative reviews.')
            ->line('Reason: ' . $this->reason)
            ->line('While blocked, you cannot add or edit products, and customers cannot place new orders with your store. Existing orders will continue as normal.')
            ->action('Request Unblock', url('/vendor/unblock/create'))
            ->line('If you have made improvements to address customer concerns, you may submit an unblock request through the link above.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'vendor_id' => $this->vendor->id,
            'reason' => $this->reason,
            'type' => 'auto_blocked',
        ];
    }
}