<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\Services\Contracts;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;

interface RecogniseRequestServiceContract
{
    public function updateById(int $id, RecogniseRequestDTO $data): RecogniseRequestDTO;
}
