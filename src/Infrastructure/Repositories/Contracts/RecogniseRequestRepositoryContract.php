<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;
use Shared\Repositories\Contracts\BaseRepositoryContract;

interface RecogniseRequestRepositoryContract extends BaseRepositoryContract
{
    public function updateById(int $id, RecogniseRequestDTO $data): RecogniseRequestDTO;
}
