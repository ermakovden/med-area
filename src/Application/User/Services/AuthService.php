<?php

declare(strict_types=1);

namespace Application\User\Services;

use Application\User\DTO\TokenResponse;
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
     * @return TokenResponse
     *
     * @throws BadRequestHttpException
     */
    public function login(UserDTO $credentials): TokenResponse
    {
        if (! $accessToken = auth()->attempt($credentials->toArray())) {
            throw new BadRequestHttpException('Nickname or password incorrect.');
        }

        return $this->tokenResponse($accessToken);
    }

    /**
     * Refresh token for authenticated user
     *
     * @param boolean $forceForever
     * @param boolean $resetClaims
     * @return TokenResponse
     *
     * @throws ServerErrorException
     */
    public function refreshToken(bool $forceForever = false, bool $resetClaims = false): TokenResponse
    {
        if (! $accessToken = auth()->refresh($forceForever, $resetClaims)) {
            throw new ServerErrorException();
        }

        return $this->tokenResponse($accessToken);
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
     * @return TokenResponse
     */
    protected function tokenResponse(string $accessToken): TokenResponse
    {
        return TokenResponse::from([
            'access_token' => $accessToken,
            'token_type' => TokenType::BEARER,
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }
}
