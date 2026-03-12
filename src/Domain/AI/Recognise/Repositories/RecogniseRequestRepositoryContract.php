<?php

declare(strict_types=1);

namespace Domain\AI\Recognise\Repositories;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;
use Shared\Repositories\Contracts\BaseRepositoryContract;

interface RecogniseRequestRepositoryContract extends BaseRepositoryContract
{
    public function updateById(int $id, RecogniseRequestDTO $data): RecogniseRequestDTO;
}
