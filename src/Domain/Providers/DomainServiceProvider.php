<?php

declare(strict_types=1);

namespace Domain\Providers;

use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [];

    public function register(): void
    {
        foreach ($this->bindings as $interface => $class) {
            $this->app->bind($interface, $class);
        }
    }

    public function boot(): void {}
}
