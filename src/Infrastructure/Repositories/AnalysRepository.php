<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Domain\Analys\Models\Analys;
use Illuminate\Database\Eloquent\Collection;
use Infrastructure\Repositories\Contracts\AnalysRepositoryContract;
use Shared\Repositories\BaseRepository;

class AnalysRepository extends BaseRepository implements AnalysRepositoryContract
{
    /**
     * @var class-string<Analys>
     */
    protected string $model = Analys::class;

    /**
     * Get many models Analys
     *
     * @return Collection<array-key, Analys>
     */
    public function getMany(): Collection
    {
        logger()->debug('[AnalysRepository.getMany] fetching all records');

        $result = $this->model::query()->get();

        logger()->debug('[AnalysRepository.getMany] returning records', ['count' => $result->count()]);

        return $result;
    }
}
