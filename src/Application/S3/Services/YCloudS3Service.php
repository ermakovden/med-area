<?php

declare(strict_types=1);

namespace Application\S3\Services;

use Domain\File\DTO\FileDTO;
use Domain\File\DTO\Filters\FilterFileDTO;
use Application\S3\Services\Contracts\S3ServiceContract;
use Domain\File\Models\File;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Infrastructure\Jobs\File\DeleteFileJob;
use Domain\File\Repositories\FileRepositoryContract;
use Shared\Enums\Storage as EnumsStorage;
use Shared\Exceptions\ServerErrorException;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Docs see here: https://yandex.cloud/ru/docs/storage/
 */
class YCloudS3Service implements S3ServiceContract
{
    public private(set) Filesystem $disk;

    protected readonly FileRepositoryContract $fileRepository;

    protected readonly EnumsStorage $diskName;

    public function __construct(
        FileRepositoryContract $fileRepository,
        ?Filesystem $disk = null,
        ?EnumsStorage $diskName = null,
    ) {
        $this->fileRepository = $fileRepository;
        $this->disk = $disk ?? Storage::disk(EnumsStorage::S3); // use s3 disk for ycloud service (see: filesystems.php -> disks -> s3)
        $this->diskName = $diskName ?? EnumsStorage::S3;
    }

    public function upload(FileDTO $file): File
    {
        logger()->debug('[YCloudS3Service.upload] starting', ['file_key' => $file->key]);

        try {
            if ($file->emptyValue('content')) {
                throw new ServerErrorException('File content not found.');
            }

            assert($file->content instanceof UploadedFile);

            $path = $this->getFilePath($file);

            $result = $this->disk->putFile($path, $file->content);
            if (! $result) {
                throw new ServerErrorException('Cant upload file to ycloud s3. Path: ' . $path);
            }
        } catch (\Exception $e) {
            logger()->critical('[YCloudS3Service.upload] upload to S3 failed', [
                'error'   => $e->getMessage(),
                'context' => ['file_key' => $file->key],
            ]);
            throw new ServerErrorException();
        }

        $file->key = $result;

        return $this->createFile($file);
    }

    public function createFile(FileDTO $file): File
    {
        logger()->debug('[YCloudS3Service.createFile] starting', ['file_key' => $file->key]);

        try {
            return $this->fileRepository->create($file);
        } catch (\Throwable $e) {
            logger()->critical('[YCloudS3Service.createFile] failed to save file to DB', [
                'error'   => $e->getMessage(),
                'context' => ['file_key' => $file->key],
            ]);

            throw new ServerErrorException();
        }
    }

    /**
     * Get Files Models Collection
     *
     * @param FilterFileDTO $filters
     * @return Collection<array-key, File>
     */
    public function getFiles(FilterFileDTO $filters): Collection
    {
        logger()->debug('[YCloudS3Service.getFiles] starting', ['filters' => $filters->toArray()]);

        try {
            $result = $this->fileRepository->getMany($filters);
        } catch (\Throwable $e) {
            logger()->error('[YCloudS3Service.getFiles] failed to get files', [
                'error'   => $e->getMessage(),
                'context' => $filters->toArray(),
            ]);
            throw new ServerErrorException();
        }

        logger()->debug('[YCloudS3Service.getFiles] returning records', ['count' => $result->count()]);

        return $result;
    }

    /**
     * Get file content from s3 storage
     *
     * @param string $key
     * @param ?EnumsStorage $diskName = null (uses default disk if null)
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function getFileFromStorage(string $key, ?EnumsStorage $diskName = null): string
    {
        logger()->debug('[YCloudS3Service.getFileFromStorage] retrieving file from storage', [
            'key' => $key,
            'disk' => $diskName !== null ? $diskName->value : $this->diskName->value,
        ]);

        $storage = $diskName !== null ? Storage::disk($diskName) : $this->disk;

        if (! $content = $storage->get($key)) {
            throw new NotFoundHttpException();
        }

        return $content;
    }

    public function delete(FilterFileDTO $filters): void
    {
        logger()->info('[YCloudS3Service.delete] deleting files', ['filters' => $filters->toArray()]);

        try {
            $this->fileRepository->deleteMany($filters);
        } catch (\Throwable $e) {
            logger()->error('[YCloudS3Service.delete] delete failed', [
                'error'   => $e->getMessage(),
                'context' => $filters->toArray(),
            ]);
            throw new ServerErrorException();
        }
    }

    public function forceDelete(FilterFileDTO $filters): void
    {
        logger()->info('[YCloudS3Service.forceDelete] force-deleting files', ['filters' => $filters->toArray()]);

        try {
            $filesForDeleting = $this->fileRepository->getMany($filters);

            $this->fileRepository->forceDeleteMany($filters);

            foreach ($filesForDeleting as $file) {
                DeleteFileJob::dispatch($file->key, $this->diskName);
            }
        } catch (\Throwable $e) {
            logger()->error('[YCloudS3Service.forceDelete] force-delete failed', [
                'error'   => $e->getMessage(),
                'context' => $filters->toArray(),
            ]);
            throw new ServerErrorException();
        }
    }

    public function fileExists(string $key): bool
    {
        return $this->disk->exists($key);
    }

    public function setDisk(Filesystem $newDisk): self
    {
        $this->disk = $newDisk;

        return $this;
    }

    /**
     * Get path for file
     * Example: users/{userId}/fileName.extension
     *
     * @param FileDTO $file
     * @param string|null $userId
     * @return string
     *
     * @throws ServerErrorException
     */
    private function getFilePath(FileDTO $file, ?string $userId = null): string
    {
        if ($userId === null && ! auth()->check()) {
            throw new ServerErrorException();
        }

        $userId ??= auth()->user()?->id;

        assert($file->content instanceof UploadedFile);

        return 'users/' . $userId . '/' . $file->key . '.' . $file->content->extension();
    }
}
