<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Models\UserAnalys;
use Illuminate\Support\Facades\DB;
use Infrastructure\Repositories\Contracts\UserAnalysRepositoryContract;

class UserAnalysRepository implements UserAnalysRepositoryContract
{
    /**
     * @var class-string<UserAnalys>
     */
    protected $model = UserAnalys::class;

    /**
     * Create many models UserAnalys
     *
     * @param array<UserAnalysDTO> $data
     * @return bool
     *
     * @throws \Exception
     */
    public function createMany(array $data): bool
    {
        return DB::transaction(function () use ($data) {
            foreach ($data as $userAnalys) {
                UserAnalys::create($userAnalys->toArray());
            }

            return true;
        }, 2);
    }
}
