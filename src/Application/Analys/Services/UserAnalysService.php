<?php

declare(strict_types=1);

namespace Application\Analys\Services;

use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Application\Analys\Services\Contracts\UserAnalysServiceContract;
use Domain\Analys\Models\UserAnalys;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;
use Shared\Exceptions\ServerErrorException;

class UserAnalysService implements UserAnalysServiceContract
{
    public function __construct(
        protected readonly UserAnalysRepositoryContract $userAnalysRepository,
    ) {}

    /**
     * Add new analysis for users
     *
     * @param CreateUserAnalysisRequestDTO $dto
     * @return array<UserAnalys>
     *
     * @throws ServerErrorException
     */
    public function createUserAnalysis(CreateUserAnalysisRequestDTO $dto): array
    {
        return $this->userAnalysRepository->createMany($dto->analysis);
    }
}
