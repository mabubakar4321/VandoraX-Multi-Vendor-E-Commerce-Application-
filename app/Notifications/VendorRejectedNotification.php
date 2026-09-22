<?php

namespace App\Notifications;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(public Vendor $vendor, public string $reason) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Update on your VandoraX store application')
            ->greeting("Hello {$notifiable->name},")
            ->line('We were unable to approve your store application at this time.')
            ->line("Reason: {$this->reason}")
            ->action('Update your application', route('vendor.pending'))
            ->line('You can correct the issue and resubmit your documents.');
    }

    public function toArray($notifiable): array
    {
        return ['type' => 'vendor_rejected', 'vendor_id' => $this->vendor->id, 'reason' => $this->reason];
    }
}