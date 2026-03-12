<?php

declare(strict_types=1);

namespace Application\User\Services\Contracts;

use Domain\User\DTO\UserDTO;
use Domain\User\Models\User;

interface RegistrationServiceContract
{
    public function register(UserDTO $userDTO): UserDTO;

    public function sendEmailVerificationNotification(User $user): void;
}
