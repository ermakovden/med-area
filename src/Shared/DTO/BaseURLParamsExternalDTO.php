<?php

declare(strict_types=1);

namespace Shared\DTO;

use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Optional;

class BaseURLParamsExternalDTO extends BaseDTO
{
    public string|Optional $endpoint;

    public string|Optional $version;

    public string|Optional $module;

    public string|Optional $resource;
}
