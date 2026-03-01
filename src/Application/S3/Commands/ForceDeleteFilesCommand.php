<?php

declare(strict_types=1);

namespace Application\S3\Commands;

use Application\S3\DTO\Filters\FilterFileDTO;
use Application\S3\Services\Contracts\S3ServiceContract;
use Illuminate\Console\Command;

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

    public function __construct(
        private readonly S3ServiceContract $s3Service,
    ) {
        parent::__construct();
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
