<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Domain\User\Models\User;
use Application\User\DTO\UserDTO;
use Shared\Repositories\Contracts\BaseRepositoryContract;

/**
 * @method User create(UserDTO $dto)
 */
interface UserRepositoryContract extends BaseRepositoryContract
{
    public function getById(string|int $id): ?UserDTO;
}
