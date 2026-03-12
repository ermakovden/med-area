<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Enums\Analys;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;
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

    /**
     * Create UserAnalys Model
     *
     * @param UserAnalysDTO $dto
     * @return UserAnalys
     */
    public function create(BaseDTO $dto): Model
    {
        if ($dto->isNotEmptyValue('analys_id') && $dto->emptyValue('analys_name')) {
            /** @var Analys $analysId */
            $analysId = $dto->analys_id;

            $dto->analys_name = $analysId->name;
        }

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

    /**
     * Get many model UserAnalys use filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return Collection<array-key, UserAnalys>
     */
    public function getMany(FilterUserAnalysDTO $filters): Collection
    {
        logger()->debug('[UserAnalysRepository.getMany] starting query', ['filters' => $filters->toArray()]);

        $query = $this->model::query();

        $result = $this->baseFilters($query, $filters)->get();

        logger()->debug('[UserAnalysRepository.getMany] returning records', ['count' => $result->count()]);

        return $result;
    }

    /**
     * Delete UserAnalys Models use filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return void
     *
     * @throws ServerErrorException
     */
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

        return;
    }

    /**
     * Base filters for sql requests
     *
     * @param Builder<UserAnalys> $query
     * @param FilterUserAnalysDTO $filters
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
