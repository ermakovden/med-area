<?php

declare(strict_types=1);

namespace Application\Analys\Services\Contracts;

use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Domain\Analys\Models\UserAnalys;
use Shared\Exceptions\ServerErrorException;

interface UserAnalysServiceContract
{
    /**
     * Add new analysis for users
     *
     * @param CreateUserAnalysisRequestDTO $dto
     * @return array<UserAnalys>
     *
     * @throws ServerErrorException
     */
    public function createUserAnalysis(CreateUserAnalysisRequestDTO $dto): array;
}
