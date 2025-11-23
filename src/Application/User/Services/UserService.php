<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\DTO\UserDTO;
use Domain\User\Models\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserService
{
    /**
     * Get UserDTO for current authenticated user.
     *
     * @return UserDTO
     *
     * @throws AccessDeniedHttpException
     */
    public function me(): UserDTO
    {
        /** @var User $user */
        if (! $user = auth()->user()) {
            throw new AccessDeniedHttpException();
        }

        return UserDTO::from($user);
    }
}
