<?php

declare(strict_types=1);

namespace Application\User\Services\Contracts;

use Application\User\DTO\UserDTO;

interface UserServiceContract
{
    public function me(): UserDTO;

    public function getById(string|int $id): UserDTO;
}
