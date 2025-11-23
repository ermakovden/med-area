<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Infrastructure\Repositories\Contracts\UserRepositoryContract;
use Infrastructure\Repositories\UserRepository;

class InfrastructureServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        UserRepositoryContract::class => UserRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->bindings as $interface => $class) {
            $this->app->bind($interface, $class);
        }
    }

    public function boot(): void {}
}
