<?php

declare(strict_types=1);

namespace Shared\Services\Contracts;

use Shared\DTO\BaseHeadersExternalDTO;
use Shared\DTO\BaseURLParamsExternalDTO;
use Shared\Enums\AuthTokenType;

interface BaseExternalServiceContract
{
    public function setAuthorization(string $token, AuthTokenType $tokenType = AuthTokenType::BEARER): self;

    public function setAccept(string $value): self;

    public function setHeaders(BaseHeadersExternalDTO $headers): self;

    public function setURLParams(BaseURLParamsExternalDTO $urlParams): self;

    public function setURLResourceParam(string $resource): self;

    /**
     * Get headers corrected for http request
     *
     * @return array<string, string>
     */
    public function getBaseHeaders(): array;
}
