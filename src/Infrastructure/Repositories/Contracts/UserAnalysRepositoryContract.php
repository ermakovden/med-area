<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Application\Analys\DTO\UserAnalysDTO;
use Domain\Analys\Models\UserAnalys;
use Shared\Exceptions\ServerErrorException;

interface UserAnalysRepositoryContract
{
    /**
     * Create many models UserAnalys
     *
     * @param array<UserAnalysDTO> $data
     * @return array<UserAnalys>
     *
     * @throws ServerErrorException
     */
    public function createMany(array $data): array;
}
