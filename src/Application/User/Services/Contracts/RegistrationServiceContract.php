<?php

declare(strict_types=1);

namespace Application\User\Services\Contracts;

use Application\User\DTO\UserDTO;

interface RegistrationServiceContract
{
    public function register(UserDTO $userDTO): UserDTO;
}
