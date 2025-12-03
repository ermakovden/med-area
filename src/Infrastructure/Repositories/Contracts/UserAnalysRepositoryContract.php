<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Database\Eloquent\Collection;
use Shared\Exceptions\ServerErrorException;

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
