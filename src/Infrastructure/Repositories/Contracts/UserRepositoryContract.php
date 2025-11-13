<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Application\User\DTO\UserDTO;

interface UserRepositoryContract
{
    public function create(UserDTO $userDTO): UserDTO;
}
