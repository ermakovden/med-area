<?php

declare(strict_types=1);

namespace Presentation\Analys\Resources;

use Domain\Analys\Enums\Analys;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'AnalysResource',
    properties: [
        new OA\Property(property: 'id', description: 'ID of analys', type: 'int', enum: Analys::class),
        new OA\Property(property: 'name', description: 'Name of analys', type: 'string'),
    ],
)]
/**
 * @mixin \Application\Analys\DTO\AnalysDTO
 */
class AnalysResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id->value,
            'name' => $this->resource->name,
        ];
    }
}
