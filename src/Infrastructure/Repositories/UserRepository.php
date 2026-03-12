<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Domain\User\DTO\UserDTO;
use Domain\User\Models\User;
use Domain\User\Repositories\UserRepositoryContract;
use Shared\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryContract
{
    /**
     * @var class-string<User>
     */
    protected string $model = User::class;

    /**
     * Get UserDTO model by id
     *
     * @param string|integer $id
     * @return UserDTO|null
     */
    public function getById(string|int $id): ?UserDTO
    {
        logger()->debug('[UserRepository.getById] starting query', ['id' => $id]);

        if (! $user = $this->model::find($id)) {
            logger()->debug('[UserRepository.getById] record not found', ['id' => $id]);

            return null;
        }

        logger()->debug('[UserRepository.getById] record found', ['id' => $id]);

        return UserDTO::from($user);
    }
}
