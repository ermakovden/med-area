<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Enums\Analys;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;
use Shared\Exceptions\ServerErrorException;

class UserAnalysRepository implements UserAnalysRepositoryContract
{
    /**
     * @var class-string<UserAnalys>
     */
    protected $model = UserAnalys::class;

    /**
     * Create UserAnalys Model
     *
     * @param UserAnalysDTO $dto
     * @return UserAnalys
     */
    public function create(UserAnalysDTO $dto): UserAnalys
    {
        if ($dto->isNotEmptyValue('analys_id') && $dto->emptyValue('analys_name')) {
            /** @var Analys $analysId */
            $analysId = $dto->analys_id;

            $dto->analys_name = $analysId->name;
        }

        return UserAnalys::query()->create($dto->toArray());
    }

    /**
     * Get many model UserAnalys use filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return Collection<array-key, UserAnalys>
     */
    public function getMany(FilterUserAnalysDTO $filters): Collection
    {
        return $this->baseFilters($filters)->get();
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
        if (
            ($filters->emptyValue('user_ids') || empty($filters->user_ids))
            && ($filters->emptyValue('analys_ids') || empty($filters->analys_ids))
        ) {
            throw new ServerErrorException('Empty filters user_ids, analys_ids for UserAnalysRepository@deleteMany');
        }

        $this->baseFilters($filters)->delete();

        return;
    }

    /**
     * Base filters for sql requests
     * Description: containts user_ids, analys_ids
     *
     * @param FilterUserAnalysDTO $filters
     * @return Builder<UserAnalys>
     */
    protected function baseFilters(FilterUserAnalysDTO $filters): Builder
    {
        $query = UserAnalys::query();

        // Attribute: user_id
        if ($filters->isNotEmptyValue('user_ids')) {
            $query->whereUserId($filters->user_ids);
        }

        // Attribute: analys_id
        if ($filters->isNotEmptyValue('analys_ids')) {
            $query->whereAnalysId($filters->analys_ids);
        }

        return $query;
    }
}
