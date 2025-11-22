<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\User\Controllers;

use Application\User\DTO\UserDTO;
use Domain\User\Factories\UserFactory;
use Domain\User\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Infrastructure\Notifications\User\EmailVerificationNotification;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    public function test_register_success(): void
    {
        // Data for Request
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->email_verified_at = null;
        $userData->password_confirmation = $userData->password;

        // Send API Request
        $response = $this->post(route('api.users.register'), $userData->toArray());

        // Check asserts
        $response->assertOk();
        $this->assertDatabaseHas(User::class, $userData->only('nickname', 'email', 'email_verified_at')->toArray());
    }

    public function test_register_dublicates(): void
    {
        // Data for Request
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->password_confirmation = $userData->password;

        // Send API Requests
        $this->post(route('api.users.register'), $userData->toArray());
        $response = $this->post(route('api.users.register'), $userData->toArray());

        // Check asserts
        $response->assertUnprocessable();
        $response->assertInvalid(['nickname', 'email']);
    }

    public function test_register_bad_password(): void
    {
        // Data for Request
        $userData = UserDTO::from((new UserFactory())->definition());

        // Create bad password
        $password = '12345';
        $userData->password = $password;
        $userData->password_confirmation = $password;

        // Send API Request
        $response = $this->post(route('api.users.register'), $userData->toArray());

        // Check asserts
        $response->assertUnprocessable();
        $response->assertInvalid(['password']);
    }

    public function test_register_password_unconfirmation(): void
    {
        // Data for Request
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->password_confirmation = '12345';

        // Send API Request
        $response = $this->post(route('api.users.register'), $userData->toArray());

        // Check asserts
        $response->assertUnprocessable();
        $response->assertInvalid(['password']);
    }

    public function test_verify_success(): void
    {
        // User Data
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->email_verified_at = null;

        /** @var User $userModel */
        $userModel = User::create($userData->toArray());

        // Notification for User
        $notification = new EmailVerificationNotification($userModel);
        $verificationUrl = $notification->verificationUrl($userModel);

        // Check assert that email_verification_at is null
        $this->assertNull($userModel->email_verified_at);

        // Login and go to verify link
        $this->actingAs($userModel)->get($verificationUrl);

        // Check assert that user has been verified
        $this->assertNotNull($userModel->email_verified_at);
    }

    public function test_verify_unreal_link(): void
    {
        // User Data
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->email_verified_at = null;

        /** @var User $userModel */
        $userModel = User::create($userData->toArray());

        // Fake verification url
        $verificationUrl = fake()->url();

        // Check assert that email_verification_at is null
        $this->assertNull($userModel->email_verified_at);

        // Login and go to unreal verify link
        $this->actingAs($userModel)->get($verificationUrl);

        // Check assert that user is unverified
        $this->assertNull($userModel->email_verified_at);
    }

    public function test_verify_unauth(): void
    {
        // User Data
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->email_verified_at = null;

        /** @var User $userModel */
        $userModel = User::create($userData->toArray());

        // Notification for User
        $notification = new EmailVerificationNotification($userModel);
        $verificationUrl = $notification->verificationUrl($userModel);

        // Check assert that email_verification_at is null
        $this->assertNull($userModel->email_verified_at);

        // Go to verify link
        $this->get($verificationUrl);

        // Check assert that user is unverified
        $this->assertNull($userModel->email_verified_at);
    }

    public function test_verify_someone_else_link(): void
    {
        // User Data
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->email_verified_at = null;

        $userData2 = UserDTO::from((new UserFactory())->definition());
        $userData2->email_verified_at = null;

        /** @var User $userModel */
        $userModel = User::create($userData->toArray());

        /** @var User $userModel */
        $userModel2 = User::create($userData2->toArray());

        // Notification for User
        $notification = new EmailVerificationNotification($userModel2);
        $verificationUrl = $notification->verificationUrl($userModel2);

        // Check asserts that email_verification_at is null
        $this->assertNull($userModel->email_verified_at);
        $this->assertNull($userModel2->email_verified_at);

        // Login and go to someone else verify link
        $this->actingAs($userModel)->get($verificationUrl);

        // Check asserts that user is unverified
        $this->assertNull($userModel->email_verified_at);
        $this->assertNull($userModel2->email_verified_at);
    }

    public function test_send_email_verification_success(): void
    {
        // User Data
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->email_verified_at = null;

        /** @var User $userModel */
        $userModel = User::create($userData->toArray());

        // Send API Request
        $response = $this->actingAs($userModel)->get(route('verification.send'));

        // Check asserts that email notification sended
        $response->assertOk();

        // Check that the notification was sent
        Notification::assertSentTo($userModel, VerifyEmail::class);
    }

    public function test_send_email_verification_unauth(): void
    {
        // User Data
        $userData = UserDTO::from((new UserFactory())->definition());
        $userData->email_verified_at = null;

        /** @var User $userModel */
        $userModel = User::create($userData->toArray());

        // Send API Request
        $response = $this->get(route('verification.send'));

        // Check assert unauth
        $response->assertUnauthorized();

        // Check that the notification was not sent
        Notification::assertNotSentTo($userModel, VerifyEmail::class);
    }

    public function test_send_email_verification_already_verified(): void
    {
        // User Data
        $userData = UserDTO::from((new UserFactory())->definition());

        /** @var User $userModel */
        $userModel = User::create($userData->toArray());

        // Send API Request
        $response = $this->actingAs($userModel)->get(route('verification.send'));

        // Check assert 200
        $response->assertOk();

        // Check that the notification was not sent
        Notification::assertNotSentTo($userModel, VerifyEmail::class);
    }
}
