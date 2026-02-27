<?php

declare(strict_types=1);

namespace Shared\Services;

use Shared\DTO\BaseHeadersExternalDTO;
use Shared\DTO\BaseURLParamsExternalDTO;
use Shared\Enums\AuthTokenType;
use Shared\Services\Contracts\BaseExternalServiceContract;

abstract class BaseExternalService implements BaseExternalServiceContract
{
    private BaseHeadersExternalDTO $headers;

    protected BaseURLParamsExternalDTO $urlParams;

    public function __construct()
    {
        $this->setHeaders(BaseHeadersExternalDTO::from());
        $this->setURLParams(BaseURLParamsExternalDTO::from());
    }

    abstract public function getURITemplate(): string;

    public function setAuthorization(string $token, AuthTokenType $tokenType = AuthTokenType::BEARER): self
    {
        $this->headers->authorization = $token;

        if (in_array($tokenType, [AuthTokenType::BEARER, AuthTokenType::API_KEY])) {
            $this->headers->authorization = $tokenType->value . ' ' . $token;
        }

        return $this;
    }

    public function setAccept(string $value): self
    {
        $this->headers->accept = $value;

        return $this;
    }

    public function setHeaders(BaseHeadersExternalDTO $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function setURLParams(BaseURLParamsExternalDTO $urlParams): self
    {
        $this->urlParams = $urlParams;

        return $this;
    }

    public function setURLResourceParam(string $resource): self
    {
        $this->urlParams->resource = $resource;

        return $this;
    }

    /**
     * Get headers corrected for http request
     *
     * @return array<string, string>
     */
    public function getBaseHeaders(): array
    {
        $response = [];

        /** @phpstan-ignore foreach.nonIterable */
        foreach ($this->headers as $key => $value) {
            if ($value !== null) {
                $response[ucfirst($key)] = $value;
            }
        }

        return $response;
    }
}
