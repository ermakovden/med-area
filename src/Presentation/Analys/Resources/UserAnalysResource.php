<?php

declare(strict_types=1);

namespace Presentation\Analys\Resources;

use Application\Analys\DTO\AnalysDTO;
use Domain\Analys\Enums\Analys;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'UserAnalysResource',
    properties: [
        new OA\Property(property: 'id', description: 'ID User Analys', type: 'string', format: 'uuid'),
        new OA\Property(property: 'user_id', description: 'ID User', type: 'string'),
        new OA\Property(property: 'analys_id', description: 'ID Analys', type: 'int', enum: Analys::class),
        new OA\Property(property: 'analys_name', description: 'Name Analys', type: 'string'),
        new OA\Property(property: 'data', description: 'Data Analys', type: 'float'),
        new OA\Property(property: 'created_at', description: 'Datetime created at of analys for user', type: 'datetime'),
        new OA\Property(property: 'updated_at', description: 'Datetime updated at of analys for user', type: 'datetime'),
    ],
)]
/**
 * @mixin \Application\Analys\DTO\UserAnalysDTO
 */
class UserAnalysResource extends JsonResource
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
            'analys' => new AnalysResource(AnalysDTO::from([
                'id' => $this->resource->analys_id,
                'name' => $this->resource->analys_name,
            ])),
            'data' => $this->resource->data,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
