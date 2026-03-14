<?php

declare(strict_types=1);

namespace Domain\File\Events;

use Shared\Enums\Storage;

class FileMarkedForDeletion
{
    public function __construct(
        public readonly string $key,
        public readonly Storage $diskName,
    ) {}
}
