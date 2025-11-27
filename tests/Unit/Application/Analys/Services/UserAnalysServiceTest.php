<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Analys\Services;

use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Application\Analys\Services\UserAnalysService;
use Domain\Analys\Factories\UserAnalysFactory;
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
        $this->userAnalysRepositoryMock->shouldReceive('createMany')
            ->once()
            ->with($dto->analysis)
            ->andReturn($dto->analysis);

        // Result from method of service
        $result = $this->service->createUserAnalysis($dto);

        // Check assert result
        $this->assertIsArray($result);
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
        $this->userAnalysRepositoryMock->shouldReceive('createMany')
            ->once()
            ->with($dto->analysis)
            ->andThrow(ServerErrorException::class);

        // Call method of service
        $this->service->createUserAnalysis($dto);
    }
}
