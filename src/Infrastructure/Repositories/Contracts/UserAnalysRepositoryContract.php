<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Application\Analys\DTO\UserAnalysDTO;

interface UserAnalysRepositoryContract
{
    /**
     * Create many models UserAnalys
     *
     * @param array<UserAnalysDTO> $data
     * @return bool
     *
     * @throws \Exception
     */
    public function createMany(array $data): bool;
}
