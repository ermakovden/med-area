<?php

declare(strict_types=1);

namespace Application\Analys\Services;

use Application\Analys\Services\Contracts\AnalysServiceContract;
use Domain\Analys\Models\Analys;
use Illuminate\Database\Eloquent\Collection;
use Infrastructure\Repositories\Contracts\AnalysRepositoryContract;
use Shared\Exceptions\ServerErrorException;

class AnalysService implements AnalysServiceContract
{
    public function __construct(
        protected readonly AnalysRepositoryContract $analysRepository,
    ) {}

    /**
     * Get Analysis from DB
     *
     * @return Collection<array-key, Analys>
     * 
     * @throws ServerErrorException
     */
    public function getAnalysis(): Collection
    {
        try {
            return $this->analysRepository->getMany();
        } catch (\Throwable $e) {
            \Log::critical('Failed to get analysis from DB.', [
                'class' => AnalysService::class,
                'method' => 'getAnalysis',
                'message' => $e->getMessage(),
            ]);

            throw new ServerErrorException();
        }
    }
}
