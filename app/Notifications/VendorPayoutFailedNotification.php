<?php

namespace App\Notifications;

use App\Models\VendorPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorPayoutFailedNotification extends Notification
{
    use Queueable;

    public function __construct(public VendorPayout $payout, public string $reason = '') {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payout transfer failed')
            ->greeting("Hello {$notifiable->name},")
            ->line("We attempted to credit your payout of ₨" . number_format($this->payout->amount, 2) . " but the bank transfer failed.")
            ->line("Reference: {$this->payout->transaction_id}")
            ->when($this->reason !== '', function (MailMessage $m) {
                $m->line("Reason: {$this->reason}");
            })
            ->line('Our team will retry the transfer automatically. No action is needed unless the issue persists.')
            ->line('If you have not been paid within 48 hours, please contact support.')
            ->action('View payout', route('vendor.payments.show', $this->payout))
            ->line('Thank you for your patience.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'payout_failed',
            'payout_id' => $this->payout->id,
            'amount' => (float) $this->payout->amount,
            'reference' => $this->payout->transaction_id,
            'reason' => $this->reason,
        ];
    }
}
