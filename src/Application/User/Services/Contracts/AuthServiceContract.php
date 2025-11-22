<?php

declare(strict_types=1);

namespace Application\User\Services\Contracts;

use Application\User\DTO\TokensResponse;
use Application\User\DTO\UserDTO;

interface AuthServiceContract
{
    public function login(UserDTO $userDTO): TokensResponse;
}
