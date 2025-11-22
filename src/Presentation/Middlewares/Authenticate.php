<?php

declare(strict_types=1);

namespace Presentation\Middlewares;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as MiddlewareAuthenticate;

class Authenticate extends MiddlewareAuthenticate
{
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException('Unauthenticated.', $guards);
    }
}
