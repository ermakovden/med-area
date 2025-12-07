<?php

declare(strict_types=1);

namespace Application\S3\Services;

use Application\S3\DTO\FileDTO;
use Application\S3\Services\Contracts\S3ServiceContract;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Shared\Exceptions\ServerErrorException;

class YCloudS3Service implements S3ServiceContract
{
    /**
     * Storage
     *
     * @var Filesystem
     */
    protected Filesystem $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage->disk('s3'); // use s3 disk for ycloud service (see: filesistems.php -> disks -> s3)
    }

    public function upload(FileDTO $file): string
    {
        $path = $this->getFilePath($file);

        if (! $result = $this->storage->putFileAs($path, $file->content, $file->key)) {
            \Log::critical();
            throw new ServerErrorException();
        }
    
        return $result;
    }

    /**
     * Get path for file
     * Example: users/{userId}/fileName/fileName.extension
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

        return 'users/' . $userId ?? auth()->user()?->id . '/' . $file->key . '/' . $file->content->extension();

    }
}
