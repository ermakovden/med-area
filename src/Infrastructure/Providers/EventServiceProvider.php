<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\User\Events\UserRegistered;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Notifications\User\EmailVerificationNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * List event listeners
     *
     * @var array
     */
    protected $listen = [
        UserRegistered::class => [EmailVerificationNotification::class],
    ];

    public function register(): void {}

    public function boot(): void {}
}
