<?php

declare(strict_types=1);

namespace Domain\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [];

    public function register(): void
    {
        foreach ($this->bindings as $interface => $class) {
            $this->app->bind($interface, $class);
        }

        /** @phpstan-ignore-next-line */
        Factory::guessFactoryNamesUsing(function (string $modelName): string {
            if (str_starts_with($modelName, 'Domain\\')) {
                $factoryNamespace = str_replace('\\Models\\', '\\Factories\\', $modelName);
                $factoryClass = $factoryNamespace . 'Factory';

                if (class_exists($factoryClass)) {
                    return $factoryClass;
                }
            }

            return 'Database\\Factories\\' . class_basename($modelName) . 'Factory';
        });
    }

    public function boot(): void {}
}
