<?php

declare(strict_types=1);

namespace Application\Providers;

use Application\User\Services\AuthService;
use Application\User\Services\Contracts\AuthServiceContract;
use Application\User\Services\Contracts\RegistrationServiceContract;
use Application\User\Services\Contracts\UserServiceContract;
use Application\User\Services\RegistrationService;
use Application\User\Services\UserService;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        RegistrationServiceContract::class => RegistrationService::class,
        AuthServiceContract::class => AuthService::class,
        UserServiceContract::class => UserService::class,
    ];

    public function register(): void
    {
        foreach ($this->bindings as $interface => $class) {
            $this->app->bind($interface, $class);
        }
    }

    public function boot(): void {}
}
