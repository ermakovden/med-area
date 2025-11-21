<?php

declare(strict_types=1);

return [
    Application\Providers\ApplicationServiceProvider::class,
    Domain\Providers\DomainServiceProvider::class,
    Domain\Providers\EventServiceProvider::class,
    Infrastructure\Providers\InfrastructureServiceProvider::class,
];
