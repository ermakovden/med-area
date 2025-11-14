<?php

declare(strict_types=1);

namespace Shared\DTO;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class BaseDTO extends Data
{
    public function emptyValue(string $attribute): bool
    {
        return $this->$attribute === Optional::class;
    }

    public function isNotEmptyValue(string $attribute): bool
    {
        return !$this->emptyValue($attribute);
    }
}
