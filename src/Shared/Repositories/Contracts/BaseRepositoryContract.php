<?php

declare(strict_types=1);

namespace Shared\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shared\DTO\BaseDTO;
use Shared\DTO\FilterBaseDTO;

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
