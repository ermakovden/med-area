<?php

declare(strict_types=1);

namespace Infrastructure\Listeners\User;

use Domain\User\Events\UserRegistered;
use Domain\User\Models\User;
use Infrastructure\Notifications\User\EmailVerificationNotification;

class SendEmailVerificationListener
{
    public function handle(UserRegistered $event): void
    {
        /** @var User $user */
        $user = $event->user;

        logger()->debug('[SendEmailVerificationListener.handle] sending email verification notification', [
            'user_id' => $user->id,
        ]);

        if (! $user->hasVerifiedEmail()) {
            $user->notify(new EmailVerificationNotification($user));
        }
    }
}
