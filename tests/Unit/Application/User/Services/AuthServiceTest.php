<?php

declare(strict_types=1);

namespace Tests\Unit\Application\User\Services;

use Application\User\DTO\UserDTO;
use Application\User\Services\AuthService;
use Domain\User\Enums\TokenType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    /**
     * Сontains the service being tested
     *
     * @var AuthService
     */
    protected AuthService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new AuthService();
    }

    public function test_login_success(): void
    {
        // User for testing
        $user = $this->getUser();

        // Data for testing
        $credentials = UserDTO::from([
            'nickname' => $user->nickname,
            'password' => $this->userPassword,
        ]);

        // Result from method of service
        $result = $this->service->login($credentials);

        // Сheck that the result matches
        $this->assertTrue($result->isNotEmptyValue('access_token'));
        $this->assertSame(TokenType::BEARER, $result->token_type);
        $this->assertIsInt($result->expires_in);

        // Chech that user authenticated
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_bad_nickname(): void
    {
        // User for testing
        $user = $this->getUser();

        // Data for testing
        $credentials = UserDTO::from([
            'nickname' => 'testing',
            'password' => $this->userPassword,
        ]);

        // Сheck that was exception
        $this->expectException(BadRequestHttpException::class);

        // Use method of service for try to login
        $this->service->login($credentials);
    }

    public function test_login_bad_password(): void
    {
        // User for testing
        $user = $this->getUser();

        // Data for testing
        $credentials = UserDTO::from([
            'nickname' => $user->nickname,
            'password' => '12345',
        ]);

        // Сheck that was exception
        $this->expectException(BadRequestHttpException::class);

        // Use method of service for try to login
        $this->service->login($credentials);
    }

    public function test_refresh_token_success(): void
    {
        // User for testing
        $user = $this->getUser();

        // Login user
        $this->actingAs($user);
        $initialToken = auth()->login($user);

        // Result from method of service
        $result = $this->service->refreshToken();

        // Сheck that the result matches
        $this->assertTrue($result->isNotEmptyValue('access_token'));
        $this->assertNotEquals($initialToken, $result->access_token);
        $this->assertSame(TokenType::BEARER, $result->token_type);
        $this->assertIsInt($result->expires_in);

        // Chech that user authenticated
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_success(): void
    {
        // User for testing
        $user = $this->getUser();

        // Login user
        $this->actingAs($user);
        auth()->login($user);

        // Logout user with use service
        $this->service->logout();

        // Check assert that user is not authenticated, has been log out
        $this->assertFalse($this->isAuthenticated());
    }
}
