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
     * Get many models Analys
     *
     * @return Collection<array-key, Analys>
     */
    public function getMany(): Collection
    {
        return Analys::query()->get();
    }
}
