<?php

namespace App\Notifications;

use App\Models\ProductReturn;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReturnUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProductReturn $return, public string $message) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Return update — ' . $this->return->return_number)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message)
            ->line('Current status: ' . ucfirst(str_replace('_', ' ', $this->return->status)))
            ->action(
                'View return',
                $notifiable->hasRole('vendor')
                    ? url('/vendor/returns/' . $this->return->id)
                    : url('/returns/' . $this->return->id)
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'return_updated',
            'return_id' => $this->return->id,
            'return_number' => $this->return->return_number,
            'status' => $this->return->status,
            'message' => $this->message,
        ];
    }
}
