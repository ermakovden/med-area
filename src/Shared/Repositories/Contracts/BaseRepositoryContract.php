<?php

declare(strict_types=1);

namespace Shared\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shared\DTO\BaseDTO;
use Shared\DTO\FilterBaseDTO;

/**
 * Base contract for all repositories.
 *
 * Convention: every concrete repository contract MUST declare a `getMany()` method
 * with a typed filter DTO parameter (e.g. `getMany(FilterFooDTO $filters): Collection`),
 * or with no parameter for simple entities without filterable state (e.g. `Analys`).
 * This pattern is enforced at the concrete contract level because filter DTOs are
 * domain-specific and cannot be abstracted into a single shared signature here.
 */
interface BaseRepositoryContract
{
    public function create(BaseDTO $dto): Model;

    /**
     * Base filters using FilterBaseDTO
     *
     * @param Builder<Model> $query
     * @param FilterBaseDTO $filters
     * @return Builder<Model>
     */
    public function baseFilters(Builder $query, FilterBaseDTO $filters): Builder;
}
