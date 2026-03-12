<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;
use Infrastructure\Repositories\AnalysRepository;
use Domain\Analys\Repositories\AnalysRepositoryContract;
use Domain\File\Repositories\FileRepositoryContract;
use Domain\AI\Recognise\Repositories\RecogniseRequestRepositoryContract;
use Domain\Analys\Repositories\UserAnalysRepositoryContract;
use Domain\User\Repositories\UserRepositoryContract;
use Infrastructure\Repositories\FileRepository;
use Infrastructure\Repositories\RecogniseRequestRepository;
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

        // AI
        RecogniseRequestRepositoryContract::class => RecogniseRequestRepository::class,

        // File
        FileRepositoryContract::class => FileRepository::class,

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
