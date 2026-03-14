<?php

declare(strict_types=1);

namespace Infrastructure\Listeners\AI;

use Domain\AI\Recognise\Events\RecogniseRequestCompleted;
use Infrastructure\Jobs\AI\Recogniser\UpdateYVisionRecogniseRequestJob;

class DispatchUpdateRecogniseRequestJobListener
{
    public function handle(RecogniseRequestCompleted $event): void
    {
        logger()->debug('[DispatchUpdateRecogniseRequestJobListener.handle] dispatching UpdateYVisionRecogniseRequestJob', [
            'operation_id' => $event->recogniseRequestDTO->operation_id,
        ]);

        UpdateYVisionRecogniseRequestJob::dispatch($event->recogniseRequestDTO)
            ->delay(now()->plus(seconds: 40));
    }
}
