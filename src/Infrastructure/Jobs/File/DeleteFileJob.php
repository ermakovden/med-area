<?php

declare(strict_types=1);

namespace Infrastructure\Jobs\File;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Shared\Enums\Storage as EnumsStorage;
use Shared\Exceptions\ServerErrorException;

class DeleteFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $path;
    protected Filesystem $disk;

    public function __construct(string $path, ?Filesystem $disk = null)
    {
        $this->path = $path;
        $this->disk = $disk ?? Storage::disk(EnumsStorage::S3);
    }

    public function handle(): void
    {
        try {
            if ($this->disk->exists($this->path)) {
                $this->disk->delete($this->path);
            }
        } catch (\Throwable $e) {
            \Log::error('Failed to delete file: ' . $this->path . '. ' . $e->getMessage());
            throw new ServerErrorException();
        }
    }
}
