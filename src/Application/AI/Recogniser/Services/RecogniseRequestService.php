<?php

declare(strict_types=1);

namespace Application\AI\Recogniser\Services;

use Domain\AI\Recognise\DTO\RecogniseRequestDTO;
use Application\AI\Recogniser\Services\Contracts\RecogniseRequestServiceContract;
use Domain\AI\Recognise\Repositories\RecogniseRequestRepositoryContract;

class RecogniseRequestService implements RecogniseRequestServiceContract
{
    public function __construct(
        protected readonly RecogniseRequestRepositoryContract $recogniseRequestRepository
    ) {}

    public function create(RecogniseRequestDTO $data): RecogniseRequestDTO
    {
        $model = $this->recogniseRequestRepository->create($data);

        return RecogniseRequestDTO::from($model);
    }

    public function updateById(int $id, RecogniseRequestDTO $data): RecogniseRequestDTO
    {
        return $this->recogniseRequestRepository->updateById($id, $data);
    }
}
