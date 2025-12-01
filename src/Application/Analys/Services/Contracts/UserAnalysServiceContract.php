<?php

declare(strict_types=1);

namespace Application\Analys\Services\Contracts;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\Requests\CreateUserAnalysisRequestDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Database\Eloquent\Collection;
use Shared\Exceptions\ServerErrorException;

interface UserAnalysServiceContract
{
    /**
     * Add new analysis for users
     *
     * @param CreateUserAnalysisRequestDTO $dto
     * @return array<UserAnalysDTO>
     *
     * @throws ServerErrorException
     */
    public function createUserAnalysis(CreateUserAnalysisRequestDTO $dto): array;

    /**
     * Get UserAnalys Models by filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return Collection<string, UserAnalys>
     *
     * @throws ServerErrorException
     */
    public function getUserAnalysis(FilterUserAnalysDTO $filters): Collection;


    /**
     * Delete UserAnalys Models by filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return void
     *
     * @throws ServerErrorException
     */
    public function deleteUserAnalysis(FilterUserAnalysDTO $filters): void;
}
