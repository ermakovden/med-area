<?php

declare(strict_types=1);

namespace Presentation\Analys\Resources;

use Shared\Resources\BaseResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'AnalysResourceCollection',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: AnalysResource::class),
        ),
    ],
)]
class AnalysResourceCollection extends BaseResourceCollection {}
