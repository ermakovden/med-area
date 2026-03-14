<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\AI\Recognise\Events\RecogniseRequestCompleted;
use Domain\File\Events\FileMarkedForDeletion;
use Domain\User\Events\UserRegistered;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Listeners\AI\DispatchUpdateRecogniseRequestJobListener;
use Infrastructure\Listeners\File\DispatchDeleteFileJobListener;
use Infrastructure\Listeners\User\SendEmailVerificationListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * List event listeners
     *
     * @var array<class-string, array<class-string>>
     */
    protected $listen = [
        UserRegistered::class => [SendEmailVerificationListener::class],
        FileMarkedForDeletion::class => [DispatchDeleteFileJobListener::class],
        RecogniseRequestCompleted::class => [DispatchUpdateRecogniseRequestJobListener::class],
    ];

    public function register(): void {}

    public function boot(): void {}
}
