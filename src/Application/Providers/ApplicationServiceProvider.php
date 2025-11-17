<?php

declare(strict_types=1);

namespace Application\Providers;

use Application\User\Services\Contracts\RegistrationServiceContract;
use Application\User\Services\RegistrationService;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        RegistrationServiceContract::class => RegistrationService::class
    ];

    public function register(): void
    {
        foreach ($this->bindings as $interface => $class) {
            $this->app->bind($interface, $class);
        }
    }

    public function boot(): void
    {}
}
