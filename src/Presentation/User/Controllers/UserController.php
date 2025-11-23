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

    #[OA\Get(
        path: '/api/users/me',
        operationId: 'apiUsersMe',
        description: 'Get data of current user.',
        tags: ['user', 'api'],
    )]
    #[OA\Response(
        response: 200,
        description: 'User data.',
        content: new OA\JsonContent(ref: UserResource::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorizied.',
    )]
    public function me(Request $request): JsonResponse
    {
        $user = $this->userService->me();

        return Response::json(new UserResource($user));
    }

    #[OA\Get(
        path: '/api/users/{id}',
        operationId: 'apiUsersShow',
        description: 'Get user data by id.',
        tags: ['user', 'api'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', description: 'user id', required: true, schema: new OA\Schema(type: 'string')),
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'User data.',
        content: new OA\JsonContent(ref: UserResource::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorizied.',
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden.',
    )]
    #[OA\Response(
        response: 404,
        description: 'Not found.',
    )]
    public function show(ShowUserRequest $request): JsonResponse
    {
        $user = $this->userService->getById($request->getUserId());

        return Response::json(new UserResource($user));
    }
}
