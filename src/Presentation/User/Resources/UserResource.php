<?php

declare(strict_types=1);

namespace Presentation\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'UserResource',
    properties: [
        new OA\Property(property: 'id', description: 'UUID of user', type: 'string'),
        new OA\Property(property: 'nickname', description: 'Nickname of user', type: 'string'),
        new OA\Property(property: 'email', description: 'Email of User', type: 'string'),
        new OA\Property(property: 'created_at', description: 'Datetime created at of user', type: 'datetime'),
        new OA\Property(property: 'updated_at', description: 'Datetime updated at of user', type: 'datetime'),
    ],
)]
/**
 * @mixin \Application\User\DTO\UserDTO
 */
class UserResource extends JsonResource
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
            'nickname' => $this->resource->nickname,
            'email' => $this->resource->email,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
