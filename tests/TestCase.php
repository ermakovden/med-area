<?php

declare(strict_types=1);

namespace Tests;

use Application\User\DTO\UserDTO;
use Domain\User\Factories\UserFactory;
use Domain\User\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseMigrations;

    protected string $userPassword = 'GbUTPsq894b!fM1';

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('config:clear');
        $this->artisan('migrate:fresh');
        $this->artisan('db:seed');

        $this->withHeader('accept', 'application/json');
    }

    /**
     * Get User and Authenticate
     *
     * @return User
     */
    public function authUser(): User
    {
        $user = $this->getUser();

        auth()->login($user);

        return $user;
    }

    /**
     * Get User model with verified email
     *
     * @return User
     */
    protected function getUser(): User
    {
        $userDTO = UserDTO::from((new UserFactory())->definition());
        $userDTO->password = $this->userPassword;

        /** @var User $userModel */
        $userModel = User::create($userDTO->toArray());

        $userModel->markEmailAsVerified();

        return $userModel;
    }
}
