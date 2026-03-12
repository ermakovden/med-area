<?php

declare(strict_types=1);

namespace Shared\Repositories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Shared\DTO\BaseDTO;
use Shared\DTO\FilterBaseDTO;
use Shared\Exceptions\ServerErrorException;
use Shared\Repositories\Contracts\BaseRepositoryContract;

abstract class BaseRepository implements BaseRepositoryContract
{
    /**
     * @var class-string<Model>
     */
    protected string $model;

    public function create(BaseDTO $dto): Model
    {
        try {
            $model = $this->model::create($dto->toArray());
            logger()->info('[BaseRepository.create] record created', ['id' => $model->getKey()]);

            return $model;
        } catch (\Throwable $e) {
            logger()->error('[BaseRepository.create] DB operation failed', [
                'error'   => $e->getMessage(),
                'context' => $dto->toArray(),
            ]);
            throw new ServerErrorException($e->getMessage());
        }
    }

    /**
     * Base filters using FilterBaseDTO
     *
     * @template TModel of Model
     * @param Builder<TModel> $query
     * @param FilterBaseDTO $filters
     * @return Builder<TModel>
     */
    public function baseFilters(Builder $query, FilterBaseDTO $filters): Builder
    {
        // Limit && Offset
        if ($filters->isNotEmptyValue('limit') && $filters->isNotEmptyValue('offset')) {
            /** @var int $limit */
            $limit = $filters->limit;
            /** @var int $offset */
            $offset = $filters->offset;
            $query = $query->limit($limit)->offset($offset);
        }

        return $query;
    }

    /**
     * Range filter for dates
     *
     * @template TModel of Model
     * @param Builder<TModel> $query
     * @param string $attribute
     * @param Carbon|null $minDate
     * @param Carbon|null $maxDate
     * @return Builder<TModel>
     */
    public function filterDateRange(Builder $query, string $attribute, ?Carbon $minDate = null, ?Carbon $maxDate = null): Builder
    {
        if ($minDate !== null && $maxDate !== null) {
            return $query->whereBetween($attribute, [$minDate, $maxDate]);
        }

        if ($minDate !== null && $maxDate === null) {
            return $query->where($attribute, '>', $minDate);
        }

        if ($minDate === null && $maxDate !== null) {
            return $query->where($attribute, '<', $maxDate);
        }

        return $query;
    }
}
