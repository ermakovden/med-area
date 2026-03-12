<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\Services\Contracts;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;
use Application\AI\Recogniser\DTO\Requests\RecogniseAsyncRequestDTO;
use Application\AI\Recogniser\DTO\Responses\RecogniseAsyncResponse;

interface RecogniserServiceContract
{
    public function recogniseAsync(RecogniseAsyncRequestDTO $request, RecogniseRequestDTO $recogniseRequestDTO): RecogniseRequestDTO;

    public function getRecognition(RecogniseRequestDTO $recogniseRequest): RecogniseAsyncResponse;
}
