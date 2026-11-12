<?php

namespace App\Notifications;

use App\Models\VendorPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorPayoutProcessedNotification extends Notification
{
    use Queueable;

    public function __construct(public VendorPayout $payout) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $bank = $this->payout->vendor?->bankDetails;

        return (new MailMessage)
            ->subject('Payout credited — ₨' . number_format($this->payout->amount, 2))
            ->greeting("Hello {$notifiable->name},")
            ->line("Your payout has been processed and credited to your vendor account.")
            ->line("Amount credited: ₨" . number_format($this->payout->amount, 2))
            ->line("Gross order amount: ₨" . number_format($this->payout->gross_amount, 2))
            ->line("Admin commission deducted: -₨" . number_format($this->payout->commission_amount, 2))
            ->line("Reference: {$this->payout->transaction_id}")
            ->line("Date: " . now()->format('M d, Y H:i'))
            ->when($bank, function (MailMessage $m) use ($bank) {
                $m->line("Destination: {$bank->bank_name} · {$bank->maskedAccountNumber()}");
            })
            ->line("View details in your Seller Centre under Payments.")
            ->action('View payout', route('vendor.payments.show', $this->payout))
            ->line('Thank you for being a VandoraX seller.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'payout_processed',
            'payout_id' => $this->payout->id,
            'amount' => (float) $this->payout->amount,
            'commission' => (float) $this->payout->commission_amount,
            'gross' => (float) $this->payout->gross_amount,
            'reference' => $this->payout->transaction_id,
            'processed_at' => $this->payout->processed_at?->toDateTimeString(),
        ];
    }
}
