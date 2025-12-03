<?php

declare(strict_types=1);

namespace Application\Analys\Services\Contracts;

use Domain\Analys\Models\Analys;
use Illuminate\Database\Eloquent\Collection;
use Shared\Exceptions\ServerErrorException;

interface AnalysServiceContract
{
    /**
     * Get Analysis from DB
     *
     * @return Collection<array-key, Analys>
     *
     * @throws ServerErrorException
     */
    public function getAnalysis(): Collection;
}
