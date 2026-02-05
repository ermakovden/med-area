<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\Services\Contracts;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;

interface RecogniseRequestServiceContract
{
    public function create(RecogniseRequestDTO $data): RecogniseRequestDTO;

    public function updateById(int $id, RecogniseRequestDTO $data): RecogniseRequestDTO;
}
