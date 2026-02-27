<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;
use Domain\AI\Recognise\Models\RecogniseRequest;
use Infrastructure\Repositories\Contracts\RecogniseRequestRepositoryContract;
use Shared\Exceptions\ServerErrorException;
use Shared\Repositories\BaseRepository;

class RecogniseRequestRepository extends BaseRepository implements RecogniseRequestRepositoryContract
{
    /**
     * @var class-string<RecogniseRequest>
     */
    protected string $model = RecogniseRequest::class;

    public function updateById(int $id, RecogniseRequestDTO $data): RecogniseRequestDTO
    {
        try {
            $model = $this->model::query()->findOrFail($id);

            $model->updateOrFail($data->toArray());

            return RecogniseRequestDTO::from([
                $this->model::query()->findOrFail($id),
            ]);
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'class' => RecogniseRequestRepository::class,
                'method' => 'updateById',
            ]);
            throw new ServerErrorException();
        }
    }
}
