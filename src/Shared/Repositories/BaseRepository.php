<?php

declare(strict_types=1);

namespace Shared\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shared\DTO\BaseDTO;
use Shared\DTO\FilterBaseDTO;
use Shared\Repositories\Contracts\BaseRepositoryContract;

abstract class BaseRepository implements BaseRepositoryContract
{
    /**
     * @var class-string<Model>
     */
    protected string $model;

    public function create(BaseDTO $dto): Model
    {
        return $this->model::create($dto->toArray());
    }

    /**
     * Base filters using FilterBaseDTO
     *
     * @param Builder<Model> $query
     * @param FilterBaseDTO $filters
     * @return Builder<Model>
     */
    public function baseFilters(Builder $query, FilterBaseDTO $filters): Builder
    {
        // Limit && Offset
        if ($filters->isNotEmptyValue('limit') && $filters->isNotEmptyValue('offset')) {
            /** @phpstan-ignore-next-line */
            $query = $query->limit($filters->limit)->offset($filters->offset);
        }

        return $query;
    }
}
