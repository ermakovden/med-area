<?php

declare(strict_types=1);

namespace Application\Analys\DTO\Filters;

use Shared\DTO\FilterBaseDTO;
use Spatie\LaravelData\Optional;

class FilterUserAnalysDTO extends FilterBaseDTO
{
    /** @var array<string> $user_ids */
    public array|Optional $user_ids;

    /** @var array<string> $analys_ids */
    public array|Optional $analys_ids;
}
