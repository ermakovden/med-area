<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\Services\Contracts;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;
use Application\AI\Recogniser\DTO\Requests\RecogniseAsyncRequestDTO;

interface RecogniserServiceContract
{
    public function recogniseAsync(RecogniseAsyncRequestDTO $request, RecogniseRequestDTO $recogniseRequestDTO): RecogniseRequestDTO;
}
