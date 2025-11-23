<?php

declare(strict_types=1);

namespace Presentation\User\Controllers;

use Application\User\Services\Contracts\UserServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use OpenApi\Attributes as OA;
use Presentation\User\Requests\ShowUserRequest;
use Presentation\User\Resources\UserResource;

/**
 * Controller for users
 */
class UserController extends BaseController
{
    public function __construct(
        protected readonly UserServiceContract $userService,
    ) {}

    public function me(Request $request): JsonResponse
    {
        $user = $this->userService->me();

        return Response::json(new UserResource($user));
    }

    public function show(ShowUserRequest $request): JsonResponse
    {
        $user = $this->userService->getById($request->getUserId());

        return Response::json(new UserResource($user));
    }
}
