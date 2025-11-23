<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\DTO\TokensResponse;
use Application\User\DTO\UserDTO;
use Application\User\Services\Contracts\AuthServiceContract;
use Domain\User\Enums\TokenType;
use Shared\Exceptions\ServerErrorException;
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

        return $this->tokensResponse($accessToken);
    }

    /**
     * Refresh token for authenticated user
     *
     * @param boolean $forceForever
     * @param boolean $resetClaims
     * @return TokensResponse
     *
     * @throws ServerErrorException
     */
    public function refreshToken(bool $forceForever = false, bool $resetClaims = false): TokensResponse
    {
        if (! $accessToken = auth()->refresh($forceForever, $resetClaims)) {
            throw new ServerErrorException();
        }

        return $this->tokensResponse($accessToken);
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return void
     */
    public function logout(): void
    {
        auth()->logout();
    }


    /**
     * Construct response with tokens
     *
     * @param string $accessToken
     * @return TokensResponse
     */
    protected function tokensResponse(string $accessToken): TokensResponse
    {
        return TokensResponse::from([
            'access_token' => $accessToken,
            'token_type' => TokenType::BEARER,
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
