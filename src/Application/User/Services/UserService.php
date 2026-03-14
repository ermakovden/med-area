<?php

declare(strict_types=1);

namespace Application\User\Services;

use Domain\User\DTO\UserDTO;
use Application\User\Services\Contracts\UserServiceContract;
use Domain\User\Models\User;
use Domain\User\Repositories\UserRepositoryContract;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService implements UserServiceContract
{
    public function __construct(
        protected readonly UserRepositoryContract $userRepository,
    ) {}

    /**
     * Get UserDTO for current authenticated user.
     *
     * @return UserDTO
     *
     * @throws AccessDeniedHttpException
     */
    public function me(): UserDTO
    {
        logger()->debug('[UserService.me] starting');

        if (! $user = auth()->user()) {
            throw new AccessDeniedHttpException();
        }
        /** @var User $user */

        logger()->debug('[UserService.me] returning user', ['user_id' => $user->getKey()]);

        return UserDTO::from($user);
    }

    /**
     * Get UserDTO by user id
     *
     * @param string|integer $id
     * @return UserDTO
     *
     * @throws NotFoundHttpException
     */
    public function getById(string|int $id): UserDTO
    {
        logger()->debug('[UserService.getById] starting', ['id' => $id]);

        if (! $user = $this->userRepository->getById($id)) {
            throw new NotFoundHttpException();
        }

        logger()->debug('[UserService.getById] returning user', ['user_id' => $user->id]);

        return UserDTO::from($user);
    }
}
