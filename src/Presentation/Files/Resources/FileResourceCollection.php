<?php

declare(strict_types=1);

namespace Presentation\Files\Resources;

use Shared\Resources\BaseResourceCollection;
use OpenApi\Attributes as OA;
use Presentation\Files\Resources\FileResource;

#[OA\Schema(
    title: 'FileResourceCollection',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: FileResource::class),
        ),
    ],
)]
class FileResourceCollection extends BaseResourceCollection {}
