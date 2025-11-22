<?php

declare(strict_types=1);

namespace Application\User\DTO;

use Domain\User\Enums\TokenType;
use Shared\DTO\BaseDTO;
use Spatie\LaravelData\Optional;

class TokensResponse extends BaseDTO
{
    public string|Optional $access_token;

    public string|Optional $refresh_token;

    public TokenType|Optional $token_type;

    public int|Optional $expires_in;
}
