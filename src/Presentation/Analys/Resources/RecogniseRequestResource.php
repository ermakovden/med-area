<?php

declare(strict_types=1);

namespace Presentation\Analys\Resources;

use Application\AI\Recogniser\DTO\RecogniseRequestDTO;
use Domain\AI\Recognise\Enums\RecogniseStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'RecogniseRequestResource',
    properties: [
        new OA\Property(property: 'id', description: 'ID', type: 'string', format: 'uuid'),
        new OA\Property(property: 'user_id', description: 'ID of user', type: 'string', format: 'uuid'),
        new OA\Property(property: 'file_id', description: 'File ID in recogniser', type: 'string', format: 'uuid'),
        new OA\Property(
            property: 'response', 
            description: 'Response from external recogniser service', 
            type: 'array',
            items: new OA\Items(type: 'string'),
        ),
        new OA\Property(property: 'status', description: 'Status of recognise request', type: 'string', enum: RecogniseStatus::class),
        new OA\Property(property: 'created_at', description: 'Datetime created at', type: 'datetime'),
        new OA\Property(property: 'updated_at', description: 'Datetime updated at', type: 'datetime'),
    ],
)]
/**
 * @mixin \Application\AI\Recogniser\DTO\RecogniseRequestDTO
 */
class RecogniseRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->resource = RecogniseRequestDTO::from($this->resource);

        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->user_id,
            'file_id' => $this->resource->file_id,
            'response' => $this->resource->isNotEmptyValue('response') ? $this->resource->response : [],
            'status' => $this->resource->status,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
