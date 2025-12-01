<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\Analys\DTO\Filters\FilterUserAnalysDTO;
use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Models\UserAnalys;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;

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
        return UserAnalys::query()->create($dto->toArray());
    }

    /**
     * Get many model UserAnalys use filters
     *
     * @param FilterUserAnalysDTO $filters
     * @return array<UserAnalys>
     */
    public function getMany(FilterUserAnalysDTO $filters): array
    {
        $query = UserAnalys::query();

        // Attribute: user_id
        $query->whereUserId($filters->user_ids);

        // Attribute: analys_id
        $query->whereAnalysId($filters->analys_ids);

        return $query->get()->toArray();
    }
}
