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

    public function test_get_by_id_success(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Send API Request
        $response = $this->actingAs($user)->get(route('api.users.show', ['id' => $user->id]));

        // Check response status
        $response->assertOk();

        // Check that user ids is assert
        $this->assertSame($user->id, $response->json('id'));

        // Resource for assert json data
        $resource = new UserResource(UserDTO::from($user));
        $response->assertJson(json_decode($resource->toJson(), true));
    }

    public function test_get_by_id_not_found(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Delete user for result code 404
        $user->delete();

        // Send API Request
        $response = $this->actingAs($user)->get(route('api.users.show', ['id' => $user->id]));

        // Check response status
        $response->assertNotFound();
    }

    public function test_get_by_id_unauth(): void
    {
        // User for testing
        $user = $this->getUser();

        // Send API Request
        $response = $this->get(route('api.users.show', ['id' => $user->id]));

        // Check response status
        $response->assertUnauthorized();
    }

    public function test_get_by_id_forbidden(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Fake user id for testing
        $fakeUUID = fake()->uuid();

        // Send API Request
        $response = $this->actingAs($user)->get(route('api.users.show', ['id' => $fakeUUID]));

        // Check response status
        $response->assertForbidden();
    }
}
