<?php

declare(strict_types=1);

namespace Shared\DTO;

class BaseHeadersExternalDTO extends BaseDTO
{
    public ?string $authorization = null;

    public string $accept = 'application/json';
}
