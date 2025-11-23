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

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = $request->getDTO();

        $tokenResponse = $this->authService->login($dto);

        return Response::json(new TokenResource($tokenResponse));
    }
}
