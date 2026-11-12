<?php

namespace App\Notifications;

use App\Models\VendorUnblockRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnblockRequestReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private VendorUnblockRequest $request,
        private bool $approved
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->approved) {
            return (new MailMessage)
                ->subject('Your Unblock Request Has Been Approved')
                ->greeting('Hello ' . $this->request->vendor->store_name)
                ->line('Your unblock request has been approved. Your store is now active again.')
                ->line('You can resume adding products and accepting new orders.')
                ->action('Go to Dashboard', url('/vendor/dashboard'))
                ->line('Please continue to maintain high quality standards to avoid future blocks.');
        }

        return (new MailMessage)
            ->subject('Your Unblock Request Has Been Rejected')
            ->greeting('Hello ' . $this->request->vendor->store_name)
            ->line('Your unblock request has been reviewed and unfortunately rejected.')
            ->line('Reason: ' . ($this->request->admin_response ?? 'No reason provided.'))
            ->line('You may reapply after 30 days with additional improvements.')
            ->action('View Request Details', url('/vendor/unblock/' . $this->request->id));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'unblock_request_id' => $this->request->id,
            'vendor_id' => $this->request->vendor_id,
            'approved' => $this->approved,
            'type' => 'unblock_reviewed',
        ];
    }
}