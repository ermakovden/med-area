<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Infrastructure\Repositories\AnalysRepository;
use Infrastructure\Repositories\Contracts\AnalysRepositoryContract;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;
use Infrastructure\Repositories\Contracts\UserRepositoryContract;
use Infrastructure\Repositories\UserAnalysRepository;
use Infrastructure\Repositories\UserRepository;

class InfrastructureServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [
        // User
        UserRepositoryContract::class => UserRepository::class,
    
        // Analys
        AnalysRepositoryContract::class => AnalysRepository::class,
        UserAnalysRepositoryContract::class => UserAnalysRepository::class,
    ];

    public function register(): void
    {
        foreach ($this->bindings as $interface => $class) {
            $this->app->bind($interface, $class);
        }
    }

    public function boot(): void {}
}
