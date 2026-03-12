<?php

declare(strict_types=1);

namespace Infrastructure\Repositories;

use Application\S3\DTO\Filters\FilterFileDTO;
use Domain\File\Models\File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Infrastructure\Repositories\Contracts\FileRepositoryContract;
use Shared\DTO\FilterBaseDTO;
use Shared\Repositories\BaseRepository;

class FileRepository extends BaseRepository implements FileRepositoryContract
{
    /**
     * @var class-string<File>
     */
    protected string $model = File::class;

    /**
     * Get many model File use filters
     *
     * @param FilterFileDTO $filters
     * @return Collection<array-key, File>
     */
    public function getMany(FilterFileDTO $filters): Collection
    {
        $query = $filters->emptyValue('min_deleted_at') && $filters->emptyValue('max_deleted_at')
            ? $this->model::query()
            : $this->model::withTrashed();

        $query = $this->baseFilters($query, $filters);

        return $query->get();
    }

    /**
     * Soft delete from DB
     *
     * @param FilterFileDTO $filters
     * @return void
     */
    public function delete(FilterFileDTO $filters): void
    {
        $query = $this->model::query();

        $this->baseFilters($query, $filters)->delete();
    }

    /**
     * Force delete from DB
     *
     * @param FilterFileDTO $filters
     * @return void
     */
    public function forceDelete(FilterFileDTO $filters): void
    {
        $query = $this->model::onlyTrashed();

        $this->baseFilters($query, $filters)->forceDelete();
    }

    /**
     * Base filters for sql requests
     *
     * @param Builder<File> $query
     * @param FilterFileDTO $filters
     * @return Builder<File>
     */
    public function baseFilters(Builder $query, FilterBaseDTO $filters): Builder
    {
        /** @phpstan-ignore argument.type */
        parent::baseFilters($query, $filters);

        // Attribute: deleted_at
        $filters->min_deleted_at = $filters->emptyValue('min_deleted_at') ? null : $filters->min_deleted_at;
        $filters->max_deleted_at = $filters->emptyValue('max_deleted_at') ? null : $filters->max_deleted_at;

        /** @phpstan-ignore-next-line */
        $query = $this->filterDateRange($query, 'deleted_at', $filters->min_deleted_at, $filters->max_deleted_at);
        /** @var Builder<File> $query */

        // Attribute: user_id
        if ($filters->isNotEmptyValue('user_ids')) {
            $query->whereUserId($filters->user_ids);
        }

        // Attribute: size
        if ($filters->isNotEmptyValue('min_size') && $filters->emptyValue('max_size')) {
            $query->where('size', '>', $filters->min_size);
        }

        if ($filters->emptyValue('min_size') && $filters->isNotEmptyValue('max_size')) {
            $query->where('size', '<', $filters->max_size);
        }

        if ($filters->isNotEmptyValue('min_size') && $filters->isNotEmptyValue('max_size')) {
            $query
                ->where('size', '>', $filters->min_size)
                ->where('size', '<', $filters->max_size);
        }

        return $query;
    }
}
