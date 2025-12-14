<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Domain\Analys\Models\Analys;
use Illuminate\Database\Eloquent\Collection;
use Shared\Repositories\Contracts\BaseRepositoryContract;

interface AnalysRepositoryContract extends BaseRepositoryContract
{
    /**
     * Get many models Analys
     *
     * @return Collection<array-key, Analys>
     */
    public function getMany(): Collection;
}
