<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Application\S3\DTO\FileDTO;
use Domain\File\Models\File;

interface FileRepositoryContract
{
    /**
     * Create File Model
     *
     * @param FileDTO $dto
     * @return File
     */
    public function createFile(FileDTO $dto): File;
}
