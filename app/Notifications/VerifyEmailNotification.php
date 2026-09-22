<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify your VandoraX email address')
            ->greeting("Hello {$notifiable->name},")
            ->line('Thanks for signing up for VandoraX. Please confirm your email address to activate your account.')
            ->action('Verify Email Address', $url)
            ->line('This link expires in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('— The VandoraX Team');
    }

    /**
     * Builds a SIGNED, EXPIRING URL:
     *   /email/verify/{id}/{sha1(email)}?expires=...&signature=...
     * The signature is an HMAC of the URL using APP_KEY. Changing any part of
     * the URL invalidates it, so users cannot verify someone else's account.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}