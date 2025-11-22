<?php

declare(strict_types=1);

namespace Tests;

use Application\User\DTO\UserDTO;
use Domain\User\Factories\UserFactory;
use Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Get User model with verified email
     *
     * @return User
     */
    protected function getUser(): User
    {
        $userDTO = UserDTO::from((new UserFactory())->definition());

        /** @var User $userModel */
        $userModel = User::create($userDTO->toArray());

        $userModel->markEmailAsVerified();

        return $userModel;
    }
}
