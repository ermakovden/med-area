<?php

declare(strict_types=1);

namespace Tests\Unit\Application\User\Services;

use Application\User\DTO\UserDTO;
use Application\User\Services\UserService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    /**
     * Сontains the service being tested
     *
     * @var UserService
     */
    protected UserService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = new UserService();
    }

    public function test_me_success(): void
    {
        // Auth user for testing
        $user = $this->authUser();
        $userDTO = UserDTO::from($user);

        // Result from method of service
        $result = $this->service->me();

        // Chech that user authenticated
        $this->assertAuthenticatedAs($user);

        // Сheck that the result matches
        $this->assertInstanceOf(UserDTO::class, $result);
        $this->assertJson($userDTO->toJson(), $result->toJson());
    }

    public function test_me_forbidden(): void
    {
        // User for testing
        $this->getUser();

        // Exptect exception for service method
        $this->expectException(AccessDeniedHttpException::class);

        // Call method of service
        $this->service->me();

        // Check assert that user is not authenticated
        $this->assertFalse($this->isAuthenticated());
    }

    public function test_get_by_id_success(): void
    {
        // User for testing
        $user = $this->getUser();
        $userDTO = UserDTO::from($user);

        // Result from method of service
        $result = $this->service->getById($userDTO->id);

        // Сheck that the result matches
        $this->assertInstanceOf(UserDTO::class, $result);
        $this->assertJson($userDTO->toJson(), $result->toJson());
    }

    public function test_get_by_id_not_found(): void
    {
        // User for testing
        $this->getUser();

        // Fake UUID for testing
        $fakeUUID = fake()->uuid();

        // Сheck that was exception
        $this->expectException(NotFoundHttpException::class);

        // Result from method of service
        $this->service->getById($fakeUUID);
    }
}
