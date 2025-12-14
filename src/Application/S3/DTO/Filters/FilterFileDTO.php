<?php

declare(strict_types=1);

namespace Application\S3\DTO\Filters;

use Shared\DTO\FilterBaseDTO;
use Spatie\LaravelData\Optional;

class FilterFileDTO extends FilterBaseDTO
{
    /** @var array<string> $user_ids */
    public array|Optional $user_ids;

    // Range for size
    public int|Optional $min_size;
    public int|Optional $max_size;
}
