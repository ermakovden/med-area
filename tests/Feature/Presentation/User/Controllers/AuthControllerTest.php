<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\User\Controllers;

use Application\User\DTO\UserDTO;
use Domain\User\Factories\UserFactory;
use Domain\User\Models\User;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_login_success(): void
    {
        // Data for testing
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->password = $this->userPassword;

        // User for testing
        User::create($userData->toArray());

        // Send API Request
        $response = $this->post(route('api.auth.login'), $userData->only('nickname', 'password')->toArray());

        // Check asserts
        $response->assertOk();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    public function test_login_bad_nickname(): void
    {
        // Data for testing
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->password = $this->userPassword;

        // User for testing
        User::create($userData->toArray());

        // Data for request
        $data = [
            'nickname' => fake()->userName(), // false value
            'password' => $userData->password, // true value
        ];

        // Send API Request
        $response = $this->post(route('api.auth.login'), $data);

        // Check asserts
        $response->assertBadRequest();
        $response->assertClientError();
        $response->assertJson(['message' => 'Nickname or password incorrect.']);
    }

    public function test_login_bad_password(): void
    {
        // Data for testing
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->password = $this->userPassword;

        // User for testing
        User::create($userData->toArray());

        // Data for request
        $data = [
            'nickname' => $userData->nickname, // true value
            'password' => fake()->password(), // false value
        ];

        // Send API Request
        $response = $this->post(route('api.auth.login'), $data);

        // Check asserts
        $response->assertBadRequest();
        $response->assertClientError();
        $response->assertJson(['message' => 'Nickname or password incorrect.']);
    }

    public function test_refresh_success(): void
    {
        // User for testing
        $user = $this->getUser();

        // Send API Request
        $response = $this->actingAs($user)->post(route('api.auth.refresh'));

        // Check asserts
        $response->assertOk();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

    public function test_refresh_unauth(): void
    {
        // User for testing
        $user = $this->getUser();

        // Send API Request
        $response = $this->post(route('api.auth.refresh'));

        // Check asserts
        $response->assertUnauthorized();
    }

    public function test_refresh_someone_else_token(): void
    {
        // Users for testing
        $user = $this->getUser();
        $user2 = $this->getUser();

        // Send API Request and login as $user (1)
        $response = $this->actingAs($user)->post(route('api.auth.refresh'), headers: [
            'Authentication' => auth()->tokenById($user2->getAuthIdentifier()), // Use token another user
        ]);

        // Check asserts
        $response->assertInternalServerError();
    }
}
