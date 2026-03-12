<?php

declare(strict_types=1);

namespace Application\S3\DTO\Requests;

use Domain\File\DTO\FileDTO;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Optional;

class CreateFilesRequestDTO extends BaseDTO
{
    /** @var array<FileDTO> $files  */
    public array|Optional $files;
}
