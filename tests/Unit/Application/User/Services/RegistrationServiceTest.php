<?php

declare(strict_types=1);

namespace Tests\Unit\Application\User\Services;

use Application\User\DTO\UserDTO;
use Application\User\Services\RegistrationService;
use Domain\User\Factories\UserFactory;
use Domain\User\Models\User;
use Illuminate\Support\Facades\Notification;
use Infrastructure\Notifications\User\EmailVerificationNotification;
use Infrastructure\Repositories\Contracts\UserRepositoryContract;
use Mockery\MockInterface;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    /**
     * Сontains the service being tested
     *
     * @var RegistrationService
     */
    protected RegistrationService $service;

    /**
     * Mock: Infrastructure\Repositories\Contracts\UserRepositoryContract
     *
     * @var MockInterface
     */
    protected MockInterface $userRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = $this->mock(UserRepositoryContract::class);

        $this->service = new RegistrationService($this->userRepositoryMock);
    }

    public function test_register_success(): void
    {
        // Data for testing
        $userDTO = UserDTO::from((new UserFactory())->definition());
        $userDTO->email_verified_at = null;

        // Mocks
        $createdUser = User::create($userDTO->toArray());
        $this->userRepositoryMock->shouldReceive('create')
            ->once()
            ->with($userDTO)
            ->andReturn(UserDTO::from($createdUser));

        // Result from method of service
        $result = $this->service->register($userDTO);

        // Сheck that the result matches
        $this->assertJson($userDTO->toJson(), $result->only('nickname', 'email')->toJson());
        $this->assertNull($result->email_verified_at);

        // Check that the record has appeared in the DB
        $this->assertDatabaseHas(User::class, $userDTO->except('remember_token')->toArray());
    }

    public function test_send_email_verification_notification_success(): void
    {
        // Data for testing
        $userDTO = UserDTO::from((new UserFactory())->definition());
        $userDTO->email_verified_at = null;

        $createdUser = User::create($userDTO->toArray());

        // Fake notification service
        Notification::fake();

        // Send email notification
        $this->service->sendEmailVerificationNotification($createdUser);

        // Check that the notification was sent
        Notification::assertSentTo($createdUser, EmailVerificationNotification::class);
    }

    public function test_send_email_verification_notification_already_verified(): void
    {
        // Data for testing
        $userDTO = UserDTO::from((new UserFactory())->definition());

        /** @var User $createdUser */
        $createdUser = User::create($userDTO->toArray());
        $createdUser->markEmailAsVerified();

        // Fake notification service
        Notification::fake();

        // Send email notification
        $this->service->sendEmailVerificationNotification($createdUser);

        // Check that the notification not was sent
        Notification::assertNotSentTo($createdUser, EmailVerificationNotification::class);
    }
}
