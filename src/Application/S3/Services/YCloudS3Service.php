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
            \Log::critical($e->getMessage(), [
                'class' => YCloudS3Service::class,
                'method' => 'upload',
            ]);
            throw new ServerErrorException();
        }

        $file->key = $result;

        return $this->createFile($file);
    }

    public function createFile(FileDTO $file): File
    {
        try {
            return $this->fileRepository->create($file);
        } catch (\Throwable $e) {
            \Log::critical('Failed to save to DB File data.', [
                'class' => YCloudS3Service::class,
                'method' => 'createFile',
                'message' => $e->getMessage(),
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
        try {
            return $this->fileRepository->getMany($filters);
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'class' => YCloudS3Service::class,
                'method' => 'upload',
            ]);
            throw new ServerErrorException();
        }
    }

    /**
     * Get file content from s3 storage
     *
     * @param string $key
     * @param EnumsStorage $disk = EnumsStorage::S3
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function getFileFromStorage(string $key, EnumsStorage $disk = EnumsStorage::S3): string
    {
        $disk = Storage::disk($disk);

        if (! $content = $disk->get($key)) {
            throw new NotFoundHttpException();
        }

        return $content;
    }

    public function delete(FilterFileDTO $filters): void
    {
        try {
            $this->fileRepository->deleteMany($filters);
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'class' => YCloudS3Service::class,
                'method' => 'delete',
            ]);
            throw new ServerErrorException();
        }
    }

    public function forceDelete(FilterFileDTO $filters): void
    {
        try {
            $filesForDeleting = $this->fileRepository->getMany($filters);

            $this->fileRepository->forceDeleteMany($filters);

            foreach ($filesForDeleting as $file) {
                DeleteFileJob::dispatch($file->key, $this->diskName);
            }
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'class' => YCloudS3Service::class,
                'method' => 'forceDelete',
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
