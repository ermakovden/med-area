<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Analys\Controllers;

use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Factories\UserAnalysFactory;
use Domain\Analys\Models\UserAnalys;
use Tests\TestCase;

class UserAnalysControllerTest extends TestCase
{
    public function test_user_analysis_create_success(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Data for testing
        $factory = new UserAnalysFactory();
        $dto = CreateUserAnalysisRequestDTO::from([
            'analysis' => [
                UserAnalysDTO::from(array_merge($factory->definition(), ['user_id' => $user->id])),
                UserAnalysDTO::from(array_merge($factory->definition(), ['user_id' => $user->id])),
            ],
        ]);

        // Check that records missing in DB
        foreach ($dto->analysis as $analys) {
            $this->assertDatabaseMissing(UserAnalys::class, $analys->toArray());
        }

        // Send API Request
        $response = $this->post(route('api.users.analysis.create', ['userId' => $user->id]), $dto->toArray());

        // Check asserts
        $response->assertCreated();

        // Check that records in DB
        foreach ($dto->analysis as $analys) {
            $this->assertDatabaseHas(UserAnalys::class, $analys->toArray());
        }
    }

    public function test_user_analysis_create_validation_unauth(): void
    {
        // Auth user for testing
        $user = $this->getUser();

        // Send API Request
        $response = $this->post(route('api.users.analysis.create', ['userId' => $user->id]));

        // Check asserts
        $response->assertUnauthorized();
    }

    public function test_user_analysis_create_validation_missing_values(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Data for testing
        $userAnalysDTO = UserAnalysDTO::from(['analys_name' => '']);
        $dto = CreateUserAnalysisRequestDTO::from([
            'analysis' => [$userAnalysDTO, $userAnalysDTO],
        ]);

        // Send API Request
        $response = $this->post(route('api.users.analysis.create', ['userId' => $user->id]), $dto->toArray());

        // Check asserts
        $response->assertUnprocessable();
        $response->assertInvalid(['analysis.0.user_id', 'analysis.0.analys_id', 'analysis.0.data']);

        // Check that records missing in DB
        foreach ($dto->analysis as $analys) {
            $this->assertDatabaseMissing(UserAnalys::class, $analys->toArray());
        }
    }

    public function test_user_analysis_create_validation_errors(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Data for testing
        $userAnalysDTO = UserAnalysDTO::from([
            'user_id' => $user->id, // skip 403 errors
        ]);
        $dto = CreateUserAnalysisRequestDTO::from([
            'analysis' => [$userAnalysDTO],
        ]);

        // Send API Request
        $response = $this->post(route('api.users.analysis.create', ['userId' => $user->id]), array_merge(
            $dto->toArray(),
            [
                'data' => 'str1ng1', // string, must be numeric
                'analys_id' => 0, // unreal analys id
            ]
        ));

        // Check asserts
        $response->assertUnprocessable();
        $response->assertInvalid(['analysis.0.analys_id', 'analysis.0.data']);

        // Check that records missing in DB
        foreach ($dto->analysis as $analys) {
            $this->assertDatabaseMissing(UserAnalys::class, $analys->toArray());
        }
    }

    public function test_user_analysis_create_validation_unreal_user_id(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Data for testing
        $factory = new UserAnalysFactory();
        $dto = CreateUserAnalysisRequestDTO::from([
            'analysis' => [
                UserAnalysDTO::from(array_merge($factory->definition(), ['user_id' => fake()->uuid()])),
                UserAnalysDTO::from(array_merge($factory->definition(), ['user_id' => fake()->uuid()])),
            ],
        ]);

        // Send API Request
        $response = $this->post(route('api.users.analysis.create', ['userId' => $user->id]), $dto->toArray());

        // Check asserts
        $response->assertUnprocessable();
        $response->assertInvalid(['analysis.0.user_id', 'analysis.1.user_id']);
        $response->assertJsonValidationErrors([
            'analysis.0.user_id' => [
                'The analysis.0.user_id must match the authenticated user ID.',
                'The analysis.0.user_id must match in the url user ID.',
            ],
        ]);

        // Check that records missing in DB
        foreach ($dto->analysis as $analys) {
            $this->assertDatabaseMissing(UserAnalys::class, $analys->toArray());
        }
    }

    public function test_user_analysis_create_forbidden(): void
    {
        // Auth user for testing
        $user = $this->authUser();
        $user2 = $this->getUser();

        // Data for testing
        $factory = new UserAnalysFactory();
        $dto = CreateUserAnalysisRequestDTO::from([
            'analysis' => [
                UserAnalysDTO::from(array_merge($factory->definition(), ['user_id' => $user2->id])), // using another user id
                UserAnalysDTO::from(array_merge($factory->definition(), ['user_id' => $user2->id])), // using another user id
            ],
        ]);

        // Send API Request
        $response = $this->post(route('api.users.analysis.create', ['userId' => $user2->id]), $dto->toArray());

        // Check asserts
        $response->assertForbidden();

        // Check that records missing in DB
        foreach ($dto->analysis as $analys) {
            $this->assertDatabaseMissing(UserAnalys::class, $analys->toArray());
        }
    }
}
