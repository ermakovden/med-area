<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\User\DTO\UserDTO;
use Domain\User\Models\User;
use Infrastructure\Repositories\Contracts\UserRepositoryContract;

class UserRepository implements UserRepositoryContract
{
    /**
     * @var class-string<User>
     */
    protected $model = User::class;

    public function create(UserDTO $userDTO): UserDTO
    {
        /** @var User $user */
        $user = $this->model::create($userDTO->toArray());

        return UserDTO::from($user);
    }
}
