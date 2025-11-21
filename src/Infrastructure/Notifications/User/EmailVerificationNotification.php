<?php

declare(strict_types=1);

namespace Infrastructure\Notifications\User;

use Domain\User\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerificationNotification extends VerifyEmail
{
    use Queueable;

    public function __construct(protected User $user) {}

    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Welcome to MedArea!')
            ->line('Thank you for registering, ' . $this->user->nickname . '!')
            ->action('Click to verify your email: ', url('/'))
            ->line('Thank you for using our application!');
    }

    public function verificationUrl($notifiable)
    {
        // make public method (parent protected)
        return parent::verificationUrl($notifiable);
    }
}
