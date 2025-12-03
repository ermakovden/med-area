<?php

declare(strict_types=1);

namespace Presentation\Analys\Resources;

use Shared\Resources\BaseResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'UserAnalysResourceCollection',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: UserAnalysResource::class),
        ),
    ],
)]
class UserAnalysResourceCollection extends BaseResourceCollection {}
