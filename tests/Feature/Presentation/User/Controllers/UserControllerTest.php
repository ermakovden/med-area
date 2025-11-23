<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\User\Controllers;

use Application\User\DTO\UserDTO;
use Presentation\User\Resources\UserResource;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function test_me_success(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Send API Request
        $response = $this->actingAs($user)->get(route('api.users.me'));

        // Check response status
        $response->assertOk();

        // Check that user ids is assert
        $this->assertSame($user->id, $response->json('id'));

        // Resource for assert json data
        $resource = new UserResource(UserDTO::from($user));
        $response->assertJson(json_decode($resource->toJson(), true));
    }

    public function test_me_unauth(): void
    {
        // User for testing
        $this->getUser();

        // Send API Request
        $response = $this->get(route('api.users.me'));

        // Check response status
        $response->assertUnauthorized();
    }
}
