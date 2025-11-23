<?php

declare(strict_types=1);

namespace Application\User\Services\Contracts;

use Application\User\DTO\TokenResponse;
use Application\User\DTO\UserDTO;

interface AuthServiceContract
{
    public function login(UserDTO $userDTO): TokenResponse;

    public function refreshToken(bool $forceForever = false, bool $resetClaims = false): TokenResponse;

    public function logout(): void;
}
