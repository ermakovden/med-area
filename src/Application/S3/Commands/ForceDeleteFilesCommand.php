<?php

declare(strict_types=1);

namespace Application\S3\Commands;

use Application\S3\DTO\Filters\FilterFileDTO;
use Application\S3\Services\Contracts\S3ServiceContract;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Shared\Enums\Storage as EnumsStorage;

class ForceDeleteFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:force-delete-files-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force delete files after days in .env';

    protected S3ServiceContract $s3Service;

    public function __construct(?Filesystem $disk = null)
    {
        $this->s3Service = app(S3ServiceContract::class);

        $this->s3Service->disk = $disk ?? Storage::disk(EnumsStorage::S3);
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filters = FilterFileDTO::from([
            'max_deleted_at' => now()->subDays(config('filesystems.environments.force_delete_sub_days')),
        ]);

        $this->s3Service->forceDelete($filters);
    }
}
