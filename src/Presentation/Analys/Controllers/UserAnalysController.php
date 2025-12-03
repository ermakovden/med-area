<?php

declare(strict_types=1);

namespace Presentation\Analys\Controllers;

use Application\Analys\Services\Contracts\UserAnalysServiceContract;
use Domain\Analys\Enums\Analys;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use Presentation\Analys\Requests\CreateUserAnalysisRequest;
use Presentation\Analys\Requests\DeleteUserAnalysisRequest;
use Presentation\Analys\Requests\IndexUserAnalysisRequest;
use OpenApi\Attributes as OA;
use Presentation\Analys\Resources\UserAnalysResourceCollection;

class UserAnalysController extends BaseController
{
    public function __construct(
        protected readonly UserAnalysServiceContract $userAnalysService,
    ) {}

    #[OA\Post(
        path: '/api/users/{userId}/analysis',
        operationId: 'apiUsersAnalysisCreate',
        description: 'Add analysis for userId.',
        tags: ['analys', 'api'],
        requestBody: new OA\RequestBody(ref: CreateUserAnalysisRequest::class),
        parameters: [
            new OA\Parameter(name: 'userId', in: 'path', description: 'user id', required: true, schema: new OA\Schema(type: 'string')),
        ]
    )]
    #[OA\Response(
        response: 201,
        description: 'Analysis for user added.',
        content: new OA\JsonContent(ref: UserAnalysResourceCollection::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized.',
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden.',
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error.',
    )]
    public function create(CreateUserAnalysisRequest $request): JsonResponse
    {
        $dto = $request->getDTO();

        $userAnalysis = $this->userAnalysService->createUserAnalysis($dto);

        return Response::json(new UserAnalysResourceCollection($userAnalysis), 201);
    }

    #[OA\Get(
        path: '/api/users/{userId}/analysis',
        operationId: 'apiUsersAnalysisIndex',
        description: 'Get analysis by filters.',
        tags: ['analys', 'api'],
        parameters: [
            new OA\Parameter(name: 'userId', in: 'path', description: 'user id', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(
                name: 'user_ids[]',
                in: 'query',
                description: 'Array of user IDs',
                required: true,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                ),
            ),
            new OA\Parameter(
                name: 'analys_ids[]',
                in: 'query',
                description: 'Array of analys IDs',
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'integer', enum: Analys::class),
                ),
            ),
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Analysis of users.',
        content: new OA\JsonContent(ref: UserAnalysResourceCollection::class)
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized.',
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden.',
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error.',
    )]
    public function index(IndexUserAnalysisRequest $request): JsonResponse
    {
        $filters = $request->getDTO();

        $userAnalysis = $this->userAnalysService->getUserAnalysis($filters);

        return Response::json(new UserAnalysResourceCollection($userAnalysis));
    }

    #[OA\Delete(
        path: '/api/users/{userId}/analysis',
        operationId: 'apiUsersAnalysisDestroy',
        description: 'Delete analysis by filters.',
        tags: ['analys', 'api'],
        parameters: [
            new OA\Parameter(name: 'userId', in: 'path', description: 'user id', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(
                name: 'user_ids[]',
                in: 'query',
                description: 'Array of user IDs',
                required: true,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                ),
            ),
            new OA\Parameter(
                name: 'analys_ids[]',
                in: 'query',
                description: 'Array of analys IDs',
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'integer', enum: Analys::class),
                ),
            ),
        ]
    )]
    #[OA\Response(
        response: 204,
        description: 'Analysis deleted.',
    )]
    #[OA\Response(
        response: 401,
        description: 'Unauthorized.',
    )]
    #[OA\Response(
        response: 403,
        description: 'Forbidden.',
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error.',
    )]
    public function destroy(DeleteUserAnalysisRequest $request): JsonResponse
    {
        $filters = $request->getDTO();

        $this->userAnalysService->deleteUserAnalysis($filters);

        return Response::json(status: 204);
    }
}
