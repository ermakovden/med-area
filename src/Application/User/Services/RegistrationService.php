<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\DTO\UserDTO;
use Application\User\Services\Contracts\RegistrationServiceContract;
use Infrastructure\Repositories\Contracts\UserRepositoryContract;

class RegistrationService implements RegistrationServiceContract
{
    public function __construct(
        protected readonly UserRepositoryContract $userRepository,
    ) {}

    /**
     * Create new User model
     * Send email message for confirmation of registration
     *
     * @param UserDTO $userDTO
     * @return UserDTO
     */
    public function register(UserDTO $userDTO): UserDTO
    {
        $userDTO = $this->userRepository->create($userDTO);

        // TODO: send confirm to email

        return $userDTO;
    }
}
