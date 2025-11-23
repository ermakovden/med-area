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
            'access_token' => $this->access_token,
            'token_type' => $this->token_type,
            'expires_in' => $this->expires_in,
        ];
    }
}
