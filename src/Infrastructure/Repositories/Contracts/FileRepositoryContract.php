<?php

declare(strict_types=1);

namespace Infrastructure\Repositories\Contracts;

use Application\S3\DTO\Filters\FilterFileDTO;
use Domain\File\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Shared\Repositories\Contracts\BaseRepositoryContract;
use Application\S3\DTO\FileDTO;

/**
 * @method File create(FileDTO $file)
 */
interface FileRepositoryContract extends BaseRepositoryContract
{
    /**
     * Get many model File use filters
     *
     * @param FilterFileDTO $filters
     * @return Collection<array-key, File>
     */
    public function getMany(FilterFileDTO $filters): Collection;
}
