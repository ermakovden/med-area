<?php

declare(strict_types=1);

namespace Application\S3\DTO\Filters;

use Carbon\Carbon;
use Shared\DTO\FilterBaseDTO;
use Spatie\LaravelData\Optional;

class FilterFileDTO extends FilterBaseDTO
{
    /** @var array<string> $ids */
    public array|Optional $ids;

    /** @var array<string> $user_ids */
    public array|Optional $user_ids;

    // Range for size
    public int|Optional $min_size;
    public int|Optional $max_size;

    // Range for deleted_at
    public Carbon|Optional|null $min_deleted_at;
    public Carbon|Optional|null $max_deleted_at;
}
