<?php

declare(strict_types=1);

namespace Presentation\Analys\Controllers;

use Application\Analys\Services\Contracts\UserAnalysServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\BaseController;
use Presentation\Analys\Requests\CreateUserAnalysisRequest;
use Presentation\Analys\Requests\IndexUserAnalysisRequest;
use Presentation\Analys\Resources\UserAnalysResource;

class UserAnalysController extends BaseController
{
    public function __construct(
        protected readonly UserAnalysServiceContract $userAnalysService,
    ) {}

    public function create(CreateUserAnalysisRequest $request): JsonResponse
    {
        $dto = $request->getDTO();

        $userAnalysis = $this->userAnalysService->createUserAnalysis($dto);

        return Response::json(UserAnalysResource::collection($userAnalysis), 201);
    }

    public function index(IndexUserAnalysisRequest $request): JsonResponse
    {
        $filters = $request->getDTO();

        $userAnalysis = $this->userAnalysService->getUserAnalysis($filters);

        return Response::json(UserAnalysResource::collection($userAnalysis));
    }
}
