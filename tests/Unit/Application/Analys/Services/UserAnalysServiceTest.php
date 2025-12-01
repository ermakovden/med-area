<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Analys\Services;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Application\Analys\Services\UserAnalysService;
use Domain\Analys\Factories\UserAnalysFactory;
use Domain\Analys\Models\UserAnalys;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;
use Mockery\MockInterface;
use Shared\Exceptions\ServerErrorException;
use Tests\TestCase;

class UserAnalysServiceTest extends TestCase
{
    /**
     * Сontains the service being tested
     *
     * @var UserAnalysService
     */
    protected UserAnalysService $service;

    /**
     * Mock: Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract
     *
     * @var MockInterface
     */
    protected MockInterface $userAnalysRepositoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->userAnalysRepositoryMock = $this->mock(UserAnalysRepositoryContract::class);

        $this->service = new UserAnalysService($this->userAnalysRepositoryMock);
    }

    public function test_create_user_analysis_success(): void
    {
        // Data for testing
        $factory = new UserAnalysFactory();
        $dto = CreateUserAnalysisRequestDTO::from([
            'analysis' => [
                UserAnalysDTO::from($factory->definition()),
                UserAnalysDTO::from($factory->definition()),
            ],
        ]);

        // Mocks
        foreach ($dto->analysis as $analys) {
            $this->userAnalysRepositoryMock->shouldReceive('create')
                ->once()
                ->with($analys)
                ->andReturn(new UserAnalys($analys->toArray()));
        }

        // Result from method of service
        $result = $this->service->createUserAnalysis($dto);

        // Check assert result
        $this->assertIsArray($result);
        $this->assertInstanceOf(UserAnalysDTO::class, $result[0]);
        $this->assertCount(count($dto->analysis), $result);

    }

    public function test_create_user_analysis_missing_values(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Data for testing
        $factory = new UserAnalysFactory();
        $userAnalys = UserAnalysDTO::from($factory->definition());
        $userAnalys->user_id = $user->id;

        // Data for testing - missing user_id
        $dto = CreateUserAnalysisRequestDTO::from([
            'analysis' => [
                $userAnalys,
                $userAnalys->except('user_id'),
            ],
        ]);

        // Сheck that was exception
        $this->expectException(ServerErrorException::class);

        // Mocks
        $this->userAnalysRepositoryMock->shouldReceive('create')
            ->with($dto->analysis[0])
            ->andThrow(ServerErrorException::class);

        // Call method of service
        $this->service->createUserAnalysis($dto);
    }

    public function test_get_user_analysis_filter_by_user_ids(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Init models with another user_id for search

        // Init model for search
        $factory = new UserAnalysFactory();
        $dto = UserAnalysDTO::from($factory->definition());
        $dto->user_id = $user->id;
        $userAnalysModel = UserAnalys::query()->create($dto->toArray());

        // Filters
        $filters = FilterUserAnalysDTO::from(['user_ids' => [$user->id]]);

        // Mocks
        $this->userAnalysRepositoryMock->shouldReceive('getMany')
            ->once()
            ->with($filters)
            ->andReturn([$userAnalysModel]);

        // Result from method of service
        $result = $this->service->getUserAnalysis($filters);

        // Check assert result
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(UserAnalys::class, $result[0]);
        $this->assertSame($user->id, $result[0]->user_id);
    }

    public function test_get_user_analysis_filter_by_analys_ids(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Init models with another user_id for search

        // Init model for search
        $factory = new UserAnalysFactory();
        $dto = UserAnalysDTO::from($factory->definition());
        $dto->user_id = $user->id;
        $userAnalysModel = UserAnalys::query()->create($dto->toArray());

        // Filters
        $filters = FilterUserAnalysDTO::from(['analys_ids' => [$dto->analys_id]]);

        // Mocks
        $this->userAnalysRepositoryMock->shouldReceive('getMany')
            ->once()
            ->with($filters)
            ->andReturn([$userAnalysModel]);

        // Result from method of service
        $result = $this->service->getUserAnalysis($filters);

        // Check assert result
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(UserAnalys::class, $result[0]);
        $this->assertSame($user->id, $result[0]->user_id);
    }

    public function test_get_user_analysis_filter_not_found(): void
    {
        // Auth user for testing
        $user = $this->authUser();

        // Init models for search
        $count = 5;
        $factory = new UserAnalysFactory();

        for ($i = 0; $i < $count; $i++) {
            $dto = UserAnalysDTO::from($factory->definition());
            $dto->user_id = $user->id;

            UserAnalys::query()->create($dto->toArray());
        }

        // Filters
        $filters = FilterUserAnalysDTO::from([
            'user_ids' => [fake()->uuid()],
            // ...
        ]);

        // Mocks
        $this->userAnalysRepositoryMock->shouldReceive('getMany')
            ->once()
            ->with($filters)
            ->andReturn([]);

        // Result from method of service
        $result = $this->service->getUserAnalysis($filters);

        // Check assert result
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
