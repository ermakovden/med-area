<?php

declare(strict_types=1);

namespace Application\Providers;

use Application\Analys\Services\AnalysService;
use Application\Analys\Services\Contracts\AnalysServiceContract;
use Application\Analys\Services\Contracts\UserAnalysServiceContract;
use Application\Analys\Services\UserAnalysService;
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
        // User
        RegistrationServiceContract::class => RegistrationService::class,
        AuthServiceContract::class => AuthService::class,
        UserServiceContract::class => UserService::class,

        // Analys
        AnalysServiceContract::class => AnalysService::class,
        UserAnalysServiceContract::class => UserAnalysService::class,
    ];

    public function register(): void
    {
        foreach ($this->bindings as $interface => $class) {
            $this->app->bind($interface, $class);
        }
    }

    public function boot(): void {}
}
