<?php

declare(strict_types=1);

namespace Application\S3\Services\Contracts;

use Application\S3\DTO\FileDTO;
use Application\S3\DTO\Filters\FilterFileDTO;
use Domain\File\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Shared\Enums\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * Get file content from s3 storage
     *
     * @param string $key
     * @param Storage $disk = Storage::S3
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function getFileFromStorage(string $key, Storage $disk = Storage::S3): string;

    public function delete(FilterFileDTO $filters): void;

    public function forceDelete(FilterFileDTO $filters): void;

    public function fileExists(string $key): bool;
}
