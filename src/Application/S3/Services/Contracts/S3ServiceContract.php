<?php

declare(strict_types=1);

namespace Application\S3\Services\Contracts;

use Application\S3\DTO\FileDTO;
use Domain\File\Models\File;

interface S3ServiceContract
{
    public function upload(FileDTO $file): File;

    public function createFile(FileDTO $file): File;
}
