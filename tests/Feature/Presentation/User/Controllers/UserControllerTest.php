<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\User\Controllers;

use Domain\User\Factories\UserFactory;
use Presentation\User\Controllers\UserController;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    protected UserController $userController;

    public function test_register_success(): void
    {
        // Data for Request
        $userData = (new UserFactory())->definition();
        $userData['password_confirmation'] = $userData['password'];

        // Send API Request
        $response = $this->post(route('api.users.register'), $userData);

        // Check asserts
        $response->assertOk();
    }

    public function test_register_dublicates(): void
    {
        // Data for Request
        $userData = (new UserFactory())->definition();
        $userData['password_confirmation'] = $userData['password'];

        // Send API Requests
        $this->post(route('api.users.register'), $userData);
        $response = $this->post(route('api.users.register'), $userData);

        // Check asserts
        $response->assertStatus(422);
        $response->assertInvalid(['nickname', 'email']);
    }

    public function test_register_bad_password(): void
    {
        // Data for Request
        $userData = (new UserFactory())->definition();

        // Create bad password
        $password = '12345';
        $userData['password'] = $password;
        $userData['password_confirmation'] = $password;

        // Send API Request
        $response = $this->post(route('api.users.register'), $userData);

        // Check asserts
        $response->assertStatus(422);
        $response->assertInvalid(['password']);
    }

    public function test_register_password_unconfirmation(): void
    {
        // Data for Request
        $userData = (new UserFactory())->definition();
        $userData['password_confirmation'] = '12345';

        // Send API Request
        $response = $this->post(route('api.users.register'), $userData);

        // Check asserts
        $response->assertStatus(422);
        $response->assertInvalid(['password']);
    }
}
