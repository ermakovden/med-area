<?php

declare(strict_types=1);

namespace Presentation\User\Resources;

use Domain\User\Enums\TokenType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'TokenResource',
    properties: [
        new OA\Property(property: 'access_token', description: 'Access token', type: 'string'),
        new OA\Property(property: 'token_type', description: 'Type of token', type: 'string', enum: TokenType::class),
        new OA\Property(property: 'expires_in', description: 'Expires token in', type: 'int'),
    ],
)]
/**
 * @mixin \Application\User\DTO\TokenResponse
 */
class TokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'access_token' => $this->resource->access_token,
            'token_type' => $this->resource->token_type,
            'expires_in' => $this->resource->expires_in,
        ];
    }
}
