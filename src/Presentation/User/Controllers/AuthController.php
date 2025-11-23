<?php

declare(strict_types=1);

namespace Presentation\User\Controllers;

use Application\User\Services\Contracts\AuthServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use OpenApi\Attributes as OA;
use Presentation\User\Requests\LoginRequest;
use Presentation\User\Resources\TokenResource;

/**
 * Controller responsible for authenticate of users
 */
class AuthController extends BaseController
{
    public function __construct(
        protected readonly AuthServiceContract $authService,
    ) {}

    #[OA\Post(
        path: '/api/auth/login',
        operationId: 'apiAuthLogin',
        description: 'Authorization of user.',
        tags: ['auth', 'api'],
        requestBody: new OA\RequestBody(ref: LoginRequest::class),
    )]
    #[OA\Response(
        response: 200,
        description: 'Success.',
        content: new OA\JsonContent(ref: TokenResource::class)
    )]
    #[OA\Response(
        response: 400,
        description: 'Bad request. Nickname or password incorrect.',
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error.',
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = $request->getDTO();

        $tokenResponse = $this->authService->login($dto);

        return Response::json(new TokenResource($tokenResponse));
    }

    #[OA\Post(
        path: '/api/auth/refresh',
        operationId: 'apiAuthRefresh',
        description: 'Refresh token for authenticated user.',
        tags: ['auth', 'api'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Success.',
        content: new OA\JsonContent(ref: TokenResource::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorizied.',
    )]
    #[OA\Response(
        response: 500,
        description: 'Internal server error.',
    )]
    public function refresh(): JsonResponse
    {
        $tokenResponse = $this->authService->refreshToken();

        return Response::json(new TokenResource($tokenResponse));
    }

    #[OA\Post(
        path: '/api/auth/logout',
        operationId: 'apiAuthLogout',
        description: 'Logout for authenticated user.',
        tags: ['auth', 'api'],
    )]
    #[OA\Response(
        response: 204,
        description: 'Success. No content.',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorizied.',
    )]
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return Response::json(status: 204);
    }
}
