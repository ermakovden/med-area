<?php

declare(strict_types=1);

namespace Domain\User\Repositories;

use Application\User\DTO\UserDTO;
use Domain\User\Models\User;
use Shared\Repositories\Contracts\BaseRepositoryContract;

/**
 * @method User create(UserDTO $dto)
 */
interface UserRepositoryContract extends BaseRepositoryContract
{
    public function getById(string|int $id): ?UserDTO;
}
