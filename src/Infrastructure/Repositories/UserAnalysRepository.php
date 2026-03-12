<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Domain\Analys\Repositories\UserAnalysRepositoryContract;
use Shared\DTO\BaseDTO;
use Shared\DTO\FilterBaseDTO;
use Shared\Exceptions\ServerErrorException;
use Shared\Repositories\BaseRepository;

class UserAnalysRepository extends BaseRepository implements UserAnalysRepositoryContract
{
    /**
     * @var class-string<UserAnalys>
     */
    protected string $model = UserAnalys::class;

    public function create(BaseDTO $dto): Model
    {
        try {
            $model = $this->model::query()->create($dto->toArray());
            logger()->info('[UserAnalysRepository.create] record created', ['id' => $model->getKey()]);

            return $model;
        } catch (\Throwable $e) {
            logger()->error('[UserAnalysRepository.create] DB operation failed', [
                'error'   => $e->getMessage(),
                'context' => $dto->toArray(),
            ]);
            throw new ServerErrorException($e->getMessage());
        }
    }

    public function getMany(FilterUserAnalysDTO $filters): Collection
    {
        logger()->debug('[UserAnalysRepository.getMany] starting query', ['filters' => $filters->toArray()]);

        $query = $this->model::query();

        $result = $this->baseFilters($query, $filters)->get();

        logger()->debug('[UserAnalysRepository.getMany] returning records', ['count' => $result->count()]);

        return $result;
    }

    public function deleteMany(FilterUserAnalysDTO $filters): void
    {
        $query = $this->model::query();

        if (
            ($filters->emptyValue('user_ids') || empty($filters->user_ids))
            && ($filters->emptyValue('analys_ids') || empty($filters->analys_ids))
        ) {
            throw new ServerErrorException('Empty filters user_ids, analys_ids');
        }

        try {
            $this->baseFilters($query, $filters)->delete();
        } catch (\Throwable $e) {
            logger()->error('[UserAnalysRepository.deleteMany] DB operation failed', [
                'error'   => $e->getMessage(),
                'context' => $filters->toArray(),
            ]);
            throw new ServerErrorException($e->getMessage());
        }
    }

    /**
     * @param Builder<UserAnalys> $query
     * @param FilterBaseDTO $filters
     * @return Builder<UserAnalys>
     */
    public function baseFilters(Builder $query, FilterBaseDTO $filters): Builder
    {
        logger()->debug('[UserAnalysRepository.baseFilters] applying filters', $filters->toArray());

        $query = parent::baseFilters($query, $filters);

        // Attribute: user_id
        if ($filters->isNotEmptyValue('user_ids')) {
            $query->whereUserId($filters->user_ids);
        }

        // Attribute: analys_id
        if ($filters->isNotEmptyValue('analys_ids')) {
            $query->whereAnalysId($filters->analys_ids);
        }

        logger()->debug('[UserAnalysRepository.baseFilters] filters applied', ['query' => $query->toRawSql()]);

        return $query;
    }
}
