<?php

declare(strict_types=1);

namespace Presentation\Files\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'FileResource',
    properties: [
        new OA\Property(property: 'id', description: 'id of File', type: 'string', format: 'uuid'),
        new OA\Property(property: 'user_id', description: 'user_id of File', type: 'string', format: 'uuid'),
        new OA\Property(property: 'path', description: 'path of File', type: 'string'),
    ],
)]
/**
 * @mixin \Application\S3\DTO\FileDTO
 */
class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'path' => $this->resource->endpoint . '/' . $this->resource->bucket . '/' . $this->resource->key,
        ];
    }
}
