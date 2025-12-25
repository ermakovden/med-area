<?php

declare(strict_types=1);

namespace Shared\Enums;

enum AuthTokenType: string
{
    case BEARER = 'Bearer';

    case API_KEY = 'Api-Key';
}
