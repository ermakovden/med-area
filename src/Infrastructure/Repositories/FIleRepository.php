<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\S3\DTO\FileDTO;
use Domain\File\Models\File;
use Infrastructure\Repositories\Contracts\FileRepositoryContract;
use Shared\Repositories\BaseRepository;

class FileRepository extends BaseRepository implements FileRepositoryContract
{
    /**
     * @var class-string<File>
     */
    protected string $model = File::class;

    /**
     * Create File Model
     *
     * @param FileDTO $dto
     * @return File
     */
    public function createFile(FileDTO $dto): File
    {
        return $this->model::query()->create($dto->toArray());
    }
}
