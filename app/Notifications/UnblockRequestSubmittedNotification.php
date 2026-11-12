<?php

namespace App\Notifications;

use App\Models\VendorUnblockRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnblockRequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private VendorUnblockRequest $request
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Unblock Request Received')
            ->greeting('Hello ' . $this->request->vendor->store_name)
            ->line('We have received your unblock request and it is now under review.')
            ->line('Our team will evaluate the improvements you have made and respond as soon as possible.')
            ->action('View Request Status', url('/vendor/unblock/' . $this->request->id))
            ->line('Thank you for your patience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'unblock_request_id' => $this->request->id,
            'vendor_id' => $this->request->vendor_id,
            'type' => 'unblock_submitted',
        ];
    }
}