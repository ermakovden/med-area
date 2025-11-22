<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\DTO\TokensResponse;
use Application\User\DTO\UserDTO;
use Application\User\Services\Contracts\AuthServiceContract;
use Domain\User\Enums\TokenType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthService implements AuthServiceContract
{
    /**
     * Attemt to login user by credentials
     *
     * @param UserDTO $credentials
     * @return TokensResponse
     *
     * @throws BadRequestException
     */
    public function login(UserDTO $credentials): TokensResponse
    {
        if (! $accessToken = auth()->attempt($credentials->toArray())) {
            throw new BadRequestHttpException('Nickname or password incorrect.');
        }

        $refreshToken = auth()->refresh();

        return $this->tokensResponse($accessToken, $refreshToken);
    }

    /**
     * Construct response with tokens
     *
     * @param string $accessToken
     * @param string $refreshToken
     * @return TokensResponse
     */
    protected function tokensResponse(string $accessToken, string $refreshToken = ''): TokensResponse
    {
        return TokensResponse::from([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => TokenType::BEARER,
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
