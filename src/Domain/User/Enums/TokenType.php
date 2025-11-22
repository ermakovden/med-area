<?php

declare(strict_types=1);

namespace Domain\User\Enums;

enum TokenType: string
{
    case BEARER = 'bearer';
}
