<?php

declare(strict_types=1);

namespace Tests\Feature\Presentation\Analys\Controllers;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Enums\Analys;
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

    public function test_user_analysis_index_success(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Data for testing
        $count = 3;
        $factory = new UserAnalysFactory();

        // Data with another values for testing filters
        UserAnalys::factory(10)
            ->for($user)
            ->createMany();

        // Delete analys_id for testing filters
        UserAnalys::query()->whereAnalysId(Analys::D3->value)->delete();

        // Data UserAnalys Models for searching with filters (user_id, analys_id)
        for ($i = 0; $i < $count; $i++) {
            UserAnalys::query()->create(array_merge($factory->definition(), [
                'user_id' => $user->id,
                'analys_id' => Analys::D3,
            ]));
        }

        // Filters for testing
        $filters = FilterUserAnalysDTO::from([
            'user_ids' => [$user->id],
            'analys_ids' => [Analys::D3],
        ]);

        // Send API Request with filters
        $response = $this->get(route('api.users.analysis.index', array_merge([
            'userId' => $user->id,
        ], $filters->toArray())));

        // Check asserts
        $response->assertOk();
        $response->assertJsonCount($count);
    }

    public function test_user_analysis_index_forbidden_unreal_user_id(): void
    {
        // Auth user for testing
        $user = $this->authUser();
        $user2 = $this->getUser();

        // Filters for testing
        $filters = FilterUserAnalysDTO::from(['user_ids' => [$user->id]]);

        // Send API Request with filters and another user_id in url
        $response = $this->get(route('api.users.analysis.index', array_merge([
            'userId' => $user2->id,
        ], $filters->toArray())));

        // Check assert forbidden, 403 http code
        $response->assertForbidden();
    }

    public function test_user_analysis_index_forbidden_filter_user_ids(): void
    {
        // Auth user for testing
        $user = $this->authUser();
        $user2 = $this->getUser();

        // Filters with another user_id for testing
        $filters = FilterUserAnalysDTO::from(['user_ids' => [$user->id, $user2->id]]);

        // Send API Request with bad filters
        $response = $this->get(route('api.users.analysis.index', array_merge([
            'userId' => $user->id,
        ], $filters->toArray())));

        // Check assert forbidden, 403 http code
        $response->assertForbidden();
    }
}
