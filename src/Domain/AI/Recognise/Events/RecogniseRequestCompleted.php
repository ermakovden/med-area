<?php

declare(strict_types=1);

namespace Domain\AI\Recognise\Events;

use Domain\AI\Recognise\DTO\RecogniseRequestDTO;

class RecogniseRequestCompleted
{
    public function __construct(
        public readonly RecogniseRequestDTO $recogniseRequestDTO,
    ) {}
}
