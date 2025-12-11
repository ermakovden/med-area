<?php

declare(strict_types=1);

namespace Shared\Enums;

enum Storage: string
{
    case S3 = 's3';

    case S3_TESTING = 's3-testing';
}
