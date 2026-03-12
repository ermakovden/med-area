<?php

declare(strict_types=1);

namespace Domain\File\Repositories;

use Application\S3\DTO\Filters\FilterFileDTO;
use Application\S3\DTO\FileDTO;
use Domain\File\Models\File;
use Illuminate\Database\Eloquent\Collection;
use Shared\Repositories\Contracts\BaseRepositoryContract;

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

    /**
     * Soft delete from DB
     *
     * @param FilterFileDTO $filters
     * @return void
     */
    public function deleteMany(FilterFileDTO $filters): void;

    /**
     * Force delete from DB
     *
     * @param FilterFileDTO $filters
     * @return void
     */
    public function forceDeleteMany(FilterFileDTO $filters): void;
}
