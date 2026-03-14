<?php

declare(strict_types=1);

namespace Domain\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
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

        Factory::guessFactoryNamesUsing(
            /**
             * @param class-string<Model> $modelName
             * @return class-string<Factory<Model>>
             */
            function (string $modelName): string {
                if (str_starts_with($modelName, 'Domain\\')) {
                    $factoryNamespace = str_replace('\\Models\\', '\\Factories\\', $modelName);
                    $factoryClass = $factoryNamespace . 'Factory';

                    if (class_exists($factoryClass)) {
                        /** @var class-string<Factory<Model>> $factoryClass */
                        return $factoryClass;
                    }
                }

                /** @var class-string<Factory<Model>> $fallback */
                $fallback = 'Database\\Factories\\' . class_basename($modelName) . 'Factory';

                return $fallback;
            }
        );
    }

    public function boot(): void {}
}
