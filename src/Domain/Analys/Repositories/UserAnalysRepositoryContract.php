<?php

declare(strict_types=1);

namespace Domain\Analys\Repositories;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Database\Eloquent\Collection;
use Shared\Exceptions\ServerErrorException;
use Shared\Repositories\Contracts\BaseRepositoryContract;

interface UserAnalysRepositoryContract extends BaseRepositoryContract
{
    /**
     * Get many model UserAnalys use filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return Collection<array-key, UserAnalys>
     */
    public function getMany(FilterUserAnalysDTO $filters): Collection;

    /**
     * Delete UserAnalys Models use filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return void
     *
     * @throws ServerErrorException
     */
    public function deleteMany(FilterUserAnalysDTO $filters): void;
}
