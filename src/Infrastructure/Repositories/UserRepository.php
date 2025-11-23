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

    /**
     * Create User
     *
     * @param UserDTO $userDTO
     * @return UserDTO
     */
    public function create(UserDTO $userDTO): UserDTO
    {
        /** @var User $user */
        $user = $this->model::create($userDTO->toArray());

        return UserDTO::from($user);
    }


    /**
     * Get UserDTO model by id
     *
     * @param string|integer $id
     * @return UserDTO|null
     */
    public function getById(string|int $id): ?UserDTO
    {
        if (! $user = $this->model::find($id)) {
            return null;
        }

        return UserDTO::from($user);
    }
}
