<?php

declare(strict_types=1);

namespace Infrastructure\Notifications\User;

use Domain\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class EmailVerificationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected User $user) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Welcome to MedArea!')
            ->line('Thank you for registering, ' . $this->user->nickname . '!')
            ->action('Click to verify your email: ', $this->verificationUrl($notifiable))
            ->line('Thank you for using our application!');
    }

    /**
     * Method from Illuminate\Auth\Notifications\VerifyEmail::verificationUrl($notifiable)
     *
     * @param [type] $notifiable
     * @return void
     */
    public function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
