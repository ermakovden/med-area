<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Models\UserAnalys;

interface UserAnalysRepositoryContract
{
    /**
     * Create UserAnalys Model
     *
     * @param UserAnalysDTO $dto
     * @return UserAnalys
     */
    public function create(UserAnalysDTO $dto): UserAnalys;

    /**
     * Get many model UserAnalys use filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return array<UserAnalys>
     */
    public function getMany(FilterUserAnalysDTO $filters): array;
}
