<?php

namespace App\Notifications;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(public Vendor $vendor) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your VandoraX store is approved 🎉')
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("Your store \"{$this->vendor->store_name}\" has been approved.")
            ->action('Go to your dashboard', route('vendor.dashboard'))
            ->line('You can now start listing products.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type'       => 'vendor_approved',
            'vendor_id'  => $this->vendor->id,
            'store_name' => $this->vendor->store_name,
        ];
    }
}