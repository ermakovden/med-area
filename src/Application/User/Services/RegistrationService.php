<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\DTO\UserDTO;
use Application\User\Services\Contracts\RegistrationServiceContract;
use Domain\User\Events\UserRegistered;
use Domain\User\Models\User;
use Infrastructure\Notifications\User\EmailVerificationNotification;
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
        // Create new User model
        $userDTO = $this->userRepository->create($userDTO);

        // Send email message for confirmation of registration
        $userModel = User::whereId($userDTO->id)->first();

        event(new UserRegistered($userModel));

        return $userDTO;
    }

    /**
     * Send email message for confirmation of registration
     *
     * @param User $user
     * @return void
     */
    public function sendEmailVerificationNotification(User $user): void
    {
        if (! $user->hasVerifiedEmail()) {
            $user->notify(new EmailVerificationNotification($user));
        }
    }
}
