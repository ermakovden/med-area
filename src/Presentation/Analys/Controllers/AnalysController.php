<?php

declare(strict_types=1);

namespace Presentation\Analys\Controllers;

use Application\Analys\Services\Contracts\AnalysServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Presentation\Analys\Resources\AnalysResource;
use Presentation\BaseController;

class AnalysController extends BaseController
{
    public function __construct(
        protected readonly AnalysServiceContract $analysService,
    ) {}

    public function index(): JsonResponse
    {
        $analysis = $this->analysService->getAnalysis();

        return Response::json(AnalysResource::collection($analysis));
    }
}
