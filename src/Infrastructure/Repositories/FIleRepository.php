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
        $query = $this->model::query();

        $query = $this->baseFilters($query, $filters);

        return $query->get();
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

        // Attribute: user_id
        if ($filters->isNotEmptyValue('user_ids')) {
            $query->whereUserId($filters->user_ids);
        }

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
