<?php

declare(strict_types=1);

namespace Shared\DTO;

use Spatie\LaravelData\Optional;

abstract class FilterBaseDTO extends BaseDTO
{
    public int|Optional $limit;

    public int|Optional $offset;
}
