<?php

declare(strict_types=1);

namespace Application\S3\Services\Contracts;

use Application\S3\DTO\FileDTO;
use Application\S3\DTO\Filters\FilterFileDTO;
use Domain\File\Models\File;
use Illuminate\Database\Eloquent\Collection;

interface S3ServiceContract
{
    public function upload(FileDTO $file): File;

    public function createFile(FileDTO $file): File;

    /**
     * Get Files Models Collection
     *
     * @param FilterFileDTO $filters
     * @return Collection<array-key, File>
     */
    public function getFiles(FilterFileDTO $filters): Collection;
}
